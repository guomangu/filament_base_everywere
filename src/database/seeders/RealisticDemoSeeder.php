<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Circle;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Achievement;
use App\Models\AchievementValidation;
use Illuminate\Support\Str;

class RealisticDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Skills
        $skills = [
            'Laravel' => Skill::updateOrCreate(['name' => 'Laravel'], ['slug' => 'laravel']),
            'React' => Skill::updateOrCreate(['name' => 'React'], ['slug' => 'react']),
            'UI Design' => Skill::updateOrCreate(['name' => 'UI Design'], ['slug' => 'ui-design']),
            'Permaculture' => Skill::updateOrCreate(['name' => 'Permaculture'], ['slug' => 'permaculture']),
            'Menuiserie' => Skill::updateOrCreate(['name' => 'Menuiserie'], ['slug' => 'menuiserie']),
            'Architecture' => Skill::updateOrCreate(['name' => 'Architecture'], ['slug' => 'architecture']),
            'Cuisine' => Skill::updateOrCreate(['name' => 'Cuisine'], ['slug' => 'cuisine']),
            'Photographie' => Skill::updateOrCreate(['name' => 'Photographie'], ['slug' => 'photographie']),
            'Marketing' => Skill::updateOrCreate(['name' => 'Marketing'], ['slug' => 'marketing']),
            'Stratégie' => Skill::updateOrCreate(['name' => 'Stratégie'], ['slug' => 'strategie']),
            'FoodTech' => Skill::updateOrCreate(['name' => 'FoodTech'], ['slug' => 'foodtech']),
            'Éco-conception' => Skill::updateOrCreate(['name' => 'Éco-conception'], ['slug' => 'eco-conception']),
        ];

        // 2. Create 9 Realistic Users
        $users = [
            'marc' => User::create([
                'name' => 'Marc Lefebvre',
                'email' => 'marc@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Développeur Fullstack passionné par Laravel et les architectures scalables. Expert en infrastructure cloud. Basé à Paris.',
                'location' => 'Paris, France',
                'coordinates' => ['lat' => 48.8566, 'lng' => 2.3522],
                'avatar_url' => 'https://i.pravatar.cc/150?u=marc',
                'trust_score' => 85,
            ]),
            'julie' => User::create([
                'name' => 'Julie Chen',
                'email' => 'julie@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Designer UX/UI spécialisée dans les interfaces minimalistes et l\'accessibilité. Photographe amateur à ses heures perdues.',
                'location' => 'Paris, France',
                'coordinates' => ['lat' => 48.8647, 'lng' => 2.3327],
                'avatar_url' => 'https://i.pravatar.cc/150?u=julie',
                'trust_score' => 78,
            ]),
            'thomas' => User::create([
                'name' => 'Thomas Morel',
                'email' => 'thomas@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Architecte DPLG spécialisé dans la construction bois et l\'habitat durable. Engagé pour une ville plus verte.',
                'location' => 'Lyon, France',
                'coordinates' => ['lat' => 45.7640, 'lng' => 4.8357],
                'avatar_url' => 'https://i.pravatar.cc/150?u=thomas',
                'trust_score' => 92,
            ]),
            'sophie' => User::create([
                'name' => 'Sophie Bernard',
                'email' => 'sophie@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Formatrice en permaculture. J\'aide les citadins et les entreprises à reverdir leur environnement.',
                'location' => 'Lyon, France',
                'coordinates' => ['lat' => 45.7500, 'lng' => 4.8500],
                'avatar_url' => 'https://i.pravatar.cc/150?u=sophie',
                'trust_score' => 70,
            ]),
            'antoine' => User::create([
                'name' => 'Antoine Duval',
                'email' => 'antoine@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Artisan menuisier. Mobilier sur mesure et agencement d\'espaces durables à Marseille.',
                'location' => 'Marseille, France',
                'coordinates' => ['lat' => 43.2965, 'lng' => 5.3698],
                'avatar_url' => 'https://i.pravatar.cc/150?u=antoine',
                'trust_score' => 65,
            ]),
            'elena' => User::create([
                'name' => 'Elena Rossi',
                'email' => 'elena@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Chef de cuisine méditerranéenne. Consultante FoodTech pour une gastronomie responsable.',
                'location' => 'Marseille, France',
                'coordinates' => ['lat' => 43.3000, 'lng' => 5.4000],
                'avatar_url' => 'https://i.pravatar.cc/150?u=elena',
                'trust_score' => 88,
            ]),
            'lucas' => User::create([
                'name' => 'Lucas Petit',
                'email' => 'lucas@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Consultant marketing digital. Expert en stratégie de contenu pour les marques éthiques.',
                'location' => 'Nantes, France',
                'coordinates' => ['lat' => 47.2184, 'lng' => -1.5536],
                'avatar_url' => 'https://i.pravatar.cc/150?u=lucas',
                'trust_score' => 60,
            ]),
            'ines' => User::create([
                'name' => 'Inès Garcia',
                'email' => 'ines@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Photographe professionnelle. Capturer l\'instant et l\'essence des projets à taille humaine.',
                'location' => 'Nantes, France',
                'coordinates' => ['lat' => 47.2200, 'lng' => -1.5600],
                'avatar_url' => 'https://i.pravatar.cc/150?u=ines',
                'trust_score' => 72,
            ]),
            'gabriel' => User::create([
                'name' => 'Gabriel Vandernoot',
                'email' => 'gabriel@trustcircle.com',
                'password' => bcrypt('password'),
                'bio' => 'Conseiller en stratégie réseau et facilitateur de confiance. Passionné par l\'intelligence collective.',
                'location' => 'Bruxelles, Belgique',
                'coordinates' => ['lat' => 50.8503, 'lng' => 4.3517],
                'avatar_url' => 'https://i.pravatar.cc/150?u=gabriel',
                'trust_score' => 95,
            ]),
        ];

        // 3. Create Circles (Extended)
        $circles = [
            'tech-paris' => Circle::create([
                'name' => 'Tech & Design Paris',
                'description' => 'Un cercle d\'experts regroupant les meilleurs profils techniques et créatifs pour façonner le futur numérique.',
                'address' => 'Station F, 5 Parvis Alan Turing, 75013 Paris, France',
                'coordinates' => ['lat' => 48.8338, 'lng' => 2.3708],
                'owner_id' => $users['marc']->id,
            ]),
            'habitat-lyon' => Circle::create([
                'name' => 'Éco-Habitat Lyon',
                'description' => 'Collectif d\'artisans et d\'architectes dédiés à la rénovation bioclimatique et à l\'habitat sain.',
                'address' => 'Place Bellecour, 69002 Lyon, France',
                'coordinates' => ['lat' => 45.7578, 'lng' => 4.8322],
                'owner_id' => $users['thomas']->id,
            ]),
            'gastronomie-med' => Circle::create([
                'name' => 'Gastronomie Méditerranéenne',
                'description' => 'Partage de recettes, techniques et valorisation des terroirs marseillais.',
                'address' => 'Vieux-Port, 13001 Marseille, France',
                'coordinates' => ['lat' => 43.2951, 'lng' => 5.3744],
                'owner_id' => $users['elena']->id,
            ]),
            'reseau-national' => Circle::create([
                'name' => 'Impact Nation',
                'description' => 'Le réseau national des acteurs du changement. Confiance, Expertise et Proximité.',
                'address' => 'Grand Place, 1000 Bruxelles, Belgique',
                'coordinates' => ['lat' => 50.8467, 'lng' => 4.3524],
                'owner_id' => $users['gabriel']->id,
            ]),
        ];

        // 4. Create Memberships (Deeper Interconnection)
        $circles['tech-paris']->addMember($users['julie'], 'admin');
        $circles['tech-paris']->addMember($users['lucas'], 'member');
        $circles['tech-paris']->addMember($users['sophie'], 'member'); // Urban Green sites advisory
        $circles['tech-paris']->addMember($users['elena'], 'member'); // FoodTech advisory

        $circles['habitat-lyon']->addMember($users['sophie'], 'admin');
        $circles['habitat-lyon']->addMember($users['antoine'], 'member');
        $circles['habitat-lyon']->addMember($users['marc'], 'member'); // Technical IoT for houses advisor (remote)

        $circles['gastronomie-med']->addMember($users['antoine'], 'member');
        $circles['gastronomie-med']->addMember($users['ines'], 'member'); // Food photography

        // Everyone in Impact Nation
        foreach ($users as $user) {
            $circles['reseau-national']->addMember($user, 'member');
        }

        // 5. Create Projects (More detailed)
        $projects = [
            'nomad-hub' => Project::create([
                'title' => 'Digital Nomad Hub',
                'description' => 'Création d\'une plateforme de coliving et coworking premium. Nous cherchons à intégrer des technologies durables et un design exceptionnel.',
                'address' => 'Passage Pommeraye, 44000 Nantes, France',
                'coordinates' => ['lat' => 47.2133, 'lng' => -1.5591],
                'owner_id' => $users['lucas']->id,
                'is_open' => true,
            ]),
            'eco-cabane' => Project::create([
                'title' => 'L\'Éco-Cabane Collective',
                'description' => 'Unité d\'habitation modulaire en bois. Chantier participatif et éco-conception.',
                'address' => 'Parc de la Tête d\'Or, 69006 Lyon, France',
                'coordinates' => ['lat' => 45.7772, 'lng' => 4.8554],
                'owner_id' => $users['thomas']->id,
                'is_open' => true,
            ]),
            'food-safe' => Project::create([
                'title' => 'FoodSafe Analytics',
                'description' => 'Application de traçabilité pour les restaurants engagés dans le zéro déchet.',
                'address' => 'Canal Saint-Martin, 75010 Paris, France',
                'coordinates' => ['lat' => 48.8735, 'lng' => 2.3665],
                'owner_id' => $users['marc']->id,
                'is_open' => true,
            ]),
        ];

        // 6. Connect Projects to Circles/Users
        $projects['nomad-hub']->addMember($users['marc'], 'admin');
        $projects['nomad-hub']->addMember($users['julie'], 'member');
        $projects['nomad-hub']->addMember($users['ines'], 'member');
        $projects['nomad-hub']->skills()->attach([$skills['Laravel']->id, $skills['UI Design']->id, $skills['Photographie']->id, $skills['Marketing']->id]);

        $projects['eco-cabane']->addMember($users['antoine'], 'admin');
        $projects['eco-cabane']->addMember($users['sophie'], 'member');
        $projects['eco-cabane']->skills()->attach([$skills['Menuiserie']->id, $skills['Architecture']->id, $skills['Permaculture']->id, $skills['Éco-conception']->id]);

        $projects['food-safe']->addMember($users['elena'], 'admin');
        $projects['food-safe']->addMember($users['julie'], 'member');
        $projects['food-safe']->skills()->attach([$skills['FoodTech']->id, $skills['Laravel']->id, $skills['UI Design']->id]);

        // 7. Add Offers and Demands (Enriched)
        $projects['nomad-hub']->allOffers()->create([
            'title' => 'Stratégie de Lancement',
            'description' => 'Définition du plan marketing global et branding.',
            'type' => 'offer',
        ]);
        $projects['nomad-hub']->allOffers()->create([
            'title' => 'Dév React (Dashboard)',
            'description' => 'Besoin d\'un expert pour l\'interface de gestion des résidents.',
            'type' => 'demand',
        ]);
        $projects['nomad-hub']->allOffers()->create([
            'title' => 'Série de Portraits',
            'description' => 'Photos des membres fondateurs pour le site web.',
            'type' => 'offer',
        ]);

        $projects['eco-cabane']->allOffers()->create([
            'title' => 'Structure Bois',
            'description' => 'Réalisation de l\'ossature primaire en pin local.',
            'type' => 'offer',
        ]);
        $projects['eco-cabane']->allOffers()->create([
            'title' => 'Design Permacole',
            'description' => 'Planification du jardin nourricier entourant la cabane.',
            'type' => 'offer',
        ]);

        $projects['food-safe']->allOffers()->create([
            'title' => 'Consultation Zéro Déchet',
            'description' => 'Analyse des flux de déchets en cuisine.',
            'type' => 'offer',
        ]);

        // 8. Achievements & Validations (Massive Trust Network)
        
        $ach_data = [
            ['u' => 'marc', 's' => 'Laravel', 't' => 'Moteur de Recherche TrustCircle', 'd' => 'Algorithme de proximité kilométrique et fusion de modèles.'],
            ['u' => 'marc', 's' => 'React', 't' => 'Dashboard Temps Réel', 'd' => 'Interface interactive avec WebSockets.'],
            ['u' => 'julie', 's' => 'UI Design', 't' => 'Identité Impact Nation', 'd' => 'Charte graphique complète pour le réseau national.'],
            ['u' => 'julie', 's' => 'Photographie', 't' => 'Exposition "Villes Invisibles"', 'd' => 'Série de 20 clichés sur l\'architecture cachée de Paris.'],
            ['u' => 'thomas', 's' => 'Architecture', 't' => 'Tour Bioclimatique Lyon', 'd' => 'Projet de R&D sur la ventilation naturelle.'],
            ['u' => 'sophie', 's' => 'Permaculture', 't' => 'Potager d\'Entreprise L\'Oréal', 'd' => 'Aménagement de 500m2 de toiture terrasse.'],
            ['u' => 'antoine', 's' => 'Menuiserie', 't' => 'Agencement Restaurant "Le Mistral"', 'd' => 'Mobilier intégralement recyclé.'],
            ['u' => 'elena', 's' => 'Cuisine', 't' => 'Masterclass "La Mer en Partage"', 'd' => 'Formation sur les poissons oubliés.'],
            ['u' => 'lucas', 's' => 'Marketing', 't' => 'Campagne "Slow Living France"', 'd' => 'Hausse de 40% de la visibilité sur les réseaux.'],
            ['u' => 'ines', 's' => 'Photographie', 't' => 'Reportage "Artisans du Bois"', 'd' => 'Publication dans un magazine national.'],
            ['u' => 'gabriel', 's' => 'Stratégie', 't' => 'Fusion des Réseaux Impact 2025', 'd' => 'Négociation complexe pour regrouper 5 collectifs.'],
        ];

        $created_achs = [];
        foreach ($ach_data as $data) {
            $created_achs[$data['u'].'_'.$data['s']] = Achievement::create([
                'user_id' => $users[$data['u']]->id,
                'skill_id' => $skills[$data['s']]->id,
                'title' => $data['t'],
                'description' => $data['d'],
                'is_verified' => true,
            ]);
        }

        // Cross-Validations (Dense Network)
        $validations = [
            ['a' => 'marc_Laravel', 'v' => 'gabriel', 'c' => 'Code d\'une propreté exemplaire.'],
            ['a' => 'julie_UI Design', 'v' => 'marc', 'c' => 'Un oeil incroyable sur les détails.'],
            ['a' => 'thomas_Architecture', 'v' => 'gabriel', 'c' => 'Thomas comprend les enjeux de demain.'],
            ['a' => 'sophie_Permaculture', 'v' => 'thomas', 'c' => 'Expertise précieuse pour nos projets archi.'],
            ['a' => 'antoine_Menuiserie', 'v' => 'thomas', 'c' => 'Un artisan qui respecte le matériau.'],
            ['a' => 'elena_Cuisine', 'v' => 'ines', 'c' => 'Plats aussi beaux que bons, un régal à photographier.'],
            ['a' => 'lucas_Marketing', 'v' => 'julie', 'c' => 'Ses stratégies sont claires et percutantes.'],
            ['a' => 'ines_Photographie', 'v' => 'antoine', 'c' => 'Inès a su capturer l\'âme de mon atelier.'],
            ['a' => 'gabriel_Stratégie', 'v' => 'marc', 'c' => 'Un mentor indispensable pour notre vision.'],
            ['a' => 'marc_React', 'v' => 'julie', 'c' => 'L\'intégration est exactement fidèle aux maquettes.'],
        ];

        foreach ($validations as $v) {
            AchievementValidation::create([
                'achievement_id' => $created_achs[$v['a']]->id,
                'user_id' => $users[$v['v']]->id,
                'type' => 'validate',
                'comment' => $v['c']
            ]);
        }

        // 9. Recalculate Scores
        foreach ($users as $user) {
            $user->recalculateTrustScore();
        }
    }
}
