# leboncoin_api

Test Technique equipe Import

# Lancer Application
 Composer
    composer install
- Create Database
    bin/console doctrine:database:create
- Load Migrations
    bin/console doctrine:migrations:migrate

# Liste d'exemples de requêtes curl pour tester toutes les routes
Creation annonces
POST http://127.0.0.1:8000/annonces
{
    "titre": "New Article",
    "contenu": "New Content",
    "category": "automobile",
    "modele": "gran turismo serie 5"
}

Modifier une annonce
PUT http://127.0.0.1:8000/annonces/{id}
{
    "titre": "New Article",
    "contenu": "New Content",
    "category": "emploi"
}

Supprimer
DELLETE  http://127.0.0.1:8000/annonces/{id}

# Commentaires
Test nécessitant plus de temps, j'ai ici implementé tous les fonctionnalités du CRUD sans les tests unitaires et l'environnement docker