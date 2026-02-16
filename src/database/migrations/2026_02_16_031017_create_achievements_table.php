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
        Schema::disableForeignKeyConstraints();

        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('skill_id')->constrained();
            $table->foreignId('circle_id')->nullable()->constrained()->comment('Le');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('media_url')->nullable();
            $table->json('metadata')->nullable()->comment('Prix,');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
