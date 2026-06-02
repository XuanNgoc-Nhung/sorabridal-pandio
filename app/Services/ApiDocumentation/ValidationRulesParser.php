<?php

namespace App\Services\ApiDocumentation;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use ReflectionClass;
use ReflectionMethod;

class ValidationRulesParser
{
    /** @var array<string, array<string, string>>|null */
    private static ?array $traitRulesCache = null;

    /**
     * @return array{query: array<int, array<string, mixed>>, body: array<int, array<string, mixed>>}
     */
    public function parse(string $controllerClass, string $methodName, string $httpMethod): array
    {
        if (! class_exists($controllerClass) || ! method_exists($controllerClass, $methodName)) {
            return ['query' => [], 'body' => []];
        }

        $reflection = new ReflectionMethod($controllerClass, $methodName);
        $source = $this->methodSource($reflection);
        $rules = $this->extractRulesFromSource($source, $controllerClass);

        $httpMethod = strtoupper($httpMethod);
        $in = in_array($httpMethod, ['GET', 'HEAD', 'DELETE'], true) ? 'query' : 'body';

        return [
            'query' => $in === 'query' ? $this->rulesToParameters($rules, $in) : [],
            'body' => $in === 'body' ? $this->rulesToParameters($rules, $in) : [],
        ];
    }

    private function methodSource(ReflectionMethod $reflection): string
    {
        $filename = $reflection->getFileName();
        if ($filename === false) {
            return '';
        }

        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return '';
        }

        $start = $reflection->getStartLine() - 1;
        $end = $reflection->getEndLine() - 1;

        return implode("\n", array_slice($lines, $start, $end - $start + 1));
    }

    /**
     * @return array<string, string>
     */
    private function extractRulesFromSource(string $source, string $controllerClass): array
    {
        $rules = [];

        $offset = 0;
        while (($pos = strpos($source, '$request->validate(', $offset)) !== false) {
            $open = $pos + strlen('$request->validate(');
            $argument = $this->readBalancedParenthesisContent($source, $open);
            if ($argument !== null) {
                $rules = array_merge($rules, $this->resolveValidateArgument(trim($argument), $controllerClass));
            }

            $semi = strpos($source, ';', $open);
            $offset = $semi !== false ? $semi + 1 : $open + 1;
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function resolveValidateArgument(string $argument, string $controllerClass): array
    {
        if (str_starts_with($argument, 'array_merge(')) {
            $inner = substr($argument, strlen('array_merge('));
            $inner = $this->stripOuterParens($inner);

            return $this->mergeArgumentParts($inner, $controllerClass);
        }

        if (str_starts_with($argument, '[')) {
            return $this->parseInlineRulesArray($argument);
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    private function mergeArgumentParts(string $inner, string $controllerClass): array
    {
        $parts = $this->splitTopLevelComma($inner);
        $rules = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (preg_match('/^\$this->(\w+)\(\)$/', $part, $match)) {
                $rules = array_merge($rules, $this->resolveTraitHelper($controllerClass, $match[1]));

                continue;
            }

            if (str_starts_with($part, '[')) {
                $rules = array_merge($rules, $this->parseInlineRulesArray($part));
            }
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function resolveTraitHelper(string $controllerClass, string $method): array
    {
        $known = $this->traitRuleSets();

        if (isset($known[$method])) {
            return $known[$method];
        }

        if (! method_exists($controllerClass, $method)) {
            return [];
        }

        $reflection = new ReflectionMethod($controllerClass, $method);
        if (! $reflection->isPublic() || $reflection->getNumberOfRequiredParameters() > 0) {
            return [];
        }

        $instance = (new ReflectionClass($controllerClass))->newInstanceWithoutConstructor();

        /** @var array<string, string> $result */
        $result = $instance->{$method}();

        return $result;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function traitRuleSets(): array
    {
        if (self::$traitRulesCache !== null) {
            return self::$traitRulesCache;
        }

        $stub = new class
        {
            use RespondsWithJson;

            /** @return array<string, string> */
            public function exposePaginationRules(): array
            {
                return $this->paginationRules();
            }

            /** @return array<string, string> */
            public function exposeTuKhoaRules(): array
            {
                return $this->tuKhoaRules();
            }
        };

        self::$traitRulesCache = [
            'paginationRules' => $stub->exposePaginationRules(),
            'tuKhoaRules' => $stub->exposeTuKhoaRules(),
        ];

        return self::$traitRulesCache;
    }

    /**
     * @return array<string, string>
     */
    private function parseInlineRulesArray(string $arraySource): array
    {
        $rules = [];

        if (! preg_match_all(
            "/['\"]([a-zA-Z0-9_]+)['\"]\s*=>\s*((?:'[^']*'|\"[^\"]*\"|\[[^\]]*\]|Rule::[^\n,]+)+)/",
            $arraySource,
            $matches,
            PREG_SET_ORDER
        )) {
            return $rules;
        }

        foreach ($matches as $match) {
            $rules[$match[1]] = $this->normalizeRuleExpression($match[2]);
        }

        return $rules;
    }

    private function normalizeRuleExpression(string $expression): string
    {
        $expression = trim($expression);

        if (str_starts_with($expression, '[') && str_ends_with($expression, ']')) {
            $inner = trim($expression, '[]');
            $parts = $this->splitTopLevelComma($inner);
            $normalized = [];

            foreach ($parts as $part) {
                $part = trim($part);
                $part = trim($part, "'\"");
                if ($part !== '') {
                    $normalized[] = $part;
                }
            }

            return implode('|', $normalized);
        }

        return trim($expression, "'\"");
    }

    /**
     * @param  array<string, string>  $rules
     * @return array<int, array<string, mixed>>
     */
    private function rulesToParameters(array $rules, string $location): array
    {
        $parameters = [];

        foreach ($rules as $name => $ruleString) {
            $parameters[] = [
                'name' => $name,
                'rules' => $ruleString,
                'required' => $this->isRequired($ruleString),
                'type' => $this->inferType($ruleString),
                'in' => str_contains($ruleString, 'image') ? 'form-data' : $location,
            ];
        }

        return $parameters;
    }

    private function isRequired(string $rules): bool
    {
        return preg_match('/\brequired\b/', $rules) === 1
            && preg_match('/\brequired_if\b/', $rules) !== 1;
    }

    private function inferType(string $rules): string
    {
        if (str_contains($rules, 'image')) {
            return 'file';
        }
        if (preg_match('/\binteger\b/', $rules)) {
            return 'integer';
        }
        if (preg_match('/\bnumeric\b/', $rules)) {
            return 'number';
        }
        if (preg_match('/\bboolean\b/', $rules)) {
            return 'boolean';
        }
        if (preg_match('/\barray\b/', $rules)) {
            return 'array';
        }

        return 'string';
    }

    /**
     * @return array<int, string>
     */
    private function splitTopLevelComma(string $value): array
    {
        $parts = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $stringChar = '';

        $length = strlen($value);
        for ($i = 0; $i < $length; $i++) {
            $char = $value[$i];

            if ($inString) {
                $current .= $char;
                if ($char === $stringChar && ($i === 0 || $value[$i - 1] !== '\\')) {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'" || $char === '"') {
                $inString = true;
                $stringChar = $char;
                $current .= $char;

                continue;
            }

            if ($char === '(' || $char === '[') {
                $depth++;
                $current .= $char;

                continue;
            }

            if ($char === ')' || $char === ']') {
                $depth--;
                $current .= $char;

                continue;
            }

            if ($char === ',' && $depth === 0) {
                $parts[] = $current;
                $current = '';

                continue;
            }

            $current .= $char;
        }

        if ($current !== '') {
            $parts[] = $current;
        }

        return $parts;
    }

    private function readBalancedParenthesisContent(string $source, int $start): ?string
    {
        $length = strlen($source);
        if ($start >= $length || $source[$start - 1] !== '(') {
            return null;
        }

        $depth = 0;
        $inString = false;
        $stringChar = '';

        for ($i = $start - 1; $i < $length; $i++) {
            $char = $source[$i];

            if ($inString) {
                if ($char === $stringChar && ($i === 0 || $source[$i - 1] !== '\\')) {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'" || $char === '"') {
                $inString = true;
                $stringChar = $char;

                continue;
            }

            if ($char === '(') {
                $depth++;

                continue;
            }

            if ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    return substr($source, $start, $i - $start);
                }
            }
        }

        return null;
    }

    private function stripOuterParens(string $value): string
    {
        $value = trim($value);
        while (str_ends_with($value, ')')) {
            $value = rtrim($value);
            $value = substr($value, 0, -1);
            $value = rtrim($value);
        }

        return $value;
    }

    public function methodSummary(string $controllerClass, string $methodName): ?string
    {
        if (! class_exists($controllerClass) || ! method_exists($controllerClass, $methodName)) {
            return null;
        }

        $doc = (new ReflectionMethod($controllerClass, $methodName))->getDocComment();
        if ($doc === false) {
            return null;
        }

        $lines = preg_split('/\R/', $doc) ?: [];
        $summary = [];

        foreach ($lines as $line) {
            $line = trim($line, " \t*\r\n");
            if ($line === '' || str_starts_with($line, '@')) {
                continue;
            }
            $summary[] = $line;
        }

        return $summary === [] ? null : implode(' ', $summary);
    }
}
