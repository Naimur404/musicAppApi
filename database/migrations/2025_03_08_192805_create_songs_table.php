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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('singer_id');
            $table->unsignedBigInteger('genre_id');
            $table->string('file_path');
            $table->string('cover_image')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->timestamps();
            
            $table->foreign('singer_id')->references('id')->on('singers');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
