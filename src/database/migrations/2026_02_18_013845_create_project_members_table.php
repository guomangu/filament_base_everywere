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
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->morphs('memberable'); // memberable_type, memberable_id
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->enum('status', ['pending', 'active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['project_id', 'memberable_type', 'memberable_id'], 'project_member_unique');
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
