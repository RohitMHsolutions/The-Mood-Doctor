<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rage_analyses', function (Blueprint $table): void {
            $table->text('ai_reply')->nullable()->after('rage_level');
            $table->text('user_reply')->nullable()->after('ai_reply');
        });
    }

    public function down(): void
    {
        Schema::table('rage_analyses', function (Blueprint $table): void {
            $table->dropColumn(['ai_reply', 'user_reply']);
        });
    }
};

