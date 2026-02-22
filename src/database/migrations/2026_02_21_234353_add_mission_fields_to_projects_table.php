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
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('skill_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('actuelle'); // actuelle, verrouillée, terminée, annulée
            $table->timestamp('realized_at')->nullable();
            $table->timestamp('locked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['skill_id']);
            $table->dropColumn(['skill_id', 'status', 'realized_at', 'locked_at']);
        });
    }
};
