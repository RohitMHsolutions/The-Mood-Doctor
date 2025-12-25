<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rage_analyses', function (Blueprint $table): void {
            $table->string('language', 32)->nullable()->after('user_reply');
            $table->json('emotions')->nullable()->after('language');
        });
    }

    public function down(): void
    {
        Schema::table('rage_analyses', function (Blueprint $table): void {
            $table->dropColumn(['language', 'emotions']);
        });
    }
};

