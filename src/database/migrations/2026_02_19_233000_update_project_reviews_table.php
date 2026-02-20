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
        Schema::table('project_reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->default(5)->after('type');
            $table->foreignId('project_offer_id')->nullable()->after('project_id')->constrained()->onDelete('cascade');
            
            $table->index('project_offer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_reviews', function (Blueprint $table) {
            $table->dropForeign(['project_offer_id']);
            $table->dropColumn(['rating', 'project_offer_id']);
        });
    }
};
