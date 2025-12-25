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

        $record = RageAnalysis::create([
            'customer_message' => $payload['customer_message'],
            'support_draft' => null,
            'rage_level' => $analysis['rage_level'],
            'rewritten_reply' => $analysis['rewritten_reply'],
            'ai_reply' => $analysis['rewritten_reply'],
            'user_reply' => null,
            'language' => $analysis['language'] ?? null,
            'emotions' => $analysis['emotions'] ?? null,
        ]);

        return response()->json([
            'id' => $record->id,
            'rage_level' => $record->rage_level,
            'rewritten_reply' => $record->rewritten_reply,
            'ai_reply' => $record->ai_reply,
            'user_reply' => $record->user_reply,
            'language' => $record->language,
            'emotions' => $record->emotions,
            'created_at' => $record->created_at,
        ], 201);
    }

    /**
     * Persist a chosen response.
     */
    public function save(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'customer_message' => ['required', 'string'],
            'rage_level' => ['required', 'integer', 'min:0', 'max:100'],
            'ai_reply' => ['required', 'string'],
            'user_reply' => ['required', 'string'],
            'rewritten_reply' => ['nullable', 'string'], // backward compatibility
            'support_draft' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:32'],
            'emotions' => ['nullable', 'array'],
        ]);

        $record = RageAnalysis::create([
            'customer_message' => $payload['customer_message'],
            'support_draft' => $payload['support_draft'] ?? null,
            'rage_level' => $payload['rage_level'],
            'rewritten_reply' => $payload['rewritten_reply'] ?? $payload['ai_reply'],
            'ai_reply' => $payload['ai_reply'],
            'user_reply' => $payload['user_reply'],
            'language' => $payload['language'] ?? null,
            'emotions' => $payload['emotions'] ?? null,
        ]);

        return response()->json([
            'id' => $record->id,
            'rage_level' => $record->rage_level,
            'rewritten_reply' => $record->rewritten_reply,
            'ai_reply' => $record->ai_reply,
            'user_reply' => $record->user_reply,
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
                'ai_reply',
                'user_reply',
                'rewritten_reply',
                'language',
                'emotions',
                'created_at',
            ]);

        return response()->json($items);
    }
}

