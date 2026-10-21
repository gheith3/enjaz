<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('method');
            $table->string('tool')->nullable();
            $table->json('arguments')->nullable();
            $table->boolean('was_error');
            $table->string('error_message', 500)->nullable();
            $table->unsignedInteger('duration_ms');
            $table->string('ip', 45)->nullable();
            $table->timestamp('occurred_at')->index();

            $table->index(['user_id', 'occurred_at']);
            $table->index(['tool', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_usages');
    }
};
