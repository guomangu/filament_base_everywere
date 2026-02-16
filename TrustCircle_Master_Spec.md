# **PROMPT MAÎTRE : PROJET "TRUSTCIRCLE" (LE CARNET)**

## **1\. Vue d'ensemble & Philosophie**

Nous construisons un **Réseau Social de Confiance** (TrustCircle/Le Carnet).

* **Concept :** Contrairement à LinkedIn, les connexions sont validées par "Vouching" (recommandation). On ne s'ajoute pas, on se fait introduire.  
* **Objectif :** Créer des chaînes de confiance vérifiées pour des recrutements ou collaborations locales hyper-fiables (ex: Restauration, BTP, Intérim).

## **2\. Stack Technique (The God Stack)**

L'agent doit respecter scrupuleusement cette stack :

* **Backend :** Laravel 11\.  
* **Frontend :** Blade \+ Livewire 3 (Interactivité asynchrone) \+ TailwindCSS.  
* **Admin/App Dashboard :** FilamentPHP v3.  
* **DB Scaffolding :** Laravel Blueprint.  
* **Base de données :** SQLite (Dev) / MySQL (Prod).

## **3\. Modélisation des Données (Blueprint)**

Utilise ce schéma YAML pour générer la structure de la base de données. Il définit les entités clés : User (Entrepreneur), Circle (Lieu/Entité), Skill (Compétence), Achievement (Preuve), et Message.  
`models:`  
  `User:`  
    `name: string`  
    `email: string unique`  
    `avatar_url: string nullable`  
    `bio: text nullable`  
    `location: string nullable comment:"Ville principale (ex: Colmar)"`  
    `coordinates: json nullable comment:"{lat: ..., lng: ...}"`  
    `trust_score: integer default:0`  
    `relationships:`  
      `hasMany: OwnedCircles:Circle, Achievements, SentMessages:Message, ReceivedMessages:Message`  
      `belongsToMany: JoinedCircles:Circle (pivot:role,status,vouched_by_id)`

  `Circle:`  
    `name: string`  
    `type: enum:business,event,place`  
    `description: text nullable`  
    `address: string`  
    `coordinates: json nullable`  
    `owner_id: id foreign:users`  
    `relationships:`  
      `belongsTo: Owner:User`  
      `hasMany: Members:User, Messages`  
      `hasManyThrough: Achievements:Achievement`

  `Skill:`  
    `name: string index`  
    `category: string nullable`  
    `relationships:`  
      `hasMany: Achievements`

  `Achievement:`  
    `title: string comment:"Ex: Sushi Saumon Avocat"`  
    `user_id: id foreign:users`  
    `skill_id: id foreign:skills`  
    `circle_id: id foreign:circles nullable comment:"Le lieu où la compétence a été exercée/prouvée"`  
    `media_url: string nullable`  
    `metadata: json nullable comment:"{price: '30euros', info: 'très pimenté'}"`  
    `is_verified: boolean default:false`  
    `relationships:`  
      `belongsTo: User, Skill, Circle`

  `Message:`  
    `circle_id: id foreign:circles nullable`  
    `sender_id: id foreign:users`  
    `title: string nullable`  
    `content: text`  
    `type: enum:chat,logistics`  
    `metadata: json nullable comment:"{link_trajet: 'GoogleMaps', dispo: '5h'}"`  
    `relationships:`  
      `belongsTo: Circle, Sender:User`

## **4\. Spécifications UI/UX (Livewire Components)**

L'application se divise en 3 pages principales gérées par des composants Livewire Full-Page.

### **PAGE 1 : Racine / Home (/)**

**Route :** Route::get('/', Home::class);  
**Structure de la Vue :**

1. **Formulaire Asynchrone (Hero Section) :**  
   * Input large : "Que cherchez-vous ?" (ex: Sushi, Plombier).  
   * Input secondaire : "Où ?" (ex: Colmar).  
   * *Comportement :* wire:model.live.debounce.300ms="search" déclenche la recherche instantanée sans rechargement.  
2. **Résultats de Recherche (Liste Mixte) :**  
   * Affichage dynamique des résultats sous l'input.  
   * **Logique :** Recherche dans Cercles (par nom) ET Compétences (via Achievements).  
   * *Format :* Carte simple "Cercle \+ Entité trouvée" (ex: "Restaurant de Dams (Cercle) \- Contient : Sushi (Compétence)").  
3. **Mur de Cercles Locaux (Grille par défaut) :**  
   * Si pas de recherche, afficher les cercles autour de l'utilisateur (ou en vedette).  
   * *Composant Card :*  
     * Titre du Cercle ("Restaurant de Dams").  
     * Stack technique/Tags ("Cuisine Japonaise", "Soirée").  
     * Avatar de l'Admin.

### **PAGE 2 : Page Cercle (/circle/{circle})**

**Route :** Route::get('/circle/{circle}', ShowCircle::class);  
**Structure de la Vue :**

1. **Header (Info Cercle) :**  
   * Titre, Image de couverture, Adresse (Lien Maps).  
   * Statut (Ouvert/Fermé/Dispo dès 2h).  
   * Bouton d'action : "Demander à rejoindre" (si non membre) ou "Message" (si membre).  
2. **Membres & Compétences (Le Carnet du Cercle) :**  
   * Une grille affichant les membres du cercle.  
   * *Feature Clé :* Sous chaque membre, afficher ses **Réalisations liées à ce cercle**.  
   * *Exemple :* Carte "Zak (User)" \-\> Sous-titre "A réalisé : Sushi Saumon (30€)".  
3. **Messagerie Contextuelle (Board) :**  
   * Un mur de messages simple (type Logistique/Coordination).  
   * *Item Message :*  
     * User : Dams.  
     * Titre : "Je peux être là vers 5h".  
     * Info : Lien Google Maps trajet.

### **PAGE 3 : Page Soi / User (/user/{user})**

**Route :** Route::get('/user/{user}', ShowUserProfile::class);  
**Structure de la Vue :**

1. **Header Profil (Identité) :**  
   * Photo (PP), Nom, Titre (Cuisinier), Adresse (Colmar).  
   * Statistiques simples (Nombre de réalisations, Score de confiance).  
2. **Actions de Connexion (Auth) :**  
   * Si visiteur : Boutons "Se connecter pour contacter" ou "Créer un compte".  
   * Si Admin connecté : Bouton "Inviter dans mon cercle".  
3. **Les Cercles (Appartenance) :**  
   * Liste horizontale des cercles dont l'utilisateur est membre.  
   * C'est sa "Preuve sociale" : "Travaille chez Restaurant de Dams".  
4. **Portfolio de Compétences (Skills & Réalisations) :**  
   * **Titre :** Compétence (ex: Cuisinier).  
   * **Liste des Réalisations :**  
     * *Carte Réalisation :* Photo (Sushi), Titre, Prix, Info (Pimenté).  
     * *Preuve :* Badge "Validé par \[Nom du Cercle\]".

## **5\. Instructions de Développement pour l'Agent**

1. **Setup :** Initialise Laravel avec Livewire et Filament.  
2. **Data :** Exécute le blueprint YAML ci-dessus pour générer les modèles et migrations.  
3. **Filament (Admin) :** Crée les ressources UserResource et CircleResource pour gérer les données en backend.  
4. **Frontend (Livewire) :** Crée les 3 composants (Home, ShowCircle, ShowUserProfile).  
   * Utilise Tailwind CSS pour un design "Mobile First" et épuré.  
   * Pour la page /, assure-toi que la recherche est fluide (wire:model.live).  
5. **Logique Clé :** Dans la page /circle, n'affiche les messages que si l'utilisateur connecté est membre du cercle (Policy).