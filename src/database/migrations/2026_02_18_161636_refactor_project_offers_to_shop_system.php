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
        // 1. Create project_skill pivot table
        Schema::create('project_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['project_id', 'skill_id']);
        });

        // 2. Add images and extra_info to project_offers
        Schema::table('project_offers', function (Blueprint $table) {
            $table->json('images')->nullable()->after('description');
            $table->json('extra_info')->nullable()->after('images');
        });
        
        // 3. Migrate existing skills from offers to projects (optional but good practice)
        try {
            $offerSkills = \DB::table('project_offer_skill')
                ->join('project_offers', 'project_offer_skill.project_offer_id', '=', 'project_offers.id')
                ->select('project_offers.project_id', 'project_offer_skill.skill_id')
                ->distinct()
                ->get();

            foreach ($offerSkills as $os) {
                \DB::table('project_skill')->insertOrIgnore([
                    'project_id' => $os->project_id,
                    'skill_id' => $os->skill_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Silently skip if table doesn't exist yet or other issues
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_skill');
        
        Schema::table('project_offers', function (Blueprint $table) {
            $table->dropColumn(['images', 'extra_info']);
        });
    }
};
