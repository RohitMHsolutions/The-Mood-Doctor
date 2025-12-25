<?php

namespace App\Http\Controllers;

use App\Models\RageAnalysis;
use App\Services\AiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RageAnalysisController extends Controller
{
    /**
     * Analyze a pasted customer email (no persistence).
     */
    public function analyze(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'customer_message' => ['required', 'string'],
        ]);

        $analysis = AiResponder::analyzeMessage($payload['customer_message']);

        return response()->json([
            'rage_level' => $analysis['rage_level'],
            'rewritten_reply' => $analysis['rewritten_reply'],
            'sample_json' => [
                'customer_message' => $payload['customer_message'],
                'rage_level' => $analysis['rage_level'],
                'rewritten_reply' => $analysis['rewritten_reply'],
            ],
        ]);
    }

    /**
     * Persist a chosen response.
     */
    public function save(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'customer_message' => ['required', 'string'],
            'rage_level' => ['required', 'integer', 'min:0', 'max:100'],
            'rewritten_reply' => ['required', 'string'],
            'support_draft' => ['nullable', 'string'],
        ]);

        $record = RageAnalysis::create([
            'customer_message' => $payload['customer_message'],
            'support_draft' => $payload['support_draft'] ?? null,
            'rage_level' => $payload['rage_level'],
            'rewritten_reply' => $payload['rewritten_reply'],
        ]);

        return response()->json([
            'id' => $record->id,
            'rage_level' => $record->rage_level,
            'rewritten_reply' => $record->rewritten_reply,
            'created_at' => $record->created_at,
        ], 201);
    }

    /**
     * Return recent history (latest 50).
     */
    public function history(): JsonResponse
    {
        $items = RageAnalysis::query()
            ->latest()
            ->limit(50)
            ->get([
                'id',
                'customer_message',
                'rage_level',
                'rewritten_reply',
                'created_at',
            ]);

        return response()->json($items);
    }
}

