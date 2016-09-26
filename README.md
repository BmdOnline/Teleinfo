# Graphique Conso Electrique Téléinfo EDF avec Highcharts

[![Animation](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/animation_small.gif)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/animation.gif)

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
    * [Gestion par fichiers HTML] (#gestion-par-fichiers-html)
    * [Gestion par templates] (#gestion-par-templates)
* [Thèmes] (#thèmes)
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
| v4.5.1 | https://github.com/BmdOnline/Teleinfo/archive/v4.5.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.5.1.tar.gz |
| v4.5 | https://github.com/BmdOnline/Teleinfo/archive/v4.5.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.5.tar.gz |
| v4.4dev | https://github.com/BmdOnline/Teleinfo/archive/v4.4dev.zip <br> https://github.com/BmdOnline/Teleinfo/archive/v4.4dev.tar.gz |
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

### Relevé de l'index du compteur
Il est possible d'afficher l'index du compteur, pour faciliter le relevé EDF.

L'option se situe dans le fichier `config.php` :
```php
    $config["afficheIndex"]          = true;  // true : affiche les index pour chaque période tarifaire (relevé de compteur EDF)
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

[![Puissance Instantanée](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_inst_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_inst.png)

* Onglet _Consommation Actuelle_

[![Dernières 24h](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_day_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_day.png)

* Onglet _Données Historiques_ (2D)

[![Consommation sur 8 jours](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_hist_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_hist.png)

* Onglet _Données Historiques_ (3D)

[![Consommation sur 8 jours](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_hist_3D_small.png)](https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_hist_3D.png)

* Formule de base
    * [Affichage simple] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/base_single.png)
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/base_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/base_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/base_tab_hist.png)
    * [Onglet _Historique_ (3D)] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/base_tab_hist_3D.png)

* Formule HP/HC
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_hist.png)
    * [Onglet _Historique_ (3D)] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/hphc_tab_hist_3D.png)

* Formule Tempo
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/tempo_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/tempo_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/tempo_tab_hist.png)
    * [Onglet _Historique_ (3D)] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/tempo_tab_hist_3D.png)

* Version Mobile
    * [Affichage simple] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/mobile_single.png)
    * [Onglet _Instantané_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/mobile_tab_inst.png)
    * [Onglet _24h_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/mobile_tab_day.png)
    * [Onglet _Historique_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/mobile_tab_hist.png)

* Thèmes
    * [Thème _ui-darkhness_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/theme_ui-darkness.png)
    * [Thème _ui-lightness_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/theme_ui-lightness.png)
    * [Thème _smoothness_] (https://github.com/BmdOnline/Teleinfo/raw/master/screenshots/v4.5.1/theme_smoothness.png)

# Changements
Voir le fichier [`CHANGELOG.md`](CHANGELOG.md) pour une liste détaillée des nouveautés, corrections de bugs...