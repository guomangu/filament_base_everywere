# **MANIFESTE "GOD STACK" : Architecture Portable & Autonome**

**Projet :** Réseau Social / Wiki Collaboratif (Portable & Monolithique) **Cible :** Agent Développeur Autonome (IA) **Philosophie :** "Zero Dependency" \- Tout réside dans un dossier unique. Pas de Docker. Pas d'installation système.

## **1\. L'Objectif**

Construire une application sociale complexe (Fil d'actualité, Interactions temps réel, Gestion de contenu) qui fonctionne comme un exécutable portable. L'utilisateur doit pouvoir copier le dossier sur une clé USB, le lancer sur un autre PC Linux/Mac, et tout doit fonctionner (Base de données, Serveur Web, Websockets).

## **2\. L'Arsenal Technologique (Les Outils)**

### **Le Cœur (Runtime & Data)**

* **Runtime :** **FrankenPHP** (Binaire statique). Remplace Nginx et PHP-FPM. C'est un serveur web écrit en Go qui embarque l'interpréteur PHP.  
* **Base de Données :** **MariaDB Generic Linux Binary** (Portable). Pas d'installation apt/yum. Le binaire mariadbd est lancé depuis le dossier /bin du projet.  
* **Desktop Wrapper :** **NativePHP**. Permet de transformer cette stack Web en application de bureau native si besoin.

### **Le Framework (Laravel 11\)**

* **Backend & Admin :** **FilamentPHP v3**. Utilisé non seulement pour l'admin, mais pour *toute* la structure de données.  
* **Frontend & Interaction :** **Livewire v3**. Gestion du DOM et des événements (Likes, Post, Chat) sans écrire de JavaScript.  
* **Temps Réel :** **Laravel Reverb**. Serveur WebSocket natif PHP (tourne dans FrankenPHP). Pas de Node.js, pas de Redis obligatoire.  
* **CSS :** **TailwindCSS**. Compilé à la volée ou pré-buildé.

## **3\. Les "Cheat Codes" de Développement**

*Ces outils sont obligatoires pour accélérer le travail de l'Agent IA.*

### **A. Laravel Blueprint (L'Architecte Rapide)**

* **Usage :** Génération de masse.  
* **Pourquoi :** Au lieu de créer 50 fichiers à la main (Migration, Model, Controller, Factory), l'Agent rédige un fichier draft.yaml.  
* **Commande :** php artisan blueprint:build

### **B. Filament Standalone (Frontend Facile)**

* **Usage :** Utiliser les formulaires et tables de l'admin sur le Front-End public.  
* **Pourquoi :** L'Agent ne doit pas réinventer la roue HTML pour le formulaire de "Nouveau Post". Il doit injecter le composant \<livewire:post-form /\> qui utilise la logique Filament Form::make().

### **C. Sushi (Données Virtuelles)**

* **Usage :** Modèles Eloquent sans base de données (Array Driver).  
* **Pourquoi :** Pour les données statiques (ex: Liste des émotions de réaction, Catégories de signalement, Types de badges). Évite de créer des tables SQL inutiles.

### **D. Spatie Ray (Debug IA)**

* **Usage :** Debuggage visuel.  
* **Pourquoi :** L'Agent peut utiliser ray($data) au lieu de dd(). Si l'Agent est bloqué, copier-coller le log structuré de Ray est plus efficace que lire des logs Laravel bruts.

## **4\. Structure des Dossiers (L'Architecture Physique)**

Le projet doit respecter cette structure "Portable" stricte.  
`/PROJECT_ROOT`  
`├── /bin                  # LES BINAIRES (Le moteur)`  
`│   ├── frankenphp        # Le serveur Web + PHP`  
`│   ├── mariadbd          # Le serveur SQL portable`  
`│   └── start.sh          # Script de lancement orchestrateur`  
`│`  
`├── /data                 # LE STOCKAGE (La mémoire)`  
`│   ├── mysql             # Dossier de données MariaDB (Ignoré par Git)`  
`│   └── storage           # Uploads Laravel & Logs`  
`│`  
`├── /src                  # LE CODE (L'intelligence)`  
`│   ├── app`  
`│   │   ├── Filament      # Logique Admin & Ressources`  
`│   │   ├── Livewire      # Composants "Full Page" (Flux Social, Profil)`  
`│   │   └── Models        # Modèles Eloquent (User, Post, Comment)`  
`│   ├── database`  
`│   │   └── schema.yaml   # Fichier Blueprint de référence`  
`│   └── ... (Structure Laravel standard)`  
`│`  
`└── composer.json`

## **5\. Protocoles de Développement pour l'Agent**

### **Règle 1 : "Monolith First"**

Ne sépare jamais le Front du Back. Utilise **Livewire** pour tout ce qui est dynamique. Si tu as besoin de JavaScript (ex: une carte interactive), utilise **Alpine.js** directement dans le blade.

### **Règle 2 : Polymorphisme Social**

Pour un réseau social, utilise des relations polymorphiques dès le début pour les interactions.

* Trait Reactable (pour les Likes/Emoji).  
* Trait Commentable (pour les Commentaires).  
* Cela permet de "Liker" un Post, un Commentaire ou un Profil avec le même code.

### **Règle 3 : "Filament Everywhere"**

Si tu dois créer une page de "Paramètres Utilisateur" (Front-End), n'écris pas de HTML input. Crée un composant Livewire qui étend Filament\\Forms\\Concerns\\InteractsWithForms et utilise le schema builder de Filament. Cela garantit la cohérence visuelle et la validation.

### **Règle 4 : Le Script de Démarrage (start.sh)**

L'Agent doit maintenir un script bash qui lance les services en parallèle :

1. Lancement de MariaDB (pointant vers ./data/mysql).  
2. Attente du socket SQL.  
3. Lancement de Reverb (Websockets).  
4. Lancement de FrankenPHP (Serveur Web).

## **6\. Prompt de Démarrage (À copier-coller à l'Agent)**

"Tu es un Architecte Senior Laravel spécialisé en 'God Stack'. Nous créons un **Réseau Social Portable**. **Stack :** Laravel 11, Filament v3, Livewire, FrankenPHP, MariaDB Portable. **Cheatcodes actifs :** Blueprint, Sushi, Reverb.  
**Ta première mission :**

1. Initialise la structure de fichiers mentale selon le Manifeste.  
2. Rédige le fichier draft.yaml (Blueprint) pour générer les modèles suivants :  
   * User (avec Avatar, Bio).  
   * Post (Contenu riche, polymorphe).  
   * Comment (Hierarchique).  
   * Reaction (Type Sushi pour les emojis, relation polymorphe).  
3. Prépare le script start.sh pour orchestrer MariaDB et FrankenPHP."

## **7\. Commandes Utiles (Memo)**

### **Gestion du Projet**
* **Démarrer le projet :** `./bin/start.sh` (Lance MariaDB, FrankenPHP, Reverb)
* **Installer le projet (après git clone) :** `./bin/install.sh`
* **Arrêter proprement :** `Ctrl+C` dans le terminal du start.sh

### **Commandes Laravel (Simplifiées)**
*Grâce aux wrappers `bin/artisan` et `bin/composer`, plus besoin de `cd src`.*

* **Artisan :** `./bin/artisan <commande>`
  * *Ex: `./bin/artisan make:model Post -m`*
  * *Ex: `./bin/artisan migrate`*
* **Composer :** `./bin/composer <commande>`
  * *Ex: `./bin/composer require creating/filament`*
* **Tinker :** `./bin/artisan tinker`

### **Base de Données**
* **Accès SQL (CLI) :** `./bin/mariadb/bin/mariadb -u root --socket=data/mysql/mysql.sock laravel`
* **Reset Database :** `./bin/artisan migrate:fresh --seed`

### **Admin & Users**
* **Créer un utilisateur Filament :** `./bin/artisan make:filament-user`
* **URL Admin :** `http://localhost:8000/admin`

## **8\. Extensions MCP (Agent Enhanced)**

Ce projet inclut ses propres serveurs MCP pour augmenter les capacités de l'Agent IA :

*   **mariadb-portable** : Permet à l'Agent d'interroger la base de données directement via SQL sans passer par le terminal.
    *   *Source :* `bin/mcp/mariadb.php`
    *   *Usage :* Automatiquement configuré dans votre client MCP.