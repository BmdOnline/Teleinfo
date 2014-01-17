#Graphique Conso Electrique Téléinfo EDF
##avec Highcharts (v4.1)

###Aperçu
* Puissance Instantanée

[![Puissance Instantanée](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_part1_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_part1.png)

* Consommation Actuelle

[![Dernières 24h](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_part2_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_part2.png)

* Données Historiques

[![Consommation sur 8 jours](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_part3_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_part3.png)

* Vue Complète

[Cliquer pour visualiser](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/teleinfov4_all.png)

###Version 4.2 (dev)
* Graphiques
    - bugfix : N'affiche plus les 0 des données vides dans le graphique historique. (BmdOnline)
    - bugfix : N'affiche plus les décimales (non arrondies) des consommations dans le graphique quotidien. (BmdOnline)
    - bugfix : N'affiche plus la légende d'une période vide dans le graphique historique. (BmdOnline)
    - bugfix : Correction d'un bug dans l'affichage des semaines dans le graphique historique. (BmdOnline)
        * La semaine du "30/12/2014" apparaissait "Sem 1 (2013)" au lieu de "Sem 1 (2014)". (BmdOnline)
    - change : Refonte de l'infobulle du graphique historique. (BmdOnline)

* Moteur / PHP
    - change : Gestion des requêtes mysql dans un fichier dédié "queries.php". (BmdOnline)
    - change : Refonte complète de la gestion des abonnements. (energy01 & BmdOnline)
        * Les abonnements autres que "base" ou "HC/HP" sont maintenant gérés : EJP et Tempo (Bleu Blanc Rouge).
        * L'abonnement est détecté automatiquement, il n'est plus nécessaire de le spécifier dans le programme.

###Version 4.1 (dev)
* Interface
    - change : Gestion des appareils mobiles (Smartphones & Tablettes) (BmdOnline)
    - change : Mise en place de templates d'affichage. (BmdOnline)
        * Version avec onglets : chaque graphique est dans un onglet différent.
        * Version linéraire : tout apparait sur la même page.
    - change : Epuration du CSS spécifique dans "teleinfo.css". (BmdOnline)
    - change : Proposition de 3 thèmes CSS. (BmdOnline)
        * Version classique, en utilisant le thème "smoothness".
        * Version claire, en utilisant le thème "ui-lightness".
        * Version sombre, en utilisant le thème "ui-darkness".

* Graphiques
    - change : Rafraîchissement automatique de la gauge (option dans config.php). (energy01 & BmdOnline)
    - bugfix : N'affique que l'abonnement actuel dans les légendes des graphiques. (energy01)
    - bugfix : N'affiche plus les 0 de consommation de type "BASE" en cas d'abonnement HP/HC. (BmdOnline)
    - bugfix : Correction du bug cumulant les années sur le graphique historique. (BmdOnline)

* Moteur / PHP
    - change : Modification du nom du fichier principal "teleinfo.php" au lieu de "teleinfov4.php".
    - change : Gestion des requêtes mysql dans un fichier dédié "config.php". (energy01)
    - change : Prise en charge de différents formats de base de données (date ou timestamp notamment). (energy01 & BmdOnline)
    - change : Début de gestion des abonnements autres que "base" ou "HC/HP". (energy01 & BmdOnline)
    - change : Début de gestion d'un historique des tarifs EDF. (energy01)
    - change : Ajout d'une bibliothèque d'applications utilisées pour collecter les éléments téléinformation. (BmdOnline)

* Moteur / JavaScript
    - change : Validation JSLint de "teleinfo.js". (BmdOnline)

* Misa à jour des librairies (BmdOnline)
    - Highcharts 3.0.7 & Highstock 1.3.7
    - JQuery 2.1.0-pre (incompatible IE 6/7/8) & JQuery 1.10.2 (à activer manuellement en cas d'anciens navigateurs)
    - JQueryUI 1.11.0pre
    - JQueryMobile 1.4.0-rc1

###Version 4
* Ajout de la gauge de consommation instantanée.
* Ajout des boutons de navigation dans l'histogramme :
    - Choix du type de vue : jour / Semaine / Mois / Année.
    - Choix de la période : 1-7 jour / 1-52 semaines / 1-12 mois / 1-4 ans.
* Ajout d'une courbe représentant la période précédente dans l'histogramme.

* Remplacement de JQuery et Highcharts par les versions plus récentes.

###Version 3
* Prise en charge du régime EDF de base :
    - Les graphiques affichent les données BASE ou HP/HC selon l'abonnement EDF.
* Envoi de données de manière asynchrone, via GetJSON :
    - Les boutons de changement de période du second graphique marchent maintenant sans recharger la page.
    - Les données téléinfo n'apparaissent plus dans la page générée (afficher le code source affiche une page plus légère).

* Séparation du code php/html/javascript en 3 fichiers distincts :
    - Le code php qui fournit les données au format JSON.
    - Le fichier javascript qui génère les graphiques.
    - La page HTML qui utilise tout ça, très épurée.

* Ajout des boutons "-24h / Aujourd'hui / +24h" pour faire "défiler" le premier graphique.

* Remplacement de JQuery et Highcharts par les versions plus récentes.

* Résolution de problèmes de mémoire : chaque rafraîchissement augmentait significativement la mémoire
du navigateur.

Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts (v3)](http://penhard.anthony.free.fr/?p=283)

###Version 2
Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts (v2)](http://penhard.anthony.free.fr/?p=207)

###Version 1
Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts](http://penhard.anthony.free.fr/?p=111)

###Templates
Actuellement, 2 templates sont proposés pour chacun des affichages (desktop & mobile).
Pour en changer, il faut remplacer le contenu du répertoire "tpl"...
* Pour la vesion desktop, depuis :
    - tpl/desktop - lineaire
    - tpl/desktop - onglets
* Pour la vesion mobile, depuis :
    - tpl/mobile - lineaire
    - tpl/mobile - onglets
* Dans tous les cas, en ajoutant les fichiers communs depuis :
    - tpl/commun

Important :
A chaque changement de template, ne pas oublier de vider le contenu du répertoire "cache".

Remarque :
Pour le bon fonctionnement du programme, il faut choisir un template desktop ET un template mobile.
Par défaut, le programme est réglé sur les templates avec onglets.

###Thèmes
Actuellement, 3 thèmes sont proposés (classique, clair & sombre).
Pour en changer, il faut modifier le fichier "tpl/inc.lib.html", en spécifiant respectivement :
```php
<link rel="stylesheet" href="./css/smoothness/jquery-ui-1.11.0pre.min.css#">
```
ou
```php
<link rel="stylesheet" href="./css/ui-lightness/jquery-ui-1.11.0pre.min.css#">
```
ou
```php
<link rel="stylesheet" href="./css/ui-darkness/jquery-ui-1.11.0pre.min.css#">
```
Remarque :
Par défaut, le programme est réglé sur le thème sombre.

###Format de date MySQL
Selon l'utilitaire collectant les données téléinformation, la base peut utiliser un format de date différent.
Cette version du programme prévoit l'utilisation des 2 structure les plus fréquentes.
Le paramétrage est à faire dans le fichier config.php
```php
$db_date = "date"; // vaut soit "date" soit "timestamp"
```

###Nom des champs Teleinfo
Selon l'utilitaire collectant les données téléinformation, la base peut utiliser des noms différents.
Cette version du programme prévoit l'utilisation des 2 appellations les plus fréquentes.
Le paramétrage est à faire dans le fichier config.php :
```php
$db_iinst = "iinst1"; // vaut soit "iinst1" soit "inst1"
```

###Reste à faire
- [x] Prévoir une version "mobile", pour les smartphones et tablettes.
- [] Proposer de visualiser une période précédente ou une moyenne en surimpression de l'historique.
- [] Optimiser l'utilisation de HighCharts avec le chargement asynchrone :
    - Actuellement, le graphique est détruit et recréé. Il faudrait envisager de remplacer les données sans détruire le graphique.
- [] Fiabiliser les passages aux heures hiver/été.
- [x] Prévoir un rafraîchissement automatique, avec temporisation.
- [x] Réutiliser le fichier config.php (régression de la v4).