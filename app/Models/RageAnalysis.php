<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RageAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_message',
        'support_draft',
        'rage_level',
        'rewritten_reply',
        'ai_reply',
        'user_reply',
    ];
}

