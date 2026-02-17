# 🤖 GUIDE DE L'AGENT IA : Architecture & Fonctionnement

Ce document est destiné aux agents IA autonomes travaillant sur ce projet. Il résume l'état actuel, la stack technique et les protocoles de développement.

## 🌟 Philosophie du Projet : "God Stack"
Le projet suit le manifeste **God Stack** : une application **ultra-portable**, **monolithique** et **indépendante**.
- **Zéro Dépendance Système** : Tout est dans le dossier (Binaires inclusivement).
- **Proximité & Confiance** : Un réseau social basé sur la géolocalisation et la vérification des compétences.
- **Vitesse de Dévelppement** : Utilisation intensive de Blueprint et Filament pour minimiser le code répétitif.

---

## 🏗️ Architecture Technique

### 1. La Stack "Portable"
- **Binaire Serveur** : `bin/frankenphp` (Serveur Web + Runtime PHP 8.3+)
- **Base de Données** : `bin/mariadbd` (MariaDB portable, données dans `data/mysql`)
- **Framework** : Laravel 11
- **UI & Interaction** : Filament v3 (Admin & Formulaires) + Livewire v3 (Composants Full-Page)
- **Temps Réel** : Laravel Reverb (Websockets via FrankenPHP)

### 2. Structure des Dossiers
- `/bin` : Contient les binaires (FrankenPHP, MariaDB) et les scripts de contrôle (`start.sh`, `artisan`).
- `/data` : Stockage persistant (Base de données, uploads, logs).
- `/src` : Code source Laravel.
  - `app/Filament` : Ressources d'administration et formulaires partagés.
  - `app/Livewire` : Logique frontend (Home, Profils, Création).
  - `app/Models` : Modèles Eloquent.
  - `database/` : Migrations et Fichier de référence `draft.yaml`.

---

## 📊 Modèle de Données (Core)

Les modèles sont définis via **Blueprint** dans `src/draft.yaml`.

- **User** : Entité centrale. Possède un `trust_score`, une `location` et des `coordinates` (JSON).
- **Circle** : Groupes, Lieux ou Projets. Peuvent être publics ou privés. Ont un `Owner`.
- **CircleMember** : Gère l'appartenance à un cercle (rôles: `admin`, `member`, `guest`).
- **Skill** : Catalogue de compétences (ex: Menuiserie, Développement, Cuisine).
- **Achievement** : Preuve concrète d'une compétence liée à un utilisateur et optionnellement à un Cercle. C'est l'unité de "confiance" du réseau.
- **Message** : Discussions au sein d'un Cercle (type `chat` ou `logistics`).

---

## 🚀 Fonctionnalités Clés

### 1. Recherche Intelligente & Proximité
Le composant `Home` implémente une recherche "Smart" :
- Analyse le contenu des cercles, les compétences des propriétaires et des membres.
- Calcule la distance en temps réel via SQL (Haversine formula sur JSON `coordinates`).
- Priorise les résultats par pertinence sémantique et géographique.

### 2. Gestion des Cercles
- Création et édition via des formulaires Filament injectés dans Livewire.
- Gestion des membres avec système de "Vouching" (recommandation par un tiers).

### 3. Profils Enrichis
- Affichage des "Achievements" (Preuves) pour valider l'expertise de l'utilisateur.
- Edition de profil simplifiée via Filament Forms.

---

## 🛠️ Commandes pour l'Agent IA

Utilisez toujours les wrappers dans `bin/` pour rester dans l'environnement portable :

- **Exécuter une commande Artisan** : `./bin/artisan <command>`
- **Installer une dépendance** : `./bin/composer require <package>`
- **Générer du code via Blueprint** : Modifier `src/draft.yaml` puis `./bin/artisan blueprint:build`
- **Reset la DB & Seed** : `./bin/artisan migrate:fresh --seed`
- **Accès SQL direct** : `./bin/mariadb/bin/mariadb -u root --socket=data/mysql/mysql.sock laravel`

---

## 🚦 État Actuel & Prochaines Étapes
- [x] Structure de base (God Stack) installée.
- [x] Modèles User, Circle, Achievement, Message migrés.
- [x] Dashboard Home avec recherche intelligente opérationnel.

> [!IMPORTANT]
> Ne jamais installer de paquets système (apt/dnf). Toujours utiliser ce qui est présent dans `/bin` ou via Composer/NPM dans `/src`.
