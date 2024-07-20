# C2S - Application de Gestion de Ventes

## Description

C2S est une application web en PHP conçue pour aider les entreprises à gérer leurs clients, articles, commandes et factures. Elle offre une interface conviviale pour suivre les ventes, les stocks et les performances des représentants.

## Fonctionnalités

- **Gestion des clients:** Ajouter, modifier, supprimer et afficher des clients avec leurs informations détaillées (nom, adresse, contacts, etc.).
- **Gestion des articles:** Gérer votre catalogue d'articles, y compris le code, la désignation, la famille, la gamme, le prix unitaire, la quantité en stock et la TVA.
- **Gestion des commandes:** Créer, modifier et supprimer des commandes, ajouter et gérer des lignes de commande avec des articles sélectionnés et leurs quantités.
- **Gestion des factures:** Générer des factures à partir des commandes, afficher et imprimer des factures avec une mise en page professionnelle.
- **Statistiques:** Visualiser des graphiques interactifs des ventes par article, par client, par zone, par représentant et par mois.
- **Authentification:** Connexion sécurisée pour les administrateurs et les représentants.
- **Interface utilisateur responsive:**  L'application est conçue avec Tailwind CSS pour un rendu optimal sur tous les appareils (ordinateurs de bureau, tablettes, mobiles).

## Plan du site (Sitemap)
'''
C2S/
├── components/
│ ├── navbar.php
│ └── sidebar.php
├── clients/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ └── delete.php
├── articles/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ └── delete.php
├── gammes/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ └── delete.php
├── familles/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ └── delete.php
├── zones/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ └── delete.php
├── circuits/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ └── delete.php
├── commandes/
│ ├── index.php
│ ├── create.php
│ ├── edit.php
│ ├── view.php
│ └── delete.php
├── factures/
│ ├── index.php
│ ├── create.php
│ ├── view.php
│ ├── edit.php
│ └── delete.php
├── charts/
│ ├── index.php
│ ├── data_articles_vendus.php
│ ├── data_ventes_par_client.php
│ ├── data_ventes_par_zone.php
│ ├── data_ventes_par_representant.php
│ ├── data_commandes_par_mois.php
│ └── data_ventes_par_famille.php
├── config/
│ └── database.php
└── index.php
└── js/
└── charts.js
'''
## Technologies utilisées

- PHP
- SQL Server 
- PDO (PHP Data Objects)
- Tailwind CSS
- Chart.js
- HTML
- CSS 
- JavaScript

## Installation

1.  Clonez ce dépôt sur votre serveur web local (XAMPP, WAMP, etc.).
2.  Créez une base de données SQL Server nommée  `C2S`. 
3.  Importez le schéma de base de données (fichier SQL) dans la base de données `C2S`.
4.  Configurez les informations de connexion à la base de données dans le fichier  `config/database.php`.
5.  Assurez-vous que votre serveur web est configuré pour servir le dossier  `C2S/`  comme répertoire racine.
6.  Accédez à l'application dans votre navigateur en utilisant l'URL  `http://localhost/C2S/` (ou l'URL de votre serveur web). 

## Configuration

-   Mettez à jour les informations d'identification de la base de données dans le fichier  `config/database.php`.
-   Personnalisez le logo, les couleurs et les styles de l'application en modifiant les fichiers CSS et les modèles HTML.

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request si vous souhaitez améliorer l'application.

## Licence

Ce projet est sous licence propriétaire.
