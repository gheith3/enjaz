<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * مراجعة المنجز لصفحته: a private link (token) where an achiever confirms or corrects every point of their
 * profile and adds achievements; the team approves the answers in the dashboard and they go live.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achiever_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('sent')->index();
            $table->json('items');                  // snapshot of every point + the achiever's answer + the team's decision
            $table->json('additions')->nullable();  // achievements the achiever adds
            $table->json('journey')->nullable();    // {old: [{year, text}], new: [...], approved}: «محطات مسيرتك» (up to 10)
            $table->json('socials')->nullable();    // {old: {x, instagram, linkedin, facebook, snapchat}, new: {...}, approved}
            $table->json('gallery')->nullable();    // [{path, caption, approved}]: photos for «المكتبة المرئية»
            $table->json('videos')->nullable();     // {old: [{title, url}], new: [...], approved}: interviews
            $table->json('contact')->nullable();    // {old: {phone, email}, new: {...}, approved}: private, never published
            $table->text('note')->nullable();       // the achiever's general note
            $table->text('admin_note')->nullable(); // the team's note when the review is sent back
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_reviews');
    }
};
