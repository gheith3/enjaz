<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Phase 4a: the lab (science.html, lab-all.html, lab-item.html): inventions and scientific publications.
 * Facts (بطاقة الابتكار) and sources use the shared morph tables. Pages get `extra` for the small texts of
 * each page design ({key: {ar, en}}, keys listed in config/site.php "labels").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_items', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 12);                         // invention | publication
            $table->string('slug')->unique();
            $table->jsonb('title');
            $table->jsonb('short')->nullable();                 // short title on cards
            $table->jsonb('lead')->nullable();                  // invention: first paragraph
            $table->jsonb('body')->nullable();                  // invention: rich text
            $table->jsonb('summary')->nullable();               // publication: abstract
            $table->jsonb('who')->nullable();                   // inventor, or the lead author of a publication
            $table->jsonb('authors')->nullable();               // publication: all authors
            $table->jsonb('journal')->nullable();
            $table->jsonb('pub_type')->nullable();              // بحث منشور ...
            $table->jsonb('field')->nullable();                 // المجال (filter on lab-all for publications)
            $table->jsonb('finding_value')->nullable();         // the big number on the card
            $table->jsonb('finding_text')->nullable();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->jsonb('org_label')->nullable();             // when the organization is not in the site
            $table->foreignId('achiever_id')->nullable()->constrained()->nullOnDelete();
            $table->string('medal', 10)->nullable();            // gold | silver | bronze
            $table->string('event', 40)->nullable();            // ITEX | Geneva ... (filter)
            $table->jsonb('event_label')->nullable();           // ITEX 2026
            $table->jsonb('place')->nullable();
            $table->date('happened_at')->nullable();
            $table->string('date_precision', 10)->default('month');
            $table->string('credit_text')->nullable();          // photos credit
            $table->string('credit_url', 2048)->nullable();
            $table->boolean('cover_is_illustrative')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['kind', 'is_published', 'happened_at']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->jsonb('extra')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_items');
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn('extra'));
    }
};
