<?php

namespace App\Http\Controllers;

use App\Models\RageAnalysis;
use App\Services\AiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RageAnalysisController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'customer_message' => ['required', 'string'],
            'support_draft' => ['required', 'string'],
        ]);

        $analysis = AiResponder::analyze(
            $payload['customer_message'],
            $payload['support_draft']
        );

        $record = RageAnalysis::create([
            'customer_message' => $payload['customer_message'],
            'support_draft' => $payload['support_draft'],
            'rage_level' => $analysis['rage_level'],
            'rewritten_reply' => $analysis['rewritten_reply'],
        ]);

        return response()->json([
            'id' => $record->id,
            'rage_level' => $record->rage_level,
            'rewritten_reply' => $record->rewritten_reply,
            'created_at' => $record->created_at,
        ], 201);
    }
}

