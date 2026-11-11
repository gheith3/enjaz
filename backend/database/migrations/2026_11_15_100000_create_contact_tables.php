<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Phase 5d: the contact form (contact.html): topics of the form and the messages (dashboard inbox). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_topics', function (Blueprint $table) {
            $table->id();
            $table->jsonb('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_topic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('topic')->nullable();                 // the topic name when sent (kept if the topic is renamed)
            $table->string('name', 120);
            $table->string('email', 200);
            $table->string('phone', 30)->nullable();
            $table->text('message');
            $table->string('attachment_path')->nullable();     // private disk
            $table->string('attachment_name')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('contact_topics');
    }
};
