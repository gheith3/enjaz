<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit log of the MCP endpoint: one row per JSON-RPC message (docs/mcp.md).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('method');
            $table->string('tool')->nullable();
            $table->jsonb('arguments')->nullable();
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
