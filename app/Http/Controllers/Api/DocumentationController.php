<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiDocumentation\ApiDocumentationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentationController extends Controller
{
    public function __construct(
        private readonly ApiDocumentationService $documentation,
    ) {}

    /**
     * GET /api/documents
     * Tài liệu API tự động (HTML hoặc JSON).
     */
    public function index(Request $request): View|JsonResponse
    {
        $docs = $this->documentation->build();

        if ($request->expectsJson() || $request->query('format') === 'json') {
            return response()->json([
                'success' => true,
                'data' => $docs,
            ]);
        }

        return view('api.documentation', ['docs' => $docs]);
    }
}
