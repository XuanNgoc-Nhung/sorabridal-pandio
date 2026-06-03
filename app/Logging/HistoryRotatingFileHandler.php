<?php

namespace App\Logging;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;

class HistoryRotatingFileHandler extends RotatingFileHandler
{
    public function __construct(
        string $filename,
        int $maxFiles = 0,
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
        ?int $filePermission = null,
        bool $useLocking = false,
    ) {
        parent::__construct(
            $filename,
            $maxFiles,
            $level,
            $bubble,
            $filePermission,
            $useLocking,
            'd-m-Y',
            '{filename}-{date}',
        );
    }

    protected function setDateFormat(string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }
}
