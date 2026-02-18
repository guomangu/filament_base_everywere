<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Circle;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Achievement;
use App\Models\AchievementValidation;
use App\Models\Message;
use App\Models\ProjectReview;
use Illuminate\Support\Str;

class RealisticDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Skills (Expanded)
        $skillsData = [
            'Laravel' => 'laravel',
            'React' => 'react',
            'UI Design' => 'ui-design',
            'Permaculture' => 'permaculture',
            'Menuiserie' => 'menuiserie',
            'Architecture' => 'architecture',
            'Cuisine' => 'cuisine',
            'Photographie' => 'photographie',
            'Marketing' => 'marketing',
            'Stratégie' => 'strategie',
            'FoodTech' => 'foodtech',
            'Éco-conception' => 'eco-conception',
            'Low-Tech' => 'low-tech',
            'Ferblanterie' => 'ferblanterie',
            'Apiculture' => 'apiculture',
            'Copywriting' => 'copywriting',
            'Data Science' => 'data-science',
            'Ébénisterie' => 'ebenisterie',
            'Maraîchage' => 'maraichage',
            'Céramique' => 'ceramique',
            'Blockchain' => 'blockchain',
            'Hydrologie' => 'hydrologie',
        ];

        $skills = [];
        foreach ($skillsData as $name => $slug) {
            $skills[$name] = Skill::updateOrCreate(['name' => $name], ['slug' => $slug]);
        }

        // 2. Create 22 Realistic Personas
        $usersData = [
            ['id' => 'marc', 'name' => 'Marc Lefebvre', 'city' => 'Paris', 'lat' => 48.8566, 'lng' => 2.3522, 'role' => 'Fullstack Developer'],
            ['id' => 'julie', 'name' => 'Julie Chen', 'city' => 'Paris', 'lat' => 48.8647, 'lng' => 2.3327, 'role' => 'UX Designer'],
            ['id' => 'thomas', 'name' => 'Thomas Morel', 'city' => 'Lyon', 'lat' => 45.7640, 'lng' => 4.8357, 'role' => 'Architecte'],
            ['id' => 'sophie', 'name' => 'Sophie Bernard', 'city' => 'Lyon', 'lat' => 45.7500, 'lng' => 4.8500, 'role' => 'Permacultrice'],
            ['id' => 'antoine', 'name' => 'Antoine Duval', 'city' => 'Marseille', 'lat' => 43.2965, 'lng' => 5.3698, 'role' => 'Menuisier'],
            ['id' => 'elena', 'name' => 'Elena Rossi', 'city' => 'Marseille', 'lat' => 43.3000, 'lng' => 5.4000, 'role' => 'Chef de Cuisine'],
            ['id' => 'lucas', 'name' => 'Lucas Petit', 'city' => 'Nantes', 'lat' => 47.2184, 'lng' => -1.5536, 'role' => 'Copywriter'],
            ['id' => 'ines', 'name' => 'Inès Garcia', 'city' => 'Nantes', 'lat' => 47.2200, 'lng' => -1.5600, 'role' => 'Photographe'],
            ['id' => 'gabriel', 'name' => 'Gabriel Vandernoot', 'city' => 'Bruxelles', 'lat' => 50.8503, 'lng' => 4.3517, 'role' => 'Stratège Réseau'],
            ['id' => 'claire', 'name' => 'Claire Dubois', 'city' => 'Bordeaux', 'lat' => 44.8378, 'lng' => -0.5792, 'role' => 'Hydrologue'],
            ['id' => 'nicolas', 'name' => 'Nicolas Girard', 'city' => 'Bordeaux', 'lat' => 44.8400, 'lng' => -0.5800, 'role' => 'Low-Tech Expert'],
            ['id' => 'sarah', 'name' => 'Sarah Lopez', 'city' => 'Strasbourg', 'lat' => 48.5734, 'lng' => 7.7521, 'role' => 'Maraîchère'],
            ['id' => 'pierre', 'name' => 'Pierre Leroy', 'city' => 'Strasbourg', 'lat' => 48.5800, 'lng' => 7.7600, 'role' => 'Apiculteur'],
            ['id' => 'hannya', 'name' => 'Hannya Tanaka', 'city' => 'Lille', 'lat' => 50.6292, 'lng' => 3.0573, 'role' => 'Data Scientist'],
            ['id' => 'victor', 'name' => 'Victor Hugo', 'city' => 'Lille', 'lat' => 50.6300, 'lng' => 3.0600, 'role' => 'Expert Blockchain'],
            ['id' => 'emma', 'name' => 'Emma Watson', 'city' => 'Toulouse', 'lat' => 43.6047, 'lng' => 1.4442, 'role' => 'Céramiste'],
            ['id' => 'hugo', 'name' => 'Hugo Boss', 'city' => 'Toulouse', 'lat' => 43.6100, 'lng' => 1.4500, 'role' => 'Ébéniste'],
            ['id' => 'lea', 'name' => 'Léa Fontaine', 'city' => 'Genève', 'lat' => 46.2044, 'lng' => 6.1432, 'role' => 'Consultante RSE'],
            ['id' => 'arthur', 'name' => 'Arthur Rimbaud', 'city' => 'Namur', 'lat' => 50.4674, 'lng' => 4.8719, 'role' => 'Ferblantier'],
            ['id' => 'mathilde', 'name' => 'Mathilde Blanc', 'city' => 'Montpellier', 'lat' => 43.6108, 'lng' => 3.8767, 'role' => 'Urbaniste'],
            ['id' => 'leo', 'name' => 'Léo Marchand', 'city' => 'Rennes', 'lat' => 48.1173, 'lng' => -1.6778, 'role' => 'Musicien'],
            ['id' => 'clara', 'name' => 'Clara Morgane', 'city' => 'Nice', 'lat' => 43.7102, 'lng' => 7.2620, 'role' => 'Coach Bien-être'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $users[$data['id']] = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['id']) . '@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => "Je suis {$data['name']}, {$data['role']} basé à {$data['city']}. Passionné par la collaboration et le partage de savoir-faire.",
                'location' => "{$data['city']}, France",
                'coordinates' => ['lat' => $data['lat'], 'lng' => $data['lng']],
                'avatar_url' => "https://i.pravatar.cc/150?u={$data['id']}",
                'trust_score' => rand(50, 95),
            ]);
        }

        // 3. Create 7 Thematic Circles
        $circles = [
            'tech-paris' => Circle::create([
                'name' => 'Tech & Design Hub',
                'description' => 'Collaboration de pointe pour les projets digitaux éthiques.',
                'address' => 'Station F, Paris, France',
                'coordinates' => ['lat' => 48.8338, 'lng' => 2.3708],
                'owner_id' => $users['marc']->id,
            ]),
            'habitat-lyon' => Circle::create([
                'name' => 'Atelier Vivant Lyon',
                'description' => 'Construction écologique et design bioclimatique.',
                'address' => 'Confluence, Lyon, France',
                'coordinates' => ['lat' => 45.7485, 'lng' => 4.8159],
                'owner_id' => $users['thomas']->id,
            ]),
            'gastro-marseille' => Circle::create([
                'name' => 'Mer & Saveurs',
                'description' => 'Gastronomie locale et durable en Méditerranée.',
                'address' => 'Vieux-Port, Marseille, France',
                'coordinates' => ['lat' => 43.2951, 'lng' => 5.3744],
                'owner_id' => $users['elena']->id,
            ]),
            'lowtech-bordeaux' => Circle::create([
                'name' => 'Bordeaux Low-Tech',
                'description' => 'Ingénierie utile, durable et accessible pour le quotidien.',
                'address' => 'Darwin, Bordeaux, France',
                'coordinates' => ['lat' => 44.8504, 'lng' => -0.5619],
                'owner_id' => $users['nicolas']->id,
            ]),
            'agri-strasbourg' => Circle::create([
                'name' => 'Terres d\'Alsace',
                'description' => 'Maraîchage bio et apiculture urbaine en circuit court.',
                'address' => 'Parc de l\'Orangerie, Strasbourg, France',
                'coordinates' => ['lat' => 48.5839, 'lng' => 7.7766],
                'owner_id' => $users['sarah']->id,
            ]),
            'art-nantes' => Circle::create([
                'name' => 'L\'Île des Créatifs',
                'description' => 'Collectif d\'artistes, photographes et artisans d\'art.',
                'address' => 'Machines de l\'île, Nantes, France',
                'coordinates' => ['lat' => 47.2064, 'lng' => -1.5644],
                'owner_id' => $users['ines']->id,
            ]),
            'impact-nation' => Circle::create([
                'name' => 'Impact Nation',
                'description' => 'Le réseau global des acteurs du changement.',
                'address' => 'Grand Place, Bruxelles, Belgique',
                'coordinates' => ['lat' => 50.8467, 'lng' => 4.3524],
                'owner_id' => $users['gabriel']->id,
            ]),
        ];

        // 4. Detailed Memberships
        $circles['tech-paris']->addMember($users['julie'], 'admin');
        $circles['tech-paris']->addMember($users['hannya'], 'member');
        $circles['tech-paris']->addMember($users['victor'], 'member');
        
        $circles['habitat-lyon']->addMember($users['sophie'], 'admin');
        $circles['habitat-lyon']->addMember($users['antoine'], 'member');
        $circles['habitat-lyon']->addMember($users['hugo'], 'member');
        
        $circles['gastro-marseille']->addMember($users['antoine'], 'member');
        $circles['gastro-marseille']->addMember($users['ines'], 'member');
        
        $circles['lowtech-bordeaux']->addMember($users['claire'], 'admin');
        $circles['lowtech-bordeaux']->addMember($users['mathilde'], 'member');
        
        $circles['agri-strasbourg']->addMember($users['pierre'], 'admin');
        $circles['agri-strasbourg']->addMember($users['lea'], 'member');
        
        $circles['art-nantes']->addMember($users['lucas'], 'admin');
        $circles['art-nantes']->addMember($users['emma'], 'member');
        $circles['art-nantes']->addMember($users['leo'], 'member');

        foreach ($users as $user) {
            $circles['impact-nation']->addMember($user, 'member');
        }

        // 5. 10+ Diverse Projects
        $projectsData = [
            ['id' => 'nomad', 'owner' => 'lucas', 'title' => 'Digital Nomad Platform', 'city' => 'Nantes', 'lat' => 47.2184, 'lng' => -1.5536, 'skills' => ['Laravel', 'React', 'Marketing']],
            ['id' => 'cabane', 'owner' => 'thomas', 'title' => 'L\'Archipel Auto-construit', 'city' => 'Lyon', 'lat' => 45.7772, 'lng' => 4.8554, 'skills' => ['Architecture', 'Menuiserie', 'Éco-conception']],
            ['id' => 'trace', 'owner' => 'marc', 'title' => 'FoodTrace Blockchain', 'city' => 'Paris', 'lat' => 48.8566, 'lng' => 2.3522, 'skills' => ['Laravel', 'Blockchain', 'FoodTech']],
            ['id' => 'eau', 'owner' => 'claire', 'title' => 'Régénération Hydrique', 'city' => 'Bordeaux', 'lat' => 44.8504, 'lng' => -0.5619, 'skills' => ['Hydrologie', 'Permaculture', 'Stratégie']],
            ['id' => 'miel', 'owner' => 'pierre', 'title' => 'Miel de Strasbourg 2026', 'city' => 'Strasbourg', 'lat' => 48.5839, 'lng' => 7.7766, 'skills' => ['Apiculture', 'Maraîchage', 'Marketing']],
            ['id' => 'terre', 'owner' => 'emma', 'title' => 'Céramique & Low-Tech', 'city' => 'Toulouse', 'lat' => 43.6047, 'lng' => 1.4442, 'skills' => ['Céramique', 'Low-Tech', 'Éco-conception']],
            ['id' => 'bois', 'owner' => 'hugo', 'title' => 'Ebénisterie Participative', 'city' => 'Toulouse', 'lat' => 43.6100, 'lng' => 1.4500, 'skills' => ['Ébénisterie', 'Menuiserie', 'Stratégie']],
            ['id' => 'data', 'owner' => 'hannya', 'title' => 'Data pour l\'Impact', 'city' => 'Lille', 'lat' => 50.6292, 'lng' => 3.0573, 'skills' => ['Data Science', 'Laravel', 'Copywriting']],
            ['id' => 'rse', 'owner' => 'lea', 'title' => 'Audit RSE Global', 'city' => 'Genève', 'lat' => 46.2044, 'lng' => 6.1432, 'skills' => ['Stratégie', 'Marketing']],
            ['id' => 'metal', 'owner' => 'arthur', 'title' => 'Metal & Design', 'city' => 'Namur', 'lat' => 50.4674, 'lng' => 4.8719, 'skills' => ['Ferblanterie', 'UI Design']],
        ];

        $projects = [];
        foreach ($projectsData as $p) {
            $projects[$p['id']] = Project::create([
                'title' => $p['title'],
                'description' => "Projet ambitieux de {$p['title']} visant à transformer nos pratiques à {$p['city']}.",
                'address' => "{$p['city']}, France",
                'coordinates' => ['lat' => $p['lat'], 'lng' => $p['lng']],
                'owner_id' => $users[$p['owner']]->id,
                'is_open' => true,
            ]);

            foreach ($p['skills'] as $sName) {
                $projects[$p['id']]->skills()->attach($skills[$sName]->id);
            }
        }

        // Project Teams
        $projects['nomad']->addMember($users['marc'], 'admin');
        $projects['nomad']->addMember($users['julie'], 'member');
        $projects['cabane']->addMember($users['antoine'], 'admin');
        $projects['cabane']->addMember($users['sophie'], 'member');
        $projects['trace']->addMember($users['elena'], 'member');
        $projects['trace']->addMember($users['victor'], 'member');

        // 6. Enriched Offers & Demands
        foreach ($projects as $p) {
            $p->allOffers()->create(['title' => 'Expertise Technique', 'description' => 'Accompagnement sur la partie métier.', 'type' => 'offer']);
            $p->allOffers()->create(['title' => 'Besoin de visibilité', 'description' => 'Nous cherchons quelqu\'un pour la com.', 'type' => 'demand']);
            $p->allOffers()->create(['title' => 'Matériel disponible', 'description' => 'Outillage partagé pour l\'équipe.', 'type' => 'offer']);
        }

        // 7. Dense Trust Web (Achievements & Validations)
        $achNames = [
            ['user' => 'marc', 'skill' => 'Laravel', 'title' => 'Système de Graphe'],
            ['user' => 'julie', 'skill' => 'UI Design', 'title' => 'Refonte God Stack'],
            ['user' => 'thomas', 'skill' => 'Architecture', 'title' => 'Maison Passive'],
            ['user' => 'sophie', 'skill' => 'Permaculture', 'title' => 'Forêt Comestible'],
            ['user' => 'claire', 'skill' => 'Hydrologie', 'title' => 'Gestion des Eaux'],
            ['user' => 'nicolas', 'skill' => 'Low-Tech', 'title' => 'Filtre à Eau Low-Tech'],
            ['user' => 'sarah', 'skill' => 'Maraîchage', 'title' => 'AMAP Strasbourg'],
            ['user' => 'hannya', 'skill' => 'Data Science', 'title' => 'Modèle Prédictif'],
            ['user' => 'hugo', 'skill' => 'Ébénisterie', 'title' => 'Table en Noyer'],
            ['user' => 'emma', 'skill' => 'Céramique', 'title' => 'Art de la Table'],
            ['user' => 'gabriel', 'skill' => 'Stratégie', 'title' => 'Conférence Confiance'],
            ['user' => 'lucas', 'skill' => 'Copywriting', 'title' => 'Manifeste Impact'],
            ['user' => 'ines', 'skill' => 'Photographie', 'title' => 'Série Portrait'],
            ['user' => 'antoine', 'skill' => 'Menuiserie', 'title' => 'Charpente Traditionnelle'],
        ];

        $achs = [];
        foreach ($achNames as $a) {
            $achs[] = Achievement::create([
                'user_id' => $users[$a['user']]->id,
                'skill_id' => $skills[$a['skill']]->id,
                'title' => $a['title'],
                'description' => "Réalisation majeure dans le domaine de {$a['skill']}.",
                'is_verified' => true,
            ]);
        }

        // Mutual Cross-Validations
        foreach ($achs as $ach) {
            $validator = collect($users)->random();
            if ($validator->id !== $ach->user_id) {
                AchievementValidation::create([
                    'achievement_id' => $ach->id,
                    'user_id' => $validator->id,
                    'type' => 'validate',
                    'comment' => 'Une expertise indéniable, un plaisir de collaborer.',
                ]);
            }
        }

        // 8. Social Content (Messages & Reviews)
        foreach ($circles as $c) {
            Message::create(['circle_id' => $c->id, 'sender_id' => $c->owner_id, 'content' => "Bienvenue dans le cercle {$c->name} !"]);
            $visitor = collect($users)->random();
            Message::create(['circle_id' => $c->id, 'sender_id' => $visitor->id, 'content' => "Ravi de rejoindre ce collectif."]);
        }

        foreach ($projects as $p) {
            $review = ProjectReview::create([
                'project_id' => $p->id,
                'user_id' => collect($users)->random()->id,
                'type' => 'validate',
                'comment' => 'Excellent projet, très bien géré.',
            ]);

            ProjectReview::create([
                'project_id' => $p->id,
                'user_id' => $p->owner_id,
                'type' => 'validate',
                'comment' => 'Merci pour votre soutien !',
                'parent_id' => $review->id,
            ]);
        }

        // 9. Recalculate Everything
        foreach ($users as $user) {
            $user->recalculateTrustScore();
        }
    }
}
