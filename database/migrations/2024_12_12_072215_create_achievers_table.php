<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAchieversTable extends Migration
{
    public function up()
    {
        Schema::create('achievers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('english_name')->nullable();
            $table->string('title')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('achievers');
    }
}
