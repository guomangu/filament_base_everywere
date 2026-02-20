<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Circle;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Achievement;
use App\Models\AchievementValidation;
use App\Models\Message;
use App\Models\ProjectReview;
use App\Models\Proche;
use App\Models\ProjectOffer;

class RealisticDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Clean Existing Data to Ensure a Consistent State
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Message::truncate();
        ProjectReview::truncate();
        DB::table('project_offer_skill')->truncate();
        ProjectOffer::truncate();
        DB::table('project_skill')->truncate();
        DB::table('project_members')->truncate();
        Project::truncate();
        DB::table('circle_members')->truncate();
        Circle::truncate();
        AchievementValidation::truncate();
        Achievement::truncate();
        Proche::truncate();
        // Don't truncate users entirely to avoid breaking admin accounts, but we delete the 4 injected users
        User::whereIn('email', [
            'marc@trustcircle.com', 
            'julie@trustcircle.com', 
            'thomas@trustcircle.com', 
            'sophie@trustcircle.com'
        ])->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Core Skills
        $skillsData = [
            'Architecture' => 'architecture',
            'Menuiserie' => 'menuiserie',
            'UI Design' => 'ui-design',
            'Développement Web' => 'developpement-web',
            'Permaculture' => 'permaculture',
            'Stratégie Digitale' => 'strategie-digitale',
            'Éco-conception' => 'eco-conception',
            'Photographie' => 'photographie'
        ];

        $skills = [];
        foreach ($skillsData as $name => $slug) {
            $skills[$name] = Skill::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // 2. Create the 4 Core Users
        $usersData = [
            ['id' => 'marc', 'name' => 'Marc Lefebvre', 'city' => 'Paris', 'lat' => 48.8566, 'lng' => 2.3522, 'role' => 'Tech Lead & Stratège'],
            ['id' => 'julie', 'name' => 'Julie Chen', 'city' => 'Paris', 'lat' => 48.8647, 'lng' => 2.3327, 'role' => 'UX/UI Designer'],
            ['id' => 'thomas', 'name' => 'Thomas Morel', 'city' => 'Lyon', 'lat' => 45.7640, 'lng' => 4.8357, 'role' => 'Architecte Bioclimatique'],
            ['id' => 'sophie', 'name' => 'Sophie Bernard', 'city' => 'Lyon', 'lat' => 45.7500, 'lng' => 4.8500, 'role' => 'Experte Permaculture'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $users[$data['id']] = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['id']) . '@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => "Je suis {$data['name']}, {$data['role']} basé(e) à {$data['city']}. Heureux de collaborer sur des projets qui ont du sens.",
                'location' => "{$data['city']}, France",
                'coordinates' => ['lat' => $data['lat'], 'lng' => $data['lng']],
                'avatar_url' => "https://i.pravatar.cc/150?u={$data['id']}",
                'trust_score' => rand(50, 90),
            ]);
        }

        // 3. Create Proches (Managed Profiles)
        $proches = [];
        $proches['lucas'] = Proche::create([
            'parent_id' => $users['marc']->id,
            'name' => 'Lucas (Photographe affilié)'
        ]);

        $proches['emile'] = Proche::create([
            'parent_id' => $users['thomas']->id,
            'name' => 'Emile (Menuisier traditionnel)'
        ]);

        // 4. Detailed Achievements (Users + Proches)
        $achievementsData = [
            // Marc
            ['user' => 'marc', 'type' => 'user', 'skill' => 'Développement Web', 'title' => 'Plateforme SaaS de Coliving'],
            ['user' => 'marc', 'type' => 'user', 'skill' => 'Stratégie Digitale', 'title' => 'Lancement Campus Numérique'],
            // Julie
            ['user' => 'julie', 'type' => 'user', 'skill' => 'UI Design', 'title' => 'Refonte de l\'application GreenHome'],
            ['user' => 'julie', 'type' => 'user', 'skill' => 'Éco-conception', 'title' => 'Audit d\'accessibilité web'],
            // Thomas
            ['user' => 'thomas', 'type' => 'user', 'skill' => 'Architecture', 'title' => 'Maison Passive Lyon 3'],
            ['user' => 'thomas', 'type' => 'user', 'skill' => 'Éco-conception', 'title' => 'Label Bâtiment Durable'],
            // Sophie
            ['user' => 'sophie', 'type' => 'user', 'skill' => 'Permaculture', 'title' => 'Ferme Pédagogique du Rhône'],
            // Proches
            ['user' => 'lucas', 'type' => 'proche', 'skill' => 'Photographie', 'title' => 'Couverture Photo Festival Arts'],
            ['user' => 'emile', 'type' => 'proche', 'skill' => 'Menuiserie', 'title' => 'Rénovation Charpente Historique'],
        ];

        $achs = [];
        foreach ($achievementsData as $k => $a) {
            $entityIdentifier = $a['type'] === 'user' ? 'user_id' : 'proche_id';
            $entityId = $a['type'] === 'user' ? $users[$a['user']]->id : $proches[$a['user']]->id;

            $achs[$k] = Achievement::create([
                $entityIdentifier => $entityId,
                'skill_id' => $skills[$a['skill']]->id,
                'title' => $a['title'],
                'description' => "Une réalisation validée en {$a['skill']}.",
                'is_verified' => true,
            ]);
        }

        // 5. Build Trust Web (Cross-Validations)
        AchievementValidation::create(['achievement_id' => $achs[0]->id, 'user_id' => $users['julie']->id, 'type' => 'validate', 'comment' => 'Marc a magistralement piloté la tech.']);
        AchievementValidation::create(['achievement_id' => $achs[2]->id, 'user_id' => $users['marc']->id, 'type' => 'validate', 'comment' => 'Les maquettes de Julie sont parfaites.']);
        AchievementValidation::create(['achievement_id' => $achs[4]->id, 'user_id' => $users['sophie']->id, 'type' => 'validate', 'comment' => 'Son design architectural respecte le vivant.']);
        AchievementValidation::create(['achievement_id' => $achs[6]->id, 'user_id' => $users['thomas']->id, 'type' => 'validate', 'comment' => 'Sophie a transformé un sol mort en oasis.']);
        AchievementValidation::create(['achievement_id' => $achs[1]->id, 'user_id' => $users['thomas']->id, 'type' => 'validate', 'comment' => 'Marc nous a très bien conseillé sur le digital.']);
        AchievementValidation::create(['achievement_id' => $achs[7]->id, 'user_id' => $users['julie']->id, 'type' => 'validate', 'comment' => 'Magnifiques photos, Lucas a du talent.']);
        AchievementValidation::create(['achievement_id' => $achs[8]->id, 'user_id' => $users['sophie']->id, 'type' => 'validate', 'comment' => 'Emile a fait du super boulot sur le cabanon.']);


        // 6. Create 3 Vibrant Circles
        $circles = [
            'collectif-tech' => Circle::create([
                'name' => 'Collectif "Digital Green"',
                'description' => 'Un espace pour repenser un web plus léger et éthique.',
                'address' => 'Paris, France',
                'coordinates' => ['lat' => 48.8600, 'lng' => 2.3400],
                'owner_id' => $users['marc']->id,
            ]),
            'atelier-rhone' => Circle::create([
                'name' => 'Ateliers du Rhône',
                'description' => 'Savoir-faire manuel, artisanat et synergie locale.',
                'address' => 'Lyon, France',
                'coordinates' => ['lat' => 45.7600, 'lng' => 4.8400],
                'owner_id' => $users['thomas']->id,
            ]),
            'vision-commune' => Circle::create([
                'name' => 'Maison de la Vision Globale',
                'description' => 'Réseau collaboratif mixant tech et monde physique.',
                'address' => 'France',
                'owner_id' => $users['julie']->id,
            ]),
        ];

        // 7. Circle Memberships
        $circles['collectif-tech']->addMember($users['julie'], 'admin');
        $circles['collectif-tech']->addMember($users['thomas'], 'member');
        
        $circles['atelier-rhone']->addMember($users['sophie'], 'admin');
        $circles['atelier-rhone']->addMember($users['marc'], 'member');
        
        $circles['vision-commune']->addMember($users['marc'], 'admin');
        $circles['vision-commune']->addMember($users['thomas'], 'member');
        $circles['vision-commune']->addMember($users['sophie'], 'member');

        // 8. Open Projects with Rich Offers/Demands
        $projects = [
            'projet-refonte' => Project::create([
                'title' => 'Refonte Web Application Mobile',
                'description' => 'Projet de refonte UI/UX et migration technique.',
                'address' => 'Paris',
                'coordinates' => ['lat' => 48.8600, 'lng' => 2.3450],
                'owner_id' => $users['marc']->id,
                'is_open' => true,
            ]),
            'projet-eco-lieu' => Project::create([
                'title' => 'Construction Éco-Lieu Partagé',
                'description' => 'Chantier participatif pour un centre de formation.',
                'address' => 'Lyon',
                'coordinates' => ['lat' => 45.7500, 'lng' => 4.8600],
                'owner_id' => $users['thomas']->id,
                'is_open' => true,
            ])
        ];

        // Project Teams
        $projects['projet-refonte']->addMember($users['julie'], 'admin');
        $projects['projet-refonte']->addMember($proches['lucas'], 'member');
        $projects['projet-eco-lieu']->addMember($users['sophie'], 'admin');
        $projects['projet-eco-lieu']->addMember($proches['emile'], 'member');
        $projects['projet-eco-lieu']->addMember($users['marc'], 'member');
        
        // Link Skills to Projects
        $projects['projet-refonte']->skills()->sync([$skills['Développement Web']->id, $skills['UI Design']->id]);
        $projects['projet-eco-lieu']->skills()->sync([$skills['Architecture']->id, $skills['Permaculture']->id, $skills['Menuiserie']->id]);


        // Offers & Demands (Marketplace)
        $projects['projet-refonte']->allOffers()->create([
            'title' => 'Audit UI/UX Complet', 
            'description' => 'Julie propose un audit détaillé de vos interfaces pour 500€.', 
            'type' => 'offer'
        ])->skills()->attach($skills['UI Design']->id);

        $projects['projet-refonte']->allOffers()->create([
            'title' => 'Recherche Développeur Front', 
            'description' => 'Nous cherchons un dev React pour épauler Marc sur cette semaine.', 
            'type' => 'demand'
        ])->skills()->attach($skills['Développement Web']->id);

        $projects['projet-eco-lieu']->allOffers()->create([
            'title' => 'Design Permaculturel du Terrain', 
            'description' => 'Sophie vous offre un plan aménagé pour optimiser les flux d\'eau.', 
            'type' => 'offer'
        ])->skills()->attach($skills['Permaculture']->id);

        $projects['projet-eco-lieu']->allOffers()->create([
            'title' => 'Besoin de bras pour la charpente', 
            'description' => 'Ce week-end, grand besoin d\'aide pour lever l\'ossature bois.', 
            'type' => 'demand'
        ])->skills()->attach($skills['Menuiserie']->id);


        // 9. Conversations in "Le Board" (Chronological)
        $boardChat = [
            ['circle' => 'vision-commune', 'sender' => 'julie', 'time' => '-2 days', 'text' => "Bienvenue à tous dans ce cercle de convergence ! C'est super de voir le groupe grandir."],
            ['circle' => 'vision-commune', 'sender' => 'thomas', 'time' => '-1 day', 'text' => "Merci Julie pour l'invitation. J'ai hâte de croiser nos approches."],
            ['circle' => 'vision-commune', 'sender' => 'marc', 'time' => '-12 hours', 'text' => "Salut ! Si quelqu'un veut échanger sur de l'outillage numérique frugal, je suis là."],
            ['circle' => 'vision-commune', 'sender' => 'sophie', 'time' => '-2 hours', 'text' => "Top ! Justement Thomas et moi on se posait des questions pour numériser certaines données de nos sols...!"],
        ];

        foreach ($boardChat as $chat) {
            Message::create([
                'circle_id' => $circles[$chat['circle']]->id,
                'sender_id' => $users[$chat['sender']]->id,
                'content' => $chat['text'],
                'created_at' => \Carbon\Carbon::parse($chat['time']),
                'updated_at' => \Carbon\Carbon::parse($chat['time']),
            ]);
        }


        // 10. Conversations in "Le Forum" (Projects)
        $forumChat = [
            ['project' => 'projet-eco-lieu', 'sender' => 'thomas', 'time' => '-3 days', 'text' => "Le terrassement commence demain. On espère qu'il ne pleuvra pas trop."],
            ['project' => 'projet-eco-lieu', 'sender' => 'sophie', 'time' => '-3 days', 'text' => "Et moi j'ai reçu les plants pour les haies sèches. On mettra tout ça en jauge."],
            ['project' => 'projet-eco-lieu', 'sender' => 'marc', 'time' => '-1 day', 'text' => "Je passerai ce week-end pour donner un coup de main, vous me direz ce qu'il faut amener !"],
            ['project' => 'projet-eco-lieu', 'sender' => 'thomas', 'time' => 'now', 'text' => "Super Marc, prends de bonnes chaussures de sécu, ça va batailler sévère."],
        ];

        foreach ($forumChat as $chat) {
            Message::create([
                'project_id' => $projects[$chat['project']]->id,
                'sender_id' => $users[$chat['sender']]->id,
                'content' => $chat['text'],
                'created_at' => \Carbon\Carbon::parse($chat['time']),
                'updated_at' => \Carbon\Carbon::parse($chat['time']),
            ]);
        }

        // 11. Project Reviews
        $review1 = ProjectReview::create([
            'project_id' => $projects['projet-eco-lieu']->id,
            'user_id' => $users['marc']->id,
            'type' => 'validate',
            'comment' => "Une organisation parfaite sur le chantier, Thomas gère ça d'une main de maître.",
            'created_at' => now()->subHours(5)
        ]);

        ProjectReview::create([
            'project_id' => $projects['projet-eco-lieu']->id,
            'user_id' => $users['thomas']->id,
            'type' => 'validate',
            'comment' => "Merci d'être venu nous aider si spontanément !",
            'parent_id' => $review1->id,
            'created_at' => now()->subHours(2)
        ]);

        // 12. Recalculate Every User's Trust Score
        foreach ($users as $user) {
            $user->recalculateTrustScore();
        }
    }
}
