# Graphique Conso Electrique Téléinfo EDF avec Highcharts

[![Animation](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/animation_small.gif)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/animation.gif)

# Sommaire
* [Pré-requis](#pré-requis)
* [Installation](#installation)
    * [Version actuelle] (#version-actuelle)
    * [Version dev] (#version-dev)
    * [Anciennes versions] (#anciennes-versions)
* [Configuration](#configuration)
    * [Accès MySQL] (#accès-mysql)
    * [Table téléinfo] (#table téléinfo)
    * [Table personnalisée] (#table-personnalisée)
* [Personnalisation](#personnalisation)
    * [Puissance apparente - Puissance active] (#puissance-apparente---puissance-active)
    * [Gauge instantanée] (#gauge-instantanée)
        * [Donnée à afficher] (#donnée-à-afficher)
        * [Rafraichissement automatique] (#rafraichissement-automatique)
        * [Aspect des gauges] (#aspect-des-gauges)
    * [Aperçu quotidien] (#Aperçu-quotidien)
    * [Historiques] (#historiques)
        * [Affichage 3D] (#affichage-3d)
        * [Type de graphique] (#type-de-graphique)
        * [Période précédente] (#période-précédente)
    * [Couleur des graphiques] (#couleur-des-graphiques)
* [Tarifs EDF] (#tarifs-edf)
* [Templates] (#templates)
* [Thèmes] (#thèmes)
    * [Gestion par fichiers HTML] (#gestion-par-fichiers-html)
    * [Gestion par templates] (#gestion-par-templates)
* [Copies d'écran](#copies-d'écran)
* [Changements] (#changements)

# Graphique Conso Electrique Téléinfo EDF avec Highcharts
Ceci est une application WEB permettant de visualiser sous forme de graphique les relevés EDF fournis par l'interface téléinfo.

La présentation s'adapte automatiquement aux smartphones & tablettes.

Il faut, au préalable, disposer d'une base de donnée MySQL contenant les relevés Téléinfo.

Vous trouverez toute la documentation nécessaire à la collecte tééinfo à l'aide de votre moteur de recherche favori.
Cet aspect technique ne sera pas évoqué et aucun support ne sera fourni ici.

# Pré-requis
* Serveur Web (testé avec Apache, serveurs type Nginx… non testés)
* PHP (testé avec versions 5.4, 5.5, 5.6 et 7.0.4)
* MySQL / MariaDB (testé avec versions 5.0 et 5.5 et 5.7.1)
* Enfin, un compteur EDF avec l'option téléinfo et les relevés correspondants.

# Installation
## Version actuelle
Deux possibilités :
* Utiliser l'utilitaire `git` pour dupliquer le dépôt :
```bash
git clone git://github.com/BmdOnline/Teleinfo.git
```

* Télécharger et décompressez l'archive zip à partir de l'interface `github` :

[![GitHub Download ZIP](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/GitHub Download ZIP.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/GitHub Download ZIP.png)

* Lien direct de l'archive :
    * https://github.com/BmdOnline/Teleinfo/archive/master.zip
    * https://github.com/BmdOnline/Teleinfo/archive/master.tar.gz

Vous avez maintenant une copie locale du dépôt distant.

## Version dev
Deux possibilités :
* Utiliser l'utilitaire `git` pour dupliquer le dépôt :
```bash
git clone -b dev git://github.com/BmdOnline/Teleinfo.git
```

* Lien direct de l'archive :
    * https://github.com/BmdOnline/Teleinfo/archive/dev.zip
    * https://github.com/BmdOnline/Teleinfo/archive/dev.tar.gz

## Liste des versions

| Version | Lien |
| ------------- | ------------- |
| dev | https://github.com/BmdOnline/Teleinfo/archive/dev.zip <br> https://github.com/BmdOnline/Teleinfo/archive/dev.tar.gz |
| stable | https://github.com/BmdOnline/Teleinfo/archive/master.zip <br> https://github.com/BmdOnline/Teleinfo/archive/master.tar.gz |
| | |
| v4.3 | https://github.com/BmdOnline/Teleinfo/archive/v4.3.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.3.tar.gz |
| v4.2dev | https://github.com/BmdOnline/Teleinfo/archive/v4.2dev.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.2dev.tar.gz |
| v4.1dev | https://github.com/BmdOnline/Teleinfo/archive/v4.1dev.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.1dev.tar.gz |
| v4.0 | https://github.com/BmdOnline/Teleinfo/archive/v4.0.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.0.tar.gz |
| v3.0 | https://github.com/BmdOnline/Teleinfo/archive/v3.0.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v3.0.tar.gz |
| v2.0 | https://github.com/BmdOnline/Teleinfo/archive/v2.0.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v2.0.tar.gz |
| v1.0 | https://github.com/BmdOnline/Teleinfo/archive/v1.0.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v1.0.tar.gz |

# Configuration
## Accès MySQL
Pour commencer, il est nécessaire de définir l'accès à la base MySQL et à la table Téléinfo.
Dans le fichier `config.php`, il faut adapter ces quelques lignes
```php
/***********************/
/*    Données MySQL    */
/***********************/
$db_connect = array (
    "serveur" => "localhost",
    "base"    => "teleinfo",
    "table"   => "tbTeleinfo",
    "login"   => "teleinfo",
    "pass"    => "teleinfo"
);
```

## Table téléinfo
Selon le système utilisé, la table MySQL peut avoir des formats différents.
Ce programme est fait pour s'adapter à différentes structures de données.
Quelques modèles type sont proposés :

| | Format 1 | Format 2 | Format 3 |
| ------------- | ------------- | ------------- | ------------- |
| Type de date | Date | Timestamp | Timestamp |
| | | | |
| DATE | Date | Timestamp | FTimestamp |
| REC_DATE | Non | Oui | Oui |
| REC_TIME | Non | Oui | Oui |
| Noms | IINST1 | INST1 | IINST |
| DEMAIN | Oui | Non | Non |
| | | | |
| OPTARIF HC | HC.. (points) | HC.. (points) | HC.. (points) |
| PTEC HC/HP | HC.. / HP.. (points) | HC / HP (sans points) | HC / HP (sans points) |
| | | | |
| Modèle | structure.date.php | structure.timestp.php | structure.ftimestp.php |

Dans le fichier `config.php`, il faut adapter ces quelques lignes
```php
/************************/
/*    Table TéléInfo    */
/************************/
// Selon la configuration de la base de données téléinfo,
//   choisir la structure à utiliser :
// - structure.date.php
// - structure.timestp.php
// - structure.ftimestp.php
// Il est également possible de se créer une structure personnalisée
// - structure.custom.php (par exemple)
include_once("structure.date.php");
```

A partir de là, le programme est opérationnel.

## Table personnalisée
Si aucun des modèles proposés ne convient, il est tout à fait possible d'en créer un personnalisé.
Le plus simple est de partir d'un modèle existant et de le modifier.

### Format de date MySQL
Selon l'utilitaire collectant les données téléinformation, la base peut utiliser un format de date différent (date ou timestamp).
Attention à la casse (majuscule / minuscule) !
```php
$config_table = array (
    // Quelques informations sur la configuration
    "type_date" => "date", // "date" ou "timestamp" selon le type de stockage de la date
    // Nom des champs de la table.
    //   Clé    = nom interne au programme : NE PAS MODIFIER
    //   Valeur = nom du champ dans la table téléinfo
    // Adapter les valeurs du tableau si le nom du champ est différent
    "table" => array (
        "DATE"     => "DATE",    // => vaut soit "DATE", soit "TIMESTAMP"
        //...//
```

### Nom des champs Teleinfo
Selon l'utilitaire collectant les données téléinformation, la base peut utiliser des noms différents.
Attention à la casse (majuscule / minuscule) !
```php
    // Nom des champs de la table.
    //   Clé    = nom interne au programme : NE PAS MODIFIER
    //   Valeur = nom du champ dans la table téléinfo
    // Adapter les valeurs du tableau si le nom du champ est différent
    "table" => array (
        "DATE"     => "DATE",    // => vaut soit "DATE", soit "TIMESTAMP"
        //...//
        "IINST1"   => "IINST1",  // => vaut soit "IINST1" soit "INST1"
        //...//
```

# Personnalisation
## Puissance apparente - Puissance active
Dans le cas de faible consommation (<~180w), la puissance apparente (PAPP) de certains relevés téléinfo ne serait pas pertinente.
Lors de relevés téléinfo avec une fréquence réduite, la pertinence de la puissance apparente peut se poser.

Il est alors possible de recalculer la puissance active, en se basant sur l'index relevé.
Ce résultat, bien qu'approximatif (impact du cos phi), peut s'avérer préférable à la puissance apparente.

L'option se situe dans le fichier `config.php` :
```php
/*******************************/
/*    Données EDF & Téléinfo   */
/*******************************/
//...//
$config["recalculPuissance"]     = false; // true : calcule la puissance en se basant sur le relevé d'index plutôt que PAPP
```

## Gauge instantanée
### Donnée à afficher
Il est possible d'afficher une ou deux gauges.
Dans le cas d'une seule gauge affichée, c'est la puissance qui est sélectionnée.
Dans le cas de deux gauges, l'intensité s'affichera à côté.

L'option se situe dans le fichier `config.php` :
```php
$config["graphiques"]["instantly"] = array(
    //...//
    "doubleGauge"  => true,      // true : affiche intensité en plus de la puissance
    //...//
```

### Rafraichissement automatique
Il est possible d'activer ou désactiver le rafraichissement automatique des gauges.

L'option se situe dans le fichier `config.php` :
```php
$config["graphiques"]["instantly"] = array(
    //...//
    "refreshAuto"  => true,      // active le rafraichissement automatique
    "refreshDelay" => 120,       // relancé toutes les 120 secondes
    //...//
```

### Aspect des gauges
Il est possible de modifier les différents seuils des gauges, ainsi que les couleurs associées.

Les options se situent dans le fichier `config.php` :
```php
// couleurs des bandes des gauges
$config["graphiques"]["instantly"] = array(
    //...//
    "bands" => array(            // couleurs des bandes des gauges
        "W" => array(            // Puissance
            300   => "#55BF3B",  // de 0 à 300
            1000  => "#DDDF0D",  // de 300 à 1000
            3000  => "#FFA500",  // de 1000 à 3000
            10000 => "#DF5353"   // supérieur à 3000
        ),
        "I" => array(            // Intensité
            2   => "#55BF3B",    // de 0 à 2
            5   => "#DDDF0D",    // de 2 à 5
            13  => "#FFA500",    // de 5 à 13
            100 => "#DF5353"     // supérieur à 20
        )
    )
    //...//
```

## Aperçu quotidien
Ce graphique ne nécessite aucun réglage spécifique.

## Historiques
### Affichage 3D
Il est possible de choisir un affichage 2D ou 3D des histogrammes.
Cette option est actuellement expérimentale. En effet, elle est nouvelle dans HighCharts et semble avoir quelques défauts d'affichage.

Défaut constatés :
* Les valeurs affichées sur les barres de l'histogramme sont parfois mal positionnées.
* La courbe de période précédente s'affiche derrière l'histogramme au lieu de s'afficher devant.

L'option se situe dans le fichier `config.php` :
```php
$config["graphiques"]["history"] = array(
    "show3D"     => true,       // true : affiche le graphique en 3D
    //...//
```

### Type de graphique
Il est possible de choisir le type de représentation des séries de données.
Certaines combinaisons n'ont pas de sens ou sont mal gérées par HighCharts.
A vous de tester…

Les options se situent dans le fichier `config.php` :
```php
$config["graphiques"]["history"] = array(
    //...//
    "typeSerie"  => "column",    // Type de graphique pour les séries de données (syntaxe HighCharts)
    "typePrec"   => "spline",    // Type de graphique pour les périodes précédentes (syntaxe HighCharts)
    //...//
```

### Période précédente
Il sera possible de choisir entre un affichage simple ou détaillé des données de la période précédente.

Les options se situent dans le fichier `config.php` :
```php
$config["graphiques"]["history"] = array(
    //...//
    "detailPrec" => false,       // true : détaille les différentes périodes tarifaires pour les périodes précédentes
    //...//
```

Cette option n'est pas encore implémentée.

## Couleur des graphiques
Chaque donnée affiché en graphique a une couleur paramétrable.

Pour changer les couleurs, il faut adapter le fichier `config.php` :
```php
// couleurs de chacune des séries des graphiques
$teleinfo["COULEURS"] = array(
    "MIN"  => "green",   // Seuil de consommation minimale sur la période
    "MAX"  => "red",     // Seuil de consommation maximale sur la période
    "PREC" => "#DB843D", // Période précédente
    "BASE" => "#2f7ed8",
    "HP"   => "#c42525",
    "HC"   => "#2f7ed8",
    "HPJB" => "#2f7ed8",
    "HPJW" => "#8bbc21",
    "HPJR" => "#910000",
    "HCJB" => "#77a1e5",
    "HCJW" => "#a6c96a",
    "HCJR" => "#c42525",
    "HN"   => "#2f7ed8",
    "HPM"  => "#c42525",
    "I"    => "blue"     // Intensité
);
```

# Tarifs EDF
Le fichier `tarifs.php` contient l'historique de tous les tarifs EDF pour chaque formule.
Les données sont nationales et communes pour tout le monde, sauf certaines taxes locales.
* Pour un calcul plus juste, il est nécessaire d'adapter la `TCFE`.
* A chaque évolution tarifaire, il est nécessaire d'ajouter les nouveaux tarifs dans le fichier.

# Templates
Il est possible de choisir entre plusieurs mises en page des graphiques.
Deux choix sont proposés :
* Sous forme d'onglets
* Sous forme linéraire

Chacune des 2 mises en page est prévue en version "desktop" et en version "mobile".

L'application est également capable de générer les pages à partir de templates complexes grace à la librairie `RainTPL`.
Par défaut, cette option est désativée : l'affichage utilise des fichiers HTML préparés.

## Gestion par fichiers HTML
Des fichiers sont proposés pour chacun des modes d'affichage.

Pour changer de modèle, il faut adapter le fichier `config.php` :
```php
/*********************************/
/*    Paramètres du programme    */
/*********************************/
$config["useTemplate"]           = false; // utilise les templates pour afficher les page HTML (utilise RainTPL)
//...//
$config["notemplate"]["desktop"] = "tpl/teleinfo.tabs.html";
$config["notemplate"]["mobile"]  = "tpl/teleinfo.tabs.mobile.html";
```

* Pour la vesion desktop, les fichiers :
    - `tpl/teleinfo.single.html`
    - `tpl/teleinfo.tabs.html`
* Pour la vesion mobile, les fichiers :
    - `tpl/teleinfo.single.mobile.html`
    - `tpl/teleinfo.tabs.mobile.html`

## Gestion par templates
Le moteur de template utilisé est `RainTPL`. Il est possible de modifier les pages en utilisant la syntaxe adéquate.

Pour changer de modèle, il faut adapter le fichier `config.php` :
```php
/*********************************/
/*    Paramètres du programme    */
/*********************************/
$config["useTemplate"]           = true; // utilise les templates pour afficher les page HTML (utilise RainTPL)
$config["template"]["tpl_dir"]   = "tpl/files/"; // Attention au / final
$config["template"]["desktop"]   = "teleinfo";
$config["template"]["mobile"]    = "teleinfo.mobile";
//...//
```

Il faut également remplacer le contenu du répertoire `tpl/files`…
* Pour la vesion desktop, depuis :
    - `tpl/files/desktop - lineaire`
    - `tpl/files/desktop - onglets`
* Pour la vesion mobile, depuis :
    - `tpl/files/mobile - lineaire`
    - `tpl/files/mobile - onglets`
* Dans tous les cas, en ajoutant les fichiers communs depuis :
    - `tpl/files/commun`

Important :
A chaque changement de template, ne pas oublier de vider le contenu du répertoire `cache`.

Remarque :
Pour le bon fonctionnement du programme, il faut choisir un template desktop ET un template mobile.
Par défaut, le programme est réglé sur les templates avec onglets.

# Thèmes
Actuellement, 3 thèmes sont proposés (classique, clair & sombre).
Pour en changer, il faut modifier le fichier...
* En mode HTML, sans template
    * Pour la vesion desktop, les fichiers :
        - `tpl/teleinfo.single.html`
        - `tpl/teleinfo.tabs.html`
    * Pour la vesion mobile, les fichiers :
        - `tpl/teleinfo.single.mobile.html`
        - `tpl/teleinfo.tabs.mobile.html`
* En mode template, le fichier :
    `tpl/files/inc.lib.html`

* Pour le mode _classique_
```php
<link rel="stylesheet" href="./css/smoothness/jquery-ui-1.11.0-pre.min.css">
```

* Pour le mode _clair_
```php
<link rel="stylesheet" href="./css/ui-lightness/jquery-ui-1.11.0-pre.min.css">
```

* Pour le mode _sombre_
```php
<link rel="stylesheet" href="./css/ui-darkness/jquery-ui-1.11.0-pre.min.css">
```

Remarque :
Par défaut, le programme est réglé sur le thème _sombre_.

* En utilisant `RainTPL`, ne pas oublier le `#` en fin d'URL.
```php
<link rel="stylesheet" href="./css/ui-darkness/jquery-ui-1.11.0-pre.min.css#">
```

#Copies d'écran

* Onglet _Puissance Instantanée_

[![Puissance Instantanée](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_inst_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_inst.png)

* Onglet _Consommation Actuelle_

[![Dernières 24h](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_day_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_day.png)

* Onglet _Données Historiques_ (2D)

[![Consommation sur 8 jours](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_hist_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_hist.png)

* Onglet _Données Historiques_ (3D)

[![Consommation sur 8 jours](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_hist_3D_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_hist_3D.png)

* Formule de base
    * [Affichage simple] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/base_single.png)
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/base_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/base_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/base_tab_hist.png)
    * [Onglet _Historique_ (3D)] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/base_tab_hist_3D.png)

* Formule HP/HC
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_hist.png)
    * [Onglet _Historique_ (3D)] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/hphc_tab_hist_3D.png)

* Formule Tempo
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/tempo_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/tempo_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/tempo_tab_hist.png)
    * [Onglet _Historique_ (3D)] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/tempo_tab_hist_3D.png)

* Version Mobile
    * [Affichage simple] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/mobile_single.png)
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/mobile_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/mobile_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/mobile_tab_hist.png)

* Thèmes
    * [Thème _ui-darkhness_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/theme_ui-darkness.png)
    * [Thème _ui-lightness_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/theme_ui-lightness.png)
    * [Thème _smoothness_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.3/theme_smoothness.png)

# Changements

##Version 4.4 (dev)
* Interface
    - bugfix : Ajustements mineurs : libellés singulier/pluriel (1 jour, xx jours)...
    - change : Affichage de l'index du compteur pour faciliter les relevés EDF.

* Moteur / PHP
    - change : Compatibilité avec PHP 7.
    - change : Prise en compte de l'Eco-Device.
    - change : Ajout d'une option "afficheIndex" pour activer l'affichage de l'index du compteur.
    - bugfix : Envisage le cas où aucune donnée n'est retournée (json ne reverra rien).
    - bugfix : Limite les requêtes (json) à la date la plus récente en base.

##Version 4.3
* Interface
    - change : Désactivation des éléments durant le rafraichissment des données (sablier).

* Graphiques
    - change : Implémentation de l'historique en 3D (option désactivable).
    - change : Suppression des "trous" lors du changement de période tarifaire (exemple : HC vers HP).
    - change : Amélioration de l'affichage des doubles gauges en mode vertical.

* Moteur / PHP
    - change : Possibilité de recalculer la puissance active et ne pas utiliser le relevé "Puissance Apparente (PAPP)".
        * Création de l'option "$config["recalculPuissance"]".
    - change : Encore une refonte de la partie configuration.
        * Il suffit maintenant de modifier un paramètre pour choisir les modèles de structure SQL proposés.
        * Regroupement des options pour chacun des graphiques.

* Misa à jour des librairies
    - Highcharts 4.0.1 & Highstock 2.0.1 (apport des graphiques 3D)
    - JQuery 2.1.0 (incompatible IE 6/7/8) & JQuery 1.11.0 (à activer manuellement en cas d'anciens navigateurs)
    - JQueryUI 1.11.0-pre (2014-04-27)
    - JQueryMobile 1.4.2

##Version 4.2 (dev)
* Interface
    - change : Ajout d'un calendrier pour sélectionner la période dans la vue "Aperçu 24h". (BmdOnline)
    - change : Affichage des données concernant l'abonnement et la consommation courante dans la vue "Instantané". (BmdOnline)
        * Option tarifaire et intensité souscrite.
        * Période tarifaire actuelle.
        * Puissance et intensité maximales sur 24h.
        * Prochaine période tarifaire (abonnement Tempo).
    - change : Ajout d'icônes pour illustrer les boutons de navigation. (BmdOnline)

* Graphiques
    - change : Affichage de double gauge (puissance & intensité). (energy01 & BmdOnline)
        * Une option permet de n'afficher que la puissance.
    - change : Paramétrage des seuils limites des gauges dans le fichier "config.php". (BmdOnline)
    - change : L'échelle de la gauge instantanée s'ajuste automatiquement. (energy01 & BmdOnline)
    - change : Affiche toutes les périodes tarifaires, et pas seulement "Base" ou "HP/HC". (BmdOnline)
    - change : Revue de l'affichage de la légende des graphiques quotidien et historique. (BmdOnline)
        * N'affiche plus les périodes ne correspondant pas à l'abonnement souscrit.
        * N'affiche plus les périodes de l'abonnement souscrit mais n'ayant pas de donnée (graphique historique).
    - change : Refonte de l'infobulle du graphique historique. (BmdOnline)
    - change : La couleur des séries est configurable dans le fichier "config.php". (BmdOnline)
        * Chaque période tarifaire a la même couleur dans tous les graphiques.
    - bugfix : N'affiche plus les 0 des données vides dans le graphique historique. (BmdOnline)
    - bugfix : N'affiche plus les décimales (non arrondies) des consommations dans le graphique quotidien. (BmdOnline)
    - bugfix : Correction d'un bug dans l'affichage des semaines dans le graphique historique. (BmdOnline)
        * La semaine du "30/12/2014" apparaissait "Sem 1 (2013)" au lieu de "Sem 1 (2014)". (BmdOnline)

* Moteur / PHP
    - change : Refonte complète de la gestion des requêtes MySQL. (BmdOnline)
        * Gestion des requêtes MySQL dans un fichier dédié "queries.php".
        * Le paramétrage est améliorée pour prendre en charge le maximum de configurations possible.
    - change : Refonte complète de la gestion des abonnements. (energy01 & BmdOnline)
        * Les abonnements autres que "base" ou "HC/HP" sont maintenant gérés : EJP et Tempo (Bleu Blanc Rouge).
        * L'abonnement est détecté automatiquement, il n'est plus nécessaire de le spécifier dans le programme.
    - change : Refonte complète de la gestion des tarifs. (BmdOnline)
        * Les tarifs EDF sont historisés, le calcul du coût tient compte des variations de prix.
        * Les taxes sont clairement identifiées.
        * Les évolutions de TVA sont également prises en charge.
    - change : JSON fournit la prochaine période tarifaire pour traitement éventuel. (BmdOnline)
    - change : L'utilisation de templates pour générer les pages est désactivé par défaut. (BmdOnline)
    - bugfix : Meilleure gestion des périodes vides dans le graphique historique. (BmdOnline)

##Version 4.1 (dev)
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
    - JQueryUI 1.11.0pre (2013-12-03)
    - JQueryMobile 1.4.0-rc1

##Version 4
* Ajout de la gauge de consommation instantanée.
* Ajout des boutons de navigation dans l'histogramme :
    - Choix du type de vue : jour / Semaine / Mois / Année.
    - Choix de la période : 1-7 jour / 1-52 semaines / 1-12 mois / 1-4 ans.
* Ajout d'une courbe représentant la période précédente dans l'histogramme.

* Remplacement de JQuery et Highcharts par les versions plus récentes.

##Version 3
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

##Version 2
Voir [Graphique Conso Electrique Téléinfo EDF avec Highcharts (v2)](http://penhard.anthony.free.fr/?p=207)

##Version 1
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
