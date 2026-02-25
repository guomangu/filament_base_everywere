# 🤖 GUIDE DE L'AGENT IA : Architecture & Fonctionnement

Ce document est destiné aux agents IA autonomes travaillant sur ce projet. Il résume la stack technique et les protocoles de développement.

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

## �️ Bonnes Pratiques de Développement & UX

### 1. Développement Modulaire et Atomique
**Principe** : Découper l'interface en petits composants réutilisables (Blade ou Livewire).
- Éviter les vues monolithiques de +500 lignes.
- Extraire les parties répétitives (cartes, boutons, modales) dans `resources/views/components/`.
- Chaque composant doit avoir une responsabilité unique et claire.

### 2. Normage et Convention de Nommage
**Principe** : Garder une arborescence propre et prévisible.
- **Vues** : Nommer les fichiers en minuscules avec des tirets (kebab-case) : `user-profile.blade.php`.
- **Composants Livewire** : Utiliser le PascalCase pour la classe (`UserProfile`) et kebab-case pour la vue (`livewire.user-profile`).
- **Composants Blade** : Préfixer les appels avec `x-` et utiliser le kebab-case (ex: `<x-project-card />`).

### 3. Respect des Standards du Dev
**Principe** : Coder proprement et défensivement.
- **Typage Strict** : Utiliser les types de retour PHP et le typage des propriétés.
- **Gestion d'Erreurs** : Anticiper les retours nuls depuis la BDD ou les erreurs d'API.
- **Performance** : Limiter les requêtes N+1 avec `with()` dans Eloquent.

### 4. Penser l'Experience Utilisateur (UX) par le "Petit à Petit" (Progressive UX)
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

> [!IMPORTANT]
> Ne jamais installer de paquets système (apt/dnf). Toujours utiliser ce qui est présent dans `/bin` ou via Composer/NPM dans `/src`.
