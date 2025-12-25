<?php

namespace App\Services;

use App\Services\TheModdDoctor;

class AiResponder
{
    /**
     * Analyze a customer email and return rage intensity plus an empathetic reply.
     */
    public static function analyzeMessage(string $customerMessage): array
    {
        $aiResult = self::tryAi($customerMessage);
        if ($aiResult !== null) {
            return $aiResult;
        }

        $rageLevel = self::estimateRageLevel($customerMessage);
        $rewrittenReply = self::craftReply($customerMessage, $rageLevel);

        return [
            'rage_level' => $rageLevel,
            'rewritten_reply' => $rewrittenReply,
            'language' => 'unknown',
            'emotions' => [
                'rage' => $rageLevel,
            ],
        ];
    }

    /**
     * Attempt real AI; return null on failure to fall back.
     */
    protected static function tryAi(string $customerMessage): ?array
    {
        try {
            $doctor = app(TheModdDoctor::class);
            $result = $doctor->analyzeEmailEmotion($customerMessage);

            // If the AI call failed or returned an error, fall back.
            if (empty($result) || isset($result['error'])) {
                return null;
            }

            $emotions = $result['emotions'] ?? [];
            $rage = isset($emotions['rage']) ? (int) $emotions['rage'] : (int) ($result['rage_score'] ?? 0);
            $rage = max(0, min(100, $rage));
            $reply = (string) ($result['empathetic_response'] ?? '');

            // Require at least a reply; if missing, fall back to heuristic.
            if ($reply === '') {
                return null;
            }

            return [
                'rage_level' => $rage,
                'rewritten_reply' => $reply,
                'language' => $result['language'] ?? '',
                'emotions' => $emotions,
            ];
        } catch (\Throwable $e) {
            // swallow and fall back to heuristic
        }

        return null;
    }

    /**
     * Rough heuristic to keep the API usable while the real AI hook is wired up.
     */
    protected static function estimateRageLevel(string $customerMessage): int
    {
        $score = 20; // base tension for negative reports

        $score += min(substr_count($customerMessage, '!') * 5, 25);
        $score += min(substr_count($customerMessage, '?') * 2, 10);

        preg_match_all('/\b[A-Z]{3,}\b/', $customerMessage, $matches);
        $score += min(count($matches[0]) * 5, 20);

        $keywords = ['angry', 'furious', 'unacceptable', 'ridiculous', 'cancel', 'refund', 'outrage', 'terrible'];
        foreach ($keywords as $keyword) {
            if (stripos($customerMessage, $keyword) !== false) {
                $score += 8;
            }
        }

        return max(0, min(100, $score));
    }

    protected static function craftReply(string $customerMessage, int $rageLevel): string
    {
        $tone = $rageLevel > 70
            ? "I understand this has been very frustrating."
            : ($rageLevel > 40
                ? "I'm sorry for the hassle and frustration."
                : "Thanks for letting us know about this.");

        $assurance = "I'm here to help resolve this quickly.";
        $actionLine = "I'll review your account details now and update you with a concrete fix.";

        $summary = trim($customerMessage);
        $summary = mb_substr($summary, 0, 240);
        if (mb_strlen($customerMessage) > 240) {
            $summary .= "...";
        }

        return implode(' ', [
            $tone,
            "I appreciate you taking the time to explain: \"{$summary}\"",
            $assurance,
            $actionLine,
            "If I miss anything, please tell me - I want to make this right."
        ]);
    }
}

