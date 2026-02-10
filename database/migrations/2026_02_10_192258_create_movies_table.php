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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('original_language');
            $table->string('original_title');
            $table->float('popularity')->nullable();
            $table->string('poster_path')->nullable();
            $table->date('release_date')->nullable();
            $table->boolean('video')->default(false);
            $table->float('vote_average')->nullable();
            $table->integer('vote_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
