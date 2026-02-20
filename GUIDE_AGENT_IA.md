# 🤖 GUIDE DE L'AGENT IA : Architecture & Fonctionnement

Ce document est destiné aux agents IA autonomes travaillant sur ce projet. Il résume l'état actuel, la stack technique et les protocoles de développement.

## 🌟 Philosophie du Projet : "God Stack"
Le projet suit le manifeste **God Stack** : une application **ultra-portable**, **monolithique** et **indépendante**.
- **Zéro Dépendance Système** : Tout est dans le dossier (Binaires inclusivement).
- **Proximité & Confiance** : Un réseau social basé sur la géolocalisation et la vérification des compétences.
- **Vitesse de Développement** : Utilisation intensive de Blueprint et Filament pour minimiser le code répétitif.

---

## 🏗️ Architecture Technique

### 1. La Stack "Portable"
- **Binaire Serveur** : `bin/frankenphp` (Serveur Web + Runtime PHP 8.4+)
- **Base de Données** : `bin/mariadbd` (MariaDB portable, données dans `data/mysql`)
- **Framework** : Laravel 11
- **UI & Interaction** : Filament v3 (Admin & Formulaires) + Livewire v3 (Composants Full-Page)
- **Temps Réel** : Laravel Reverb (Websockets via FrankenPHP)

### 2. Structure des Dossiers
- `/bin` : Contient les binaires (FrankenPHP, MariaDB, PHP) et les scripts de contrôle (`start.sh`, `artisan`, `composer`).
- `/bin/mcp/` : Scripts MCP pour accès direct à la DB (`mariadb.php`).
- `/data` : Stockage persistant (Base de données, uploads, logs).
- `/src` : Code source Laravel.
  - `app/Filament` : Ressources d'administration et formulaires partagés.
  - `app/Livewire` : Logique frontend (Home, Profils, Création).
  - `app/Models` : Modèles Eloquent.
  - `database/` : Migrations et Fichier de référence `draft.yaml`.

---

## 📊 Modèle de Données (Complet)

### Modèles Core
- **User** : Entité centrale. Possède un `trust_score`, une `location` et des `coordinates` (JSON). Relations : `ownedProjects`, `projectMemberships`, `achievements`, `proches`, `circleMembers`, `vouches`.
- **Proche** : Profil "géré" par un utilisateur parent. Permet de référencer l'expertise de son réseau sans que les personnes n'aient de compte.
- **Circle** : Groupes, Lieux ou Projets. Peuvent être publics ou privés. Ont un `Owner`. Relations : `owner`, `members`, `activeMembers`, `achievements`, `messages`, `informations`.
- **CircleMember** : Pivot entre User et Circle. Statuts : `pending`, `active`, `inactive`. Rôles : `owner`, `admin`, `member`.
- **Achievement** : Preuve concrète d'une compétence liée à un `User` OU un `Proche`. C'est l'unité de "confiance" du réseau.
- **AchievementValidation** : Validation d'un Achievement par un autre User. Types : `validate`, `reject`.
- **Skill** : Compétence référencée. Liée aux Achievements et ProjectOffers.
- **Information** : Système polymorphique (`morphMany`) pour ajouter des métadonnées dynamiques (liens, horaires, labels) sur n'importe quel modèle.
- **Vouch** : Système de garantie mutuelle entre utilisateurs pour augmenter le `trust_score`.
- **Message** : Messages dans les Circles. Lié à un `sender` (User) et un `circle`.

### Modèles Project (Marketplace)
- **Project** : Entité principale. Statut `is_open` (boolean). Relations : `owner`, `members`, `activeMembers`, `offers`, `demands`, `reviews`, `messages`.
- **ProjectMember** : Relation polymorphique (User OU Proche). Statuts : `pending`, `active`, `inactive`. Rôles : `admin`, `member`.
- **ProjectOffer** : Offres ET demandes (champ `type`). C'est l'entité centrale de présentation sur l'accueil et les profils. Liées aux Skills via pivot `project_offer_skill`.
- **ProjectReview** : Avis avec `parent_id` pour les réponses imbriquées. Types : `validate`, `reject`.

---

## 🗺️ Pages et Vues Principales (Navigation & Composants)

La navigation du site est pensée pour être unifiée. Le menu principal (`navbar`) permet de jongler entre son Profil, ses Réseaux, et la Messagerie Globale.

### 1. Page d'Accueil (`/` -> `Livewire\Home`)
- **Vocation** : Hub central de découverte de proximité. Montre les **Offres de services (ProjectOffers)** et les **Cercles** autour de l'utilisateur.
- **Composants clés** : 
  - **Recherche Smart** : Filtre instantanément par mot clé.
  - **Tri Géospatial** : Les résultats sont ordonnés par distance (SQL Haversine sur les coordonnées).
  - **Cartes Détaillées** : Utilise le composant `<x-offer-card>` pour les offres et un design de carte compact pour les Cercles affichant la distance, les membres et leurs **compétences**.
- **UX** : Grille glassmorphism responsivée (2 colonnes mobile, 4 desktop).

### 2. Profil Utilisateur (`/users/{user}` -> `Livewire\User\Profile`)
- **Vocation** : CV vivant de l'utilisateur.
- **Composants clés** :
  - **Header & Trust Score** : Met en avant le score de confiance et les statistiques (Projets, Preuves, Offres). Un bouton "MESSAGE" ouvre directement un chat privé avec cet utilisateur.
  - **Le Réseau (Trust Network)** : Affiche les Cercles de l'utilisateur (avec bouton de création en haut pour l'owner) et ses Proches gérés.
  - **Expertises & Réalisations** : Liste des compétences (Skills) validées par des "Achievements" (Preuves). Présence d'un bouton "Générer mon CV PDF" (`/cv/u/{user}`) à la fin de cette section.
  - **Offres de l'Utilisateur** : Grille listant toutes les offres (`ProjectOffer`) actives de l'utilisateur, permettant l'interaction (Devis, Avis) via `HandlesOfferActions`.

### 3. Profil de Cercle (`/circles/{circle}` -> `Livewire\Circle\Profile`)
- **Vocation** : Page de communauté ou de hub d'entreprise.
- **Composants clés** :
  - **Informations & Réseaux** : Section `polymorphic` pour afficher les horaires, sites web, etc. Affiche également "l'Explorateur de Réseau" (`Network\Explorer.php`) listant d'autres cercles connexes.
  - **Le Vivier d'Expertises** : Affiche tous les membres, proches inclus, et leurs compétences cumulées. Inclut une section **Projets Actifs** qui liste les `ProjectOffer` liés aux projets des membres du cercle.
  - **Le Board (Interface Chat)** : Espace de communication interne du cercle. Les messages sont affichés *chronologiquement* (du plus ancien au plus récent de haut en bas), avec un champ de texte fixé en bas, contenu dans un bloc scorllable avec *Alpine.js auto-scroll*. Distingue visuellement le Fondateur des Membres.

### 4. Page de Projet / Offre (`/projects/{project}` -> `Livewire\Project\Show`)
- **Vocation** : Espace collaboratif temporaire lié à des offres spécifiques.
- **Composants clés** :
  - **Amazon-style Header** : Présente le fondateur, les membres, l'adresse, et l'état d'ouverture (Open/Closed).
  - **Onglets (Tabs)** : Navigation fluide entre la "Description globale", les "Offres de services", les "Besoins" et les "Avis".
  - **Le Forum (Interface Chat)** : Identique à l'interface "Le Board" des cercles. C'est l'espace de messagerie instantanée des membres du projet. L'entrée de message est au-dessus du flux chronologique, géré par Alpine.js.

### 5. Messagerie Globale (Overlay `GlobalMessaging.php`)
- **Vocation** : Le "Direct Message" et le hub de notifications, accessible via le bouton rond (bulle) de la barre de navigation. 
- **Composants clés** :
  - **Overlay 100%** : Glisse sur l'écran sans le recharger.
  - **Navigation par Tiroir (Desktop)** : Une colonne gauche pour choisir une conversation "Privée" (Messages directs 1-to-1) ou "Forums" (Les Cercles ou Projets). La colonne droite affiche le chat.
  - **Interface Mobile** : Passe automatiquement d'une liste de conversations au chat plein écran lors d'une sélection.

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

### 3. Système Project (Marketplace / Offres)
- Les **Offres de services** (`ProjectOffer`) sont désormais au centre de l'UX (affichées sur l'accueil et les profils visiteurs).
- **Architecture de Code unifiée** : Utilisation du trait `App\Traits\HandlesOfferActions` et de la vue partagée `livewire.offers.modals` pour gérer les devis et les avis depuis n'importe où (Home, Profil, Projet, Cercle), évitant la duplication de code et la redondance des modales.
- Avis imbriqués (validate/reject avec réponses).

### 4. Interfaces de Chat Intégrées (Le Board / Le Forum)
- Les espaces de discussion des Cercles ("Le Board") et des Projets ("Le Forum") fonctionnent comme des chats temps réel en bas de page.
- **Scroll & Ordre** : Messages en ordre chronologique avec saisie en bas. Un composant Alpine.js gère le scroll automatique vers le dernier message à l'ouverture ou à l'envoi.
- **Rôles** : L'interface distingue subtilement les fondateurs/créateurs des membres via l'UI.

### 5. CV Partageable
- URL publique `/cv/u/{user}` et `/cv/c/{circle}`.
- Pour les circles : agrège les compétences de tous les membres.
- Optimisé pour export PDF A4.

### 6. UI/UX "God Stack Premium"
- Design basé sur le **Glassmorphism** (bordures blanches translucides, flous de fond 3xl).
- Responsive total de Desktop à Smartphone (Layouts fluides en 2, 3 ou 4 colonnes).
- Utilisation de composants Filament Forms injectés pour les interactions complexes.

---

## 🛠️ Commandes pour l'Agent IA

> [!IMPORTANT]
> Utilisez **TOUJOURS** les wrappers dans `bin/` depuis la racine `/home/guuu/Documents/filament1/`.
> Ne jamais faire `cd src` puis `php artisan`. Utiliser `./bin/artisan` depuis la racine.

```bash
# Artisan
./bin/artisan <command>

# Composer
./bin/composer <command>

# Migrations
./bin/artisan migrate

# Vider le cache des vues (OBLIGATOIRE après modification de .blade.php)
./bin/artisan view:clear

# Accès SQL direct
./bin/mariadb/bin/mariadb -u root --socket=data/mysql/mysql.sock laravel

# Logs Laravel
tail -f src/storage/logs/laravel.log
```

---

## ⚠️ PIÈGES CRITIQUES À ÉVITER

> [!CAUTION]
> Ces erreurs ont été rencontrées et documentées. Les éviter absolument.

### 1. Propriétés Publiques Livewire Non Initialisées
**Problème** : Déclarer `public $memberExperts;` dans un composant Livewire sans l'initialiser cause `foreach() argument must be of type array|object, null given` dans la vue.

**Règle** : Ne JAMAIS déclarer une propriété publique Livewire qui est calculée dans `render()`. Ces variables doivent être **uniquement** passées via le `return view(...)` de `render()`.

```php
// ❌ MAUVAIS - cause des erreurs null
class Profile extends Component {
    public $memberExperts; // JAMAIS ça si calculé dans render()
    
    public function render() {
        $memberExperts = User::all();
        return view('...', ['memberExperts' => $memberExperts]);
    }
}

// ✅ BON - pas de propriété publique pour les données calculées
class Profile extends Component {
    public function render() {
        $memberExperts = User::all();
        return view('...', ['memberExperts' => $memberExperts]);
    }
}
```

### 2. Commentaires Blade avec Directives
**Problème** : `{{-- @if ... @foreach ... @endif --}}` ne fonctionne PAS. Les directives Blade à l'intérieur de commentaires Blade sont quand même parsées et causent des erreurs de syntaxe.

**Solution** : Pour désactiver du code Blade, utiliser `@php /* ... */ @endphp` ou simplement supprimer le code.

### 3. Commandes Artisan hors Environnement Portable
**Problème** : `php artisan` ou `cd src && php artisan` échoue car PHP système n'est pas configuré.
**Solution** : Toujours `./bin/artisan` depuis la racine du projet.

### 4. `php artisan optimize:clear` échoue
**Problème** : `optimize:clear` tente de vider le cache DB et échoue avec `Class "PDO" not found`.
**Solution** : Utiliser uniquement `./bin/artisan view:clear` pour les vues.

### 5. Requêtes dans render() sans try-catch
**Problème** : Si une requête DB échoue dans `render()`, la page plante entièrement.
**Solution** : Toujours entourer les requêtes complexes dans `render()` d'un try-catch qui retourne des collections vides.

```php
public function render() {
    try {
        $data = Model::complexQuery()->get();
    } catch (\Exception $e) {
        $data = collect([]);
    }
    return view('...', ['data' => $data ?? collect([])]);
}
```

---

## 🎨 Protocoles UX & Interaction

### 1. La Règle du "Petit à Petit" (Progressive UX)
**Principe** : Ne jamais forcer l'utilisateur à remplir un formulaire massif au démarrage.
- **Création Express** : Permettre la création d'une entité (Projet, Cercle) avec le strict minimum (Titre).
- **Enrichissement In-Place** : L'utilisateur doit pouvoir ajouter des briques (Offres, Demandes, Preuves) directement depuis la page de consultation ("Je crée, je vois, je crée, je vois").
- **Dynamisme** : Utiliser Livewire pour que chaque ajout soit instantané sans rechargement de page.

---

## 🔌 MCP Servers Configurés

Le projet utilise des serveurs MCP pour augmenter les capacités de l'Agent IA :

### `mariadb-portable`
- **Script** : `bin/mcp/mariadb.php`
- **Usage** : Accès direct en lecture/écriture à la DB sans terminal.
- **Config** : `/home/guuu/.gemini/antigravity/mcp_config.json`

### `laravel-context` (innoge/laravel-mcp)
- **Package** : `innoge/laravel-mcp` (installé en dev)
- **Commande** : `./bin/artisan mcp:serve`
- **Usage** : Analyse vivante des Routes, Modèles, et Composants.

---

## 🚦 État Actuel & Prochaines Étapes
- [x] Structure de base (God Stack) installée.
- [x] Modèles User, Circle, Achievement, Message migrés.
- [x] Système de **Proches** et transfert de compte opérationnel.
- [x] Recherche étendue incluant le Vivier d'Expertises des Proches.
- [x] Système d'Information polymorphique stabilisé.
- [x] **Système Project** (Marketplace) : Affichage d'offres unifié. Trait HandleOfferActions implémenté.
- [x] **Interfaces de Chat** : "Le Board" et "Le Forum" restructurés avec Auto-scroll Alpine.js.
- [x] **Messagerie Globale** : Tiroir de navigation latéral pour les messages privés.
- [x] **SEO** : Dynamic tags and metadata configuration with localisation rules.
- [x] **Réseau de Confiance** : Affichage des "trust paths" transparent via le composant `user-trust-chain`.
- [x] **MCP Servers** configurés (mariadb-portable + laravel-context).
- [ ] Refonte du système de Notifications (Broadcast réel vs Polling).
- [ ] Outils de modération (Signalements).

> [!IMPORTANT]
> Ne jamais installer de paquets système (apt/dnf). Toujours utiliser ce qui est présent dans `/bin` ou via Composer/NPM dans `/src`.
