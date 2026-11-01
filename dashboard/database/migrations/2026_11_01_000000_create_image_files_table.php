<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_files', function (Blueprint $table) {
            $table->string('path')->primary();
            $table->char('sha256', 64)->index();
            $table->unsignedBigInteger('bytes');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('mime');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_files');
    }
};
