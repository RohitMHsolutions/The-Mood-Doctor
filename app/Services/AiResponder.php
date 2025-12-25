<?php

namespace App\Services;

class AiResponder
{
    /**
     * Analyze an angry customer message and return a rage level plus a softened reply.
     */
    public static function analyze(string $customerMessage, string $supportDraft): array
    {
        $rageLevel = self::estimateRageLevel($customerMessage);
        $rewrittenReply = self::rewriteDraft($supportDraft, $customerMessage, $rageLevel);

        return [
            'rage_level' => $rageLevel,
            'rewritten_reply' => $rewrittenReply,
        ];
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

    /**
     * Gentle rewrite that acknowledges the pain and promises concrete next steps.
     */
    protected static function rewriteDraft(string $supportDraft, string $customerMessage, int $rageLevel): string
    {
        $tone = $rageLevel > 70
            ? "I understand this has been very frustrating."
            : ($rageLevel > 40
                ? "I'm sorry for the hassle and frustration."
                : "Thanks for letting us know about this.");

        $assurance = "I'm here to help resolve this quickly.";
        $actionLine = "Based on what you shared, here is what I will do next: " . trim($supportDraft);

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

