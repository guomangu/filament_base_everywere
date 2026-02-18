# **MANIFESTE "GOD STACK" : Architecture Portable & Autonome**

**Projet :** Réseau Social / Wiki Collaboratif (Portable & Monolithique)  
**Cible :** Agent Développeur Autonome (IA)  
**Philosophie :** "Zero Dependency" - Tout réside dans un dossier unique. Pas de Docker. Pas d'installation système.

---

## **1. L'Objectif**

Construire une application sociale complexe (Fil d'actualité, Interactions temps réel, Gestion de contenu) qui fonctionne comme un exécutable portable. L'utilisateur doit pouvoir copier le dossier sur une clé USB, le lancer sur un autre PC Linux/Mac, et tout doit fonctionner (Base de données, Serveur Web, Websockets).

---

## **2. L'Arsenal Technologique (Les Outils)**

### **Le Cœur (Runtime & Data)**

* **Runtime :** **FrankenPHP** (Binaire statique). Remplace Nginx et PHP-FPM. C'est un serveur web écrit en Go qui embarque l'interpréteur PHP 8.4+.
* **Base de Données :** **MariaDB Generic Linux Binary** (Portable). Pas d'installation apt/yum. Le binaire mariadbd est lancé depuis le dossier /bin du projet.
* **Desktop Wrapper :** **NativePHP**. Permet de transformer cette stack Web en application de bureau native si besoin.

### **Le Framework (Laravel 11)**

* **Backend & Admin :** **FilamentPHP v3**. Utilisé non seulement pour l'admin, mais pour *toute* la structure de données.
* **Frontend & Interaction :** **Livewire v3**. Gestion du DOM et des événements (Likes, Post, Chat) sans écrire de JavaScript.
* **Temps Réel :** **Laravel Reverb**. Serveur WebSocket natif PHP (tourne dans FrankenPHP). Pas de Node.js, pas de Redis obligatoire.
* **CSS :** **TailwindCSS**. Compilé à la volée ou pré-buildé.

---

## **3. Les "Cheat Codes" de Développement**

*Ces outils sont obligatoires pour accélérer le travail de l'Agent IA.*

### **A. Laravel Blueprint (L'Architecte Rapide)**

* **Usage :** Génération de masse.
* **Pourquoi :** Au lieu de créer 50 fichiers à la main (Migration, Model, Controller, Factory), l'Agent rédige un fichier draft.yaml.
* **Commande :** `./bin/artisan blueprint:build`

### **B. Filament Standalone (Frontend Facile)**

* **Usage :** Utiliser les formulaires et tables de l'admin sur le Front-End public.
* **Pourquoi :** L'Agent ne doit pas réinventer la roue HTML pour le formulaire de "Nouveau Post". Il doit injecter le composant `<livewire:post-form />` qui utilise la logique `Filament Form::make()`.

### **C. Sushi (Données Virtuelles)**

* **Usage :** Modèles Eloquent sans base de données (Array Driver).
* **Pourquoi :** Pour les données statiques (ex: Liste des émotions de réaction, Catégories de signalement, Types de badges). Évite de créer des tables SQL inutiles.

### **D. MCP Servers (Debug & Introspection IA)**

* **mariadb-portable** : Accès direct SQL sans terminal. Script : `bin/mcp/mariadb.php`.
* **laravel-context** : Analyse vivante des routes/modèles via `./bin/artisan mcp:serve` (package `innoge/laravel-mcp`).
* **Config** : `/home/guuu/.gemini/antigravity/mcp_config.json`

---

## **4. Structure des Dossiers (L'Architecture Physique)**

```
/PROJECT_ROOT  (= /home/guuu/Documents/filament1/)
├── /bin                  # LES BINAIRES (Le moteur)
│   ├── frankenphp        # Le serveur Web + PHP 8.4
│   ├── mariadbd          # Le serveur SQL portable
│   ├── php               # PHP CLI portable
│   ├── artisan           # Wrapper artisan (TOUJOURS utiliser ça)
│   ├── composer          # Wrapper composer (TOUJOURS utiliser ça)
│   ├── start.sh          # Script de lancement orchestrateur
│   └── /mcp
│       └── mariadb.php   # Script MCP pour accès DB
│
├── /data                 # LE STOCKAGE (La mémoire)
│   ├── mysql             # Dossier de données MariaDB (Ignoré par Git)
│   └── storage           # Uploads Laravel & Logs
│
├── /src                  # LE CODE (L'intelligence)
│   ├── app
│   │   ├── Filament      # Logique Admin & Ressources
│   │   ├── Livewire      # Composants "Full Page" (Flux Social, Profil)
│   │   │   ├── Achievement/  # Create
│   │   │   ├── Circle/       # Profile, Create, Edit
│   │   │   ├── Cv/           # Viewer (user & circle)
│   │   │   ├── Information/  # Manager
│   │   │   ├── Project/      # Index, Show, Create
│   │   │   ├── User/         # Profile, Edit, Claim
│   │   │   └── Home.php      # Page d'accueil avec recherche
│   │   └── Models        # Modèles Eloquent
│   ├── database
│   │   └── draft.yaml    # Fichier Blueprint de référence
│   ├── routes/web.php    # Toutes les routes
│   └── storage/logs/laravel.log  # Logs (PAS dans /data !)
│
├── GUIDE_AGENT_IA.md     # Ce guide (lire EN PREMIER)
└── Manifeste_God_Stack_Agent.md  # Ce manifeste
```

---

## **5. Protocoles de Développement pour l'Agent**

### **Règle 1 : "Monolith First"**

Ne sépare jamais le Front du Back. Utilise **Livewire** pour tout ce qui est dynamique. Si tu as besoin de JavaScript (ex: une carte interactive), utilise **Alpine.js** directement dans le blade.

### **Règle 2 : Polymorphisme Social**

Pour un réseau social, utilise des relations polymorphiques dès le début pour les interactions.

* Trait Reactable (pour les Likes/Emoji).
* Trait Commentable (pour les Commentaires).
* Cela permet de "Liker" un Post, un Commentaire ou un Profil avec le même code.

### **Règle 3 : "Filament Everywhere"**

Si tu dois créer une page de "Paramètres Utilisateur" (Front-End), n'écris pas de HTML input. Crée un composant Livewire qui étend `Filament\Forms\Concerns\InteractsWithForms` et utilise le schema builder de Filament. Cela garantit la cohérence visuelle et la validation.

### **Règle 4 : Le Script de Démarrage (start.sh)**

L'Agent doit maintenir un script bash qui lance les services en parallèle :

1. Lancement de MariaDB (pointant vers ./data/mysql).
2. Attente du socket SQL.
3. Lancement de Reverb (Websockets).
4. Lancement de FrankenPHP (Serveur Web).

### **Règle 5 : Propriétés Livewire (CRITIQUE)**

> [!CAUTION]
> Ne JAMAIS déclarer une propriété publique Livewire pour des données calculées dans `render()`.

```php
// ❌ INTERDIT - cause foreach() null error
public $memberExperts;
public $networkExperts;

// ✅ CORRECT - passer uniquement via return view()
public function render() {
    $memberExperts = User::query()->get();
    return view('...', ['memberExperts' => $memberExperts]);
}
```

### **Règle 6 : Commentaires Blade**

> [!WARNING]
> `{{-- @if ... @foreach ... --}}` NE FONCTIONNE PAS. Les directives Blade sont parsées même dans les commentaires.

Pour désactiver du code Blade temporairement, supprimer le code ou utiliser `@php /* ... */ @endphp`.

### **Règle 7 : Toujours try-catch dans render()**

```php
public function render() {
    try {
        $data = ComplexModel::withRelations()->get();
    } catch (\Exception $e) {
        $data = collect([]);
    }
    return view('...', ['data' => $data ?? collect([])]);
}
```

---

## **6. Commandes Utiles (Memo)**

### **Gestion du Projet**
```bash
# Démarrer le projet
./bin/start.sh

# Arrêter proprement
Ctrl+C dans le terminal du start.sh
```

### **Commandes Laravel**
```bash
# Artisan (TOUJOURS depuis la racine)
./bin/artisan <commande>

# Composer
./bin/composer <commande>

# Migrations (NE PAS utiliser migrate:fresh sans accord utilisateur)
./bin/artisan migrate

# Vider le cache des vues (après modif blade)
./bin/artisan view:clear

# Logs (chemin correct)
tail -f src/storage/logs/laravel.log
```

### **Base de Données**
```bash
# Accès SQL direct (CLI)
./bin/mariadb/bin/mariadb -u root --socket=data/mysql/mysql.sock laravel

# ⚠️ NE JAMAIS faire migrate:fresh sans accord explicite de l'utilisateur
```

### **Admin & Users**
```bash
# Créer un utilisateur Filament
./bin/artisan make:filament-user

# URL Admin
http://localhost:8000/admin
```

---

## **7. Design System "God Stack Premium"**

### Principes Visuels
- **Glassmorphism** : `bg-white/60 backdrop-blur-3xl border border-white/60`
- **Arrondis généreux** : `rounded-[2.5rem]` à `rounded-[3rem]`
- **Typographie** : `font-black uppercase tracking-tight` pour les titres
- **Hover effects** : `hover:scale-[1.02] hover:shadow-2xl transition-all duration-300`
- **Badges statut** : Avec `animate-pulse` pour les états actifs
- **Stats grids** : Couleurs sémantiques (blue=offres, purple=demandes, green=positif)

### Palette de Couleurs
- Fond principal : `bg-gradient-to-br from-slate-50 to-blue-50/30`
- Cards : `bg-white/60 backdrop-blur-3xl`
- Accent primaire : `blue-500/600`
- Texte principal : `text-slate-900 font-black`
- Texte secondaire : `text-slate-400 text-[9px] uppercase`

---

## **8. Extensions MCP (Agent Enhanced)**

Ce projet inclut ses propres serveurs MCP pour augmenter les capacités de l'Agent IA :

* **mariadb-portable** : Permet à l'Agent d'interroger la base de données directement via SQL sans passer par le terminal.
    * *Script :* `bin/mcp/mariadb.php`
    * *Usage :* Automatiquement configuré dans votre client MCP.

* **laravel-context** : Analyse vivante du projet Laravel (routes, modèles, composants).
    * *Package :* `innoge/laravel-mcp` (installé en dev)
    * *Commande :* `./bin/artisan mcp:serve`
    * *Config :* `/home/guuu/.gemini/antigravity/mcp_config.json`