Le dump de la base de données est fourni dans le dossier sql/
    sql/dump_projet.sql

En mode production, il est important de supprimer ce dossier
si vous ne souhaitez pas risquer de voir votre base de données écrasée !

Importation du dump dans PHPMyAdmin:

Accueil de PHPMyAdmin
Onglet Importer
Choisir un fichier
Bouton Importer

Le projet est fourni avec deux catégories de tâches:
    - Perso
    - Pro
La gestion des catégories est complétement dynamique, rien n'est codé en dur.
Mais il n'est pas prévu de page d'administration permettant de gérer les catégorie.
Ni de rôle d'admin, dans un souci de simplification.

La gestion des catégories se fait donc via PHPMyAdmin ou directement depuis la base
via un client MySQL quelconque.

Bon dèv !