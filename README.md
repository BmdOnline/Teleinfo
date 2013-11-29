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

###Version 4.1 (dev)
* Gestion des requêtes mysql dans un fichier dédié "config.php". (energy01)
* Prise en charge partielle de différents formats de base de données (date ou timestamp notamment). (energy01)
* Début de gestion des abonnements autres que "base" ou "HC/HP". (energy01)
* Ajout d'une bibliothèque d'applications utilisées pour collecter les éléments téléinformation. (BmdOnline)
* Modification du nom du fichier principal "teleinfo.php" au lieu de "teleinfov4.php". (BmdOnline)

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

###Format de date MySQL
Selon l'utilitaire collectant les données téléinformation, la base peut utiliser format de date différent.
Cette version du programme utilise un type timestamp.
L'adaptation des requêtes SQL est plutôt simple pour utiliser un type datetime.

###Reste à faire
* Prévoir une version "mobile", pour les smartphones et tablettes.
* Prévoir un rafraîchissement automatique, avec temporisation.
* Proposer de visualiser une période précédente ou une moyenne en surimpression de l'historique.
* Optimiser l'utilisation de HighCharts avec le chargement asynchrone :
    - Actuellement, le graphique est détruit et recréé. Il faudrait envisager de remplacer les données sans détruire le graphique.
* Fiabiliser les passages aux heures hiver/été.