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
- **Project** : Entité temporaire de service. Statut `is_open` (boolean). Relations : `owner`, `members`, `activeMembers`, `offers`, `demands`, `reviews`.
- **ProjectMember** : Relation polymorphique (User OU Proche). Statuts : `pending`, `active`, `inactive`. Rôles : `admin`, `member`.
- **ProjectOffer** : Offres ET demandes (champ `type`). Liées aux Skills via pivot `project_offer_skill`.
- **ProjectReview** : Avis avec `parent_id` pour les réponses imbriquées. Types : `validate`, `reject`.

---

## 🗺️ Routes Publiques

| URL | Nom | Composant |
|-----|-----|-----------|
| `/` | - | `Livewire\Home` |
| `/users/{user}` | `users.show` | `Livewire\User\Profile` |
| `/circles` | `circles.index` | `CircleController@index` |
| `/circles/{circle}` | `circles.show` | `Livewire\Circle\Profile` |
| `/cv/u/{user}` | `cv.user` | `Livewire\Cv\Viewer` |
| `/cv/c/{circle}` | `cv.circle` | `Livewire\Cv\Viewer` |

### Routes Authentifiées
| URL | Nom | Composant |
|-----|-----|-----------|
| `/circles/create` | `circles.create` | `Livewire\Circle\Create` |
| `/circles/{circle}/edit` | `circles.edit` | `Livewire\Circle\Edit` |
| `/achievements/create` | `achievements.create` | `Livewire\Achievement\Create` |
| `/profile/edit` | `profile.edit` | `Livewire\User\Edit` |
| `/projects` | `projects.index` | `Livewire\Project\Index` |
| `/projects/create` | `projects.create` | `Livewire\Project\Create` |
| `/projects/{project}` | `projects.show` | `Livewire\Project\Show` |
| `/proches/claim/{token}` | `proches.claim` | `Livewire\User\Claim` |

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

### 3. Système Project (Marketplace)
- Projets temporaires avec offres et demandes de services.
- Membres polymorphiques (Users et Proches).
- Intégré dans les profils Circle (section "Projets Actifs").
- Avis imbriqués (validate/reject avec réponses).

### 4. CV Partageable
- URL publique `/cv/u/{user}` et `/cv/c/{circle}`.
- Pour les circles : agrège les compétences de tous les membres.
- Optimisé pour export PDF A4.

### 5. UI/UX "God Stack Premium"
- Design basé sur le **Glassmorphism** (bordures blanches translucides, flous de fond 3xl).
- Responsive total de Desktop à Smartphone.
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
- [x] **Système Project** (Marketplace) : Modèles, migrations, composants Livewire, routes.
- [x] **Intégration Circle Profile** : Section "Projets Actifs" dans "Le Vivier d'Expertises".
- [x] **MCP Servers** configurés (mariadb-portable + laravel-context).
- [ ] Vues Blade pour Project (Index, Show, Create) à créer.
- [ ] Intégration Projets dans profil User.
- [ ] Amélioration continue de l'UX/UI responsive.

> [!IMPORTANT]
> Ne jamais installer de paquets système (apt/dnf). Toujours utiliser ce qui est présent dans `/bin` ou via Composer/NPM dans `/src`.
