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
- **Proche** : Profil "géré" par un utilisateur parent. Permet de référencer l'expertise de son réseau sans que les personnes n'aient de compte.
- **Circle** : Groupes, Lieux ou Projets. Peuvent être publics ou privés. Ont un `Owner`.
- **Achievement** : Preuve concrète d'une compétence liée à un `User` OU un `Proche`. C'est l'unité de "confiance" du réseau.
- **Information** : Système polymorphique (`morphMany`) pour ajouter des métadonnées dynamiques (liens, horaires, labels) sur n'importe quel modèle.
- **Vouch** : Système de garantie mutuelle entre utilisateurs pour augmenter le `trust_score`.

---

## 🚀 Fonctionnalités Clés

### 1. Recherche Universelle & Proximité
Le composant `Home` implémente une recherche "Smart" :
- Analyse le contenu des cercles, les compétences des propriétaires, des membres et de leurs **Proches**.
- Calcule la distance en temps réel via SQL (Haversine sur JSON `coordinates`).
- Contexte de matching clair : indique si l'expertise vient du fondateur, d'un membre ou d'un proche géré.

### 2. Gestion de l'Expertise Réseau (Proches)
- Un utilisateur peut créer des profils `Proche` pour son entourage.
- Attribution de compétences et preuves aux proches.
- **Claim Strategy** : Système de transfert sécurisé (Token + Code) pour qu'un proche devienne un utilisateur autonome en récupérant tout son historique.

### 3. UI/UX "God Stack Premium"
- Design basé sur le **Glassmorphism** (bordures blanches translucides, flous de fond 3xl).
- Responsive total de Desktop à Smartphone.
- Utilisation de composants Filament Forms injectés pour les interactions complexes.

---

## 🛠️ Commandes pour l'Agent IA

Utilisez toujours les wrappers dans `bin/` pour rester dans l'environnement portable :

- **Exécuter une commande Artisan** : `./bin/artisan <command>`
- **Générer du code via Blueprint** : Modifier `src/draft.yaml` puis `./bin/artisan blueprint:build`
- **Reset la DB & Seed** : `./bin/artisan migrate:fresh --seed`
- **Tests de validation** : `./bin/php src/vendor/bin/phpunit -c src/phpunit.xml`

---

## 🚦 État Actuel & Prochaines Étapes
- [x] Structure de base (God Stack) installée.
- [x] Modèles User, Circle, Achievement, Message migrés.
- [x] Système de **Proches** et transfert de compte opérationnel.
- [x] Recherche étendue incluant le Vivier d'Expertises des Proches.
- [x] Système d'Information polymorphique stabilisé.
- [ ] Amélioration continue de l'UX/UI responsive (En cours).

> [!IMPORTANT]
> Ne jamais installer de paquets système (apt/dnf). Toujours utiliser ce qui est présent dans `/bin` ou via Composer/NPM dans `/src`.
