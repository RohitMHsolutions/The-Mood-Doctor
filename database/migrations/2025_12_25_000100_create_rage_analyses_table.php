<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rage_analyses', function (Blueprint $table): void {
            $table->id();
            $table->text('customer_message');
            $table->text('support_draft')->nullable();
            $table->unsignedTinyInteger('rage_level');
            $table->text('rewritten_reply');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rage_analyses');
    }
};

