# Changements

## [dev]
### Interface
- added : Possibilité de zoomer directement au clic dans le graphique "Aperçu 24h".
- changed : Distinction du code `teleinfo.css` et `module_highcharts.css` spécifique aux graphiques.
- changed : Nettoyage du CSS restant dans `teleinfo.css`.
- changed : Remplacment du thème `smoothness` par le thème `base`.
- changed : Thème `base` utilisé en version mobile au lieu de `ui-darkness` (tooltip & datepicker clairs).
- fixed : Calcul correct des seuils des gauges lorsqu'on est sur une date antérieure aux dernières 24h.
- fixed : Affichage correct de la gauge d'intensité lorsqu'on est sur une date antérieure aux dernières 24h.

### Moteur / PHP
- changed : Modification de la structure retournée par la requête JSON History (PREC_data_detail vs PREC_detail[data].
- fixed : Double gauge ne bugge plus si Max(I) = 0. Dans ce cas, n'affiche pas la gauge I=0.
- fixed : Affichage correct de l'historique lors des changements d'heures.

### Moteur / JavaScript
- added : Précision de la langue `lang=fr`dans les fichiers HTML.
- added : Infobulles sur les listes de type `selectmenu` dans les fichiers HTML.
- changed : N'effectue plus le rafraichissement des graphiques qui ne sont pas visibles.
- changed : Ajustement des indentations dans les fichiers HTML.
- changed : Distinction du code `teleinfo.js` et `module_highcharts.js` spécifique aux graphiques.
- changed : Suppression des appels `unbind()` (déprécié), remplacés par `off()`.
- changed : Suppression d'éléments liés aux styles dans `module_highcharts.js`. Utilisation de `module_highcharts.css`.
- fixed : Sélection de la période pour l'historique en version mobile.
- fixed : Meilleure gestion du redimensionnement lorsque la gauge est affichée. Ne recalcule plus tout.

### Dépot
- added : Création du fichier CHANGELOG.md
- added : Mise en place de la licence GPL v3
- changed : Mise à jour des captures d'écran

### Mise à jour des librairies
- changed : RainTPL 3.1.0
- changed : Mobile Detect 2.8.22
- changed : Highcharts 5.0.0 & Highstock 5.0.0
- changed : JQueryMobile 1.4.5
- changed : JQueryUI 1.2.1

## [4.5.1] - 2016-09-25
### Moteur / PHP
- added : Ajout du fichier de config Ecodevice Tempo.
- changed : Optimisation du temps d'exécution pour l'affichage de l'index du compteur.
- fixed : Ajustements divers.

### Dépot
- Mise à jour des captures d'écran

## [4.5] - 2016-09-21
### Interface
- added : Affichage de l'index du compteur pour faciliter les relevés EDF.

### Moteur / PHP
- changed : Compatibilité avec PHP 7.
- changed : Quelques adaptations du code javascript.

## [4.4dev] - 2014-06-09
### Interface
- fixed : Ajustements mineurs : libellés singulier/pluriel (1 jour, xx jours)...

### Moteur / PHP
- added : Prise en compte de l'Eco-Device.
- added : Ajout d'une option `afficheIndex` pour activer l'affichage de l'index du compteur.
- fixed : Envisage le cas où aucune donnée n'est retournée (json ne reverra rien).
- fixed : Limite les requêtes (json) à la date la plus récente en base.

## [4.3] - 2014-05-02
### Interface
- changed : Désactivation des éléments durant le rafraichissment des données (sablier).

### Graphiques
- added : Implémentation de l'historique en 3D (option désactivable).
- changed : Suppression des "trous" lors du changement de période tarifaire (exemple : HC vers HP).
- changed : Amélioration de l'affichage des doubles gauges en mode vertical.

### Moteur / PHP
- added : Possibilité de recalculer la puissance active et ne pas utiliser le relevé "Puissance Apparente (PAPP)".
    * Création de l'option `$config["recalculPuissance"]`.
- changed : Encore une refonte de la partie configuration.
    * Il suffit maintenant de modifier un paramètre pour choisir les modèles de structure SQL proposés.
    * Regroupement des options pour chacun des graphiques.

### Mise à jour des librairies
- changed : Highcharts 4.0.1 & Highstock 2.0.1 (apport des graphiques 3D)
- changed : JQuery 2.1.0 (incompatible IE 6/7/8) & JQuery 1.11.0 (à activer manuellement en cas d'anciens navigateurs)
- changed : JQueryUI 1.11.0-pre (2014-04-27)
- changed : JQueryMobile 1.4.2

## [4.2dev] - 2014-02-09
### Interface
- added : Ajout d'un calendrier pour sélectionner la période dans la vue "Aperçu 24h". (@BmdOnline)
- added : Affichage des données concernant l'abonnement et la consommation courante dans la vue "Instantané". (@BmdOnline)
    * Option tarifaire et intensité souscrite.
    * Période tarifaire actuelle.
    * Puissance et intensité maximales sur 24h.
    * Prochaine période tarifaire (abonnement Tempo).
- added : Ajout d'icônes pour illustrer les boutons de navigation. (@BmdOnline)

### Graphiques
- added : Affichage de double gauge (puissance & intensité). (@energy01 & @BmdOnline)
    * Une option permet de n'afficher que la puissance.
- added : Paramétrage des seuils limites des gauges dans le fichier `config.php`. (@BmdOnline)
- added : La couleur des séries est configurable dans le fichier `config.php`. (@BmdOnline)
    * Chaque période tarifaire a la même couleur dans tous les graphiques.
- changed : L'échelle de la gauge instantanée s'ajuste automatiquement. (@energy01 & @BmdOnline)
- changed : Affiche toutes les périodes tarifaires, et pas seulement "Base" ou "HP/HC". (@BmdOnline)
- changed : Revue de l'affichage de la légende des graphiques quotidien et historique. (@BmdOnline)
    * N'affiche plus les périodes ne correspondant pas à l'abonnement souscrit.
    * N'affiche plus les périodes de l'abonnement souscrit mais n'ayant pas de donnée (graphique historique).
- changed : Refonte de l'infobulle du graphique historique. (@BmdOnline)
- fixed : N'affiche plus les 0 des données vides dans le graphique historique. (@BmdOnline)
- fixed : N'affiche plus les décimales (non arrondies) des consommations dans le graphique quotidien. (@BmdOnline)
- fixed : Correction d'un bug dans l'affichage des semaines dans le graphique historique. (@BmdOnline)
    * La semaine du "30/12/2014" apparaissait "Sem 1 (2013)" au lieu de "Sem 1 (2014)". (@BmdOnline)

### Moteur / PHP
- changed : Refonte complète de la gestion des requêtes MySQL. (@BmdOnline)
    * Gestion des requêtes MySQL dans un fichier dédié `queries.php`.
    * Le paramétrage est améliorée pour prendre en charge le maximum de configurations possible.
- changed : Refonte complète de la gestion des abonnements. (@energy01 & @BmdOnline)
    * Les abonnements autres que "base" ou "HC/HP" sont maintenant gérés : EJP et Tempo (Bleu Blanc Rouge).
    * L'abonnement est détecté automatiquement, il n'est plus nécessaire de le spécifier dans le programme.
- changed : Refonte complète de la gestion des tarifs. (@BmdOnline)
    * Les tarifs EDF sont historisés, le calcul du coût tient compte des variations de prix.
    * Les taxes sont clairement identifiées.
    * Les évolutions de TVA sont également prises en charge.
- changed : JSON fournit la prochaine période tarifaire pour traitement éventuel. (@BmdOnline)
- changed : L'utilisation de templates pour générer les pages est désactivé par défaut. (@BmdOnline)
- fixed : Meilleure gestion des périodes vides dans le graphique historique. (@BmdOnline)

## [4.1dev] - 2013-12-06
### Interface
- added : Gestion des appareils mobiles (Smartphones & Tablettes) (@BmdOnline)
- added : Mise en place de templates d'affichage. (@BmdOnline)
    * Version avec onglets : chaque graphique est dans un onglet différent.
    * Version linéraire : tout apparait sur la même page.
- added : Proposition de 3 thèmes CSS. (@BmdOnline)
    * Version classique, en utilisant le thème `smoothness`.
    * Version claire, en utilisant le thème `ui-lightness`.
    * Version sombre, en utilisant le thème `ui-darkness`.
- changed : Epuration du CSS spécifique dans `teleinfo.css`. (@BmdOnline)

### Graphiques
- added : Rafraîchissement automatique de la gauge (option dans config.php). (@energy01 & @BmdOnline)
- fixed : N'affique que l'abonnement actuel dans les légendes des graphiques. (@energy01)
- fixed : N'affiche plus les 0 de consommation de type "BASE" en cas d'abonnement "HP/HC". (@BmdOnline)
- fixed : Correction du bug cumulant les années sur le graphique historique. (@BmdOnline)

### Moteur / PHP
- added : Début de gestion des abonnements autres que "BASE" ou "HC/HP". (@energy01 & @BmdOnline)
- added : Début de gestion d'un historique des tarifs EDF. (@energy01)
- added : Ajout d'une bibliothèque d'applications utilisées pour collecter les éléments téléinformation. (@BmdOnline)
- changed : Modification du nom du fichier principal `teleinfo.php` au lieu de `teleinfov4.php`.
- changed : Gestion des requêtes mysql dans un fichier dédié `config.php`. (@energy01)
- changed : Prise en charge de différents formats de base de données (date ou timestamp notamment). (@energy01 & @BmdOnline)

### Moteur / JavaScript
- changed : Validation JSLint de `teleinfo.js`. (@BmdOnline)

### Mise à jour des librairies (@BmdOnline)
- changed : Highcharts 3.0.7 & Highstock 1.3.7
- changed : JQuery 2.1.0-pre (incompatible IE 6/7/8) & JQuery 1.10.2 (à activer manuellement en cas d'anciens navigateurs)
- changed : JQueryUI 1.11.0pre (2013-12-03)
- changed : JQueryMobile 1.4.0-rc1

## [4.0] - 2013-10-18
### Graphiques
- added : Ajout de la gauge de consommation instantanée.
- added : Ajout des boutons de navigation dans l'histogramme :
    * Choix du type de vue : jour / Semaine / Mois / Année.
    * Choix de la période : 1-7 jour / 1-52 semaines / 1-12 mois / 1-4 ans.
- added : Ajout d'une courbe représentant la période précédente dans l'histogramme.

### Mise à jour des librairies
- changed : Remplacement de JQuery et Highcharts par les versions plus récentes.

## [3.0] - 2012-07-04
### Interface
- added : Ajout des boutons "-24h / Aujourd'hui / +24h" pour faire "défiler" le premier graphique.

### Moteur / JavaScript
- added : Prise en charge du régime EDF de base :
    * Les graphiques affichent les données BASE ou HP/HC selon l'abonnement EDF.
- changed : Envoi de données de manière asynchrone, via GetJSON :
    * Les boutons de changement de période du second graphique marchent maintenant sans recharger la page.
    * Les données téléinfo n'apparaissent plus dans la page générée (afficher le code source affiche une page plus légère).
- changed : Séparation du code php/html/javascript en 3 fichiers distincts :
    * Le code php qui fournit les données au format JSON.
    * Le fichier javascript qui génère les graphiques.
    * La page HTML qui utilise tout ça, très épurée.
- fixed : Résolution de problèmes de mémoire : chaque rafraîchissement augmentait significativement la mémoire du navigateur.

### Mise à jour des librairies
- Remplacement de JQuery et Highcharts par les versions plus récentes.

Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts (v3)](http://penhard.anthony.free.fr/?p=283)

## [2.0] - 2012-02-10
Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts (v2)](http://penhard.anthony.free.fr/?p=207)

## 1.0 - 2011-03-02
Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts](http://penhard.anthony.free.fr/?p=111)

##Reste à faire
- [ ] Thèmes pour les graphiques.
- [ ] Ajout d'un calendrier à la place du bouton "Aujourd'hui" pour l'historique.
    - La navigation doit dépendre du type d'historique (jour / semaine / mois)...
- [ ] Revoir les seuils de la gauge instantanée.
- [ ] Proposer de visualiser une période précédente ou une moyenne en surimpression de l'historique.
- [ ] Optimiser l'utilisation de HighCharts avec le chargement asynchrone :
    - Actuellement, le graphique est détruit et recréé. Il faudrait envisager de remplacer les données sans détruire le graphique.
- [ ] Fiabiliser les passages aux heures hiver/été.

[dev]: https://github.com/BmdOnline/Teleinfo/compare/v4.5.1...dev
[4.5.1]: https://github.com/BmdOnline/Teleinfo/compare/v4.5...v4.5.1
[4.5]: https://github.com/BmdOnline/Teleinfo/compare/v4.4dev...v4.5
[4.4dev]: https://github.com/BmdOnline/Teleinfo/compare/v4.3...v4.4dev
[4.3]: https://github.com/BmdOnline/Teleinfo/compare/v4.2dev...v4.3
[4.2dev]: https://github.com/BmdOnline/Teleinfo/compare/v4.1dev...v4.2dev
[4.1dev]: https://github.com/BmdOnline/Teleinfo/compare/v4.0...v4.1dev
[4.0]: https://github.com/BmdOnline/Teleinfo/compare/v3.0...v4.0
[3.0]: https://github.com/BmdOnline/Teleinfo/compare/v2.0...v3.0
[2.0]: https://github.com/BmdOnline/Teleinfo/compare/v1.0...v2.0
