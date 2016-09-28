# Sommaire
* [Installation](#installation)
    * [Version actuelle] (#version-actuelle)
    * [Version dev] (#version-dev)
    * [Anciennes versions] (#anciennes-versions)
* [Configuration](#configuration)
    * [Acc�s MySQL] (#acc�s-mysql)
    * [Table t�l�info] (#table t�l�info)
* [Param�tres](#param�tres)
    * [Puissance apparente - Puissance active] (#puissance-apparente---puissance-active)
    * [Gauge instantan�e] (#gauge-instantan�e)
        * [Donn�e � afficher] (#donn�e-�-afficher)
        * [Rafraichissement automatique] (#rafraichissement-automatique)
        * [Aspect des gauges] (#aspect-des-gauges)
    * [Aper�u quotidien] (#Aper�u-quotidien)
    * [Historiques] (#historiques)
        * [Affichage 3D] (#affichage-3d)
        * [Type de graphique] (#type-de-graphique)
        * [P�riode pr�c�dente] (#p�riode-pr�c�dente)
    * [Couleur des graphiques] (#couleur-des-graphiques)
* [Tarifs EDF] (#tarifs-edf)

# Installation
## Version actuelle
Deux possibilit�s :
* Utiliser l'utilitaire `git` pour dupliquer le d�p�t :
```bash
git clone git://github.com/BmdOnline/Teleinfo.git
```

* T�l�charger et d�compressez l'archive zip � partir de l'interface `github` :

![GitHub Download ZIP](screenshots/github/GitHub Download ZIP.png)

* Lien direct de l'archive :
    * https://github.com/BmdOnline/Teleinfo/archive/master.zip
    * https://github.com/BmdOnline/Teleinfo/archive/master.tar.gz

Vous avez maintenant une copie locale du d�p�t distant.

## Version dev
Deux possibilit�s :
* Utiliser l'utilitaire `git` pour dupliquer le d�p�t :
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
## Acc�s MySQL
Pour commencer, il est n�cessaire de d�finir l'acc�s � la base MySQL et � la table T�l�info.
Dans le fichier `config.php`, il faut adapter ces quelques lignes
```php
/***********************/
/*    Donn�es MySQL    */
/***********************/
$db_connect = array (
    "serveur" => "localhost",
    "base"    => "teleinfo",
    "table"   => "tbTeleinfo",
    "login"   => "teleinfo",
    "pass"    => "teleinfo"
);
```

## Table t�l�info
Selon le syst�me utilis�, la table MySQL peut avoir des formats diff�rents.
Ce programme est fait pour s'adapter � diff�rentes structures de donn�es.

### Choix d'un mod�le d�fini
Quelques mod�les type sont propos�s :

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
| Mod�le | structure.date.php | structure.timestp.php | structure.ftimestp.php |

Dans le fichier `config.php`, il faut adapter ces quelques lignes
```php
/************************/
/*    Table T�l�Info    */
/************************/
// Selon la configuration de la base de donn�es t�l�info,
//   choisir la structure � utiliser :
// - structure.date.php
// - structure.timestp.php
// - structure.ftimestp.php
// Il est �galement possible de se cr�er une structure personnalis�e
// - structure.custom.php (par exemple)
include_once("structure.date.php");
```

A partir de l�, le programme est op�rationnel.

### Table personnalis�e
Si aucun des mod�les propos�s ne convient, il est tout � fait possible d'en cr�er un personnalis�.
Le plus simple est de partir d'un mod�le existant et de le modifier.

#### Format de date MySQL
Selon l'utilitaire collectant les donn�es t�l�information, la base peut utiliser un format de date diff�rent (date ou timestamp).
Attention � la casse (majuscule / minuscule) !
```php
$config_table = array (
    // Quelques informations sur la configuration
    "type_date" => "date", // "date" ou "timestamp" selon le type de stockage de la date
    // Nom des champs de la table.
    //   Cl�    = nom interne au programme : NE PAS MODIFIER
    //   Valeur = nom du champ dans la table t�l�info
    // Adapter les valeurs du tableau si le nom du champ est diff�rent
    "table" => array (
        "DATE"     => "DATE",    // => vaut soit "DATE", soit "TIMESTAMP"
        //...//
```

#### Nom des champs Teleinfo
Selon l'utilitaire collectant les donn�es t�l�information, la base peut utiliser des noms diff�rents.
Attention � la casse (majuscule / minuscule) !
```php
    // Nom des champs de la table.
    //   Cl�    = nom interne au programme : NE PAS MODIFIER
    //   Valeur = nom du champ dans la table t�l�info
    // Adapter les valeurs du tableau si le nom du champ est diff�rent
    "table" => array (
        "DATE"     => "DATE",    // => vaut soit "DATE", soit "TIMESTAMP"
        //...//
        "IINST1"   => "IINST1",  // => vaut soit "IINST1" soit "INST1"
        //...//
```

# Param�tres
## Puissance apparente - Puissance active
Dans le cas de faible consommation (<~180w), la puissance apparente (PAPP) de certains relev�s t�l�info ne serait pas pertinente.
Lors de relev�s t�l�info avec une fr�quence r�duite, la pertinence de la puissance apparente peut se poser.

Il est alors possible de recalculer la puissance active, en se basant sur l'index relev�.
Ce r�sultat, bien qu'approximatif (impact du cos phi), peut s'av�rer pr�f�rable � la puissance apparente.

L'option se situe dans le fichier `config.php` :
```php
/*******************************/
/*    Donn�es EDF & T�l�info   */
/*******************************/
//...//
$config["recalculPuissance"]     = false; // true : calcule la puissance en se basant sur le relev� d'index plut�t que PAPP
```

## Gauge instantan�e
### Donn�e � afficher
Il est possible d'afficher une ou deux gauges.
Dans le cas d'une seule gauge affich�e, c'est la puissance qui est s�lectionn�e.
Dans le cas de deux gauges, l'intensit� s'affichera � c�t�.

L'option se situe dans le fichier `config.php` :
```php
$config["graphiques"]["instantly"] = array(
    //...//
    "doubleGauge"  => true,      // true : affiche intensit� en plus de la puissance
    //...//
```

### Relev� de l'index du compteur
Il est possible d'afficher l'index du compteur, pour faciliter le relev� EDF.

L'option se situe dans le fichier `config.php` :
```php
    $config["afficheIndex"]          = true;  // true : affiche les index pour chaque p�riode tarifaire (relev� de compteur EDF)
```

### Rafraichissement automatique
Il est possible d'activer ou d�sactiver le rafraichissement automatique des gauges.

L'option se situe dans le fichier `config.php` :
```php
$config["graphiques"]["instantly"] = array(
    //...//
    "refreshAuto"  => true,      // active le rafraichissement automatique
    "refreshDelay" => 120,       // relanc� toutes les 120 secondes
    //...//
```

### Aspect des gauges
Il est possible de modifier les diff�rents seuils des gauges, ainsi que les couleurs associ�es.

Les options se situent dans le fichier `config.php` :
```php
// couleurs des bandes des gauges
$config["graphiques"]["instantly"] = array(
    //...//
    "bands" => array(            // couleurs des bandes des gauges
        "W" => array(            // Puissance
            300   => "#55BF3B",  // de 0 � 300
            1000  => "#DDDF0D",  // de 300 � 1000
            3000  => "#FFA500",  // de 1000 � 3000
            10000 => "#DF5353"   // sup�rieur � 3000
        ),
        "I" => array(            // Intensit�
            2   => "#55BF3B",    // de 0 � 2
            5   => "#DDDF0D",    // de 2 � 5
            13  => "#FFA500",    // de 5 � 13
            100 => "#DF5353"     // sup�rieur � 20
        )
    )
    //...//
```

## Aper�u quotidien
Ce graphique ne propose aucun r�glage sp�cifique.

## Historiques
### Affichage 3D
Il est possible de choisir un affichage 2D ou 3D des histogrammes.
Cette option est actuellement exp�rimentale. En effet, elle est nouvelle dans HighCharts et semble avoir quelques d�fauts d'affichage.

D�faut constat�s :
* Les valeurs affich�es sur les barres de l'histogramme sont parfois mal positionn�es.
* La courbe de p�riode pr�c�dente s'affiche derri�re l'histogramme au lieu de s'afficher devant.

L'option se situe dans le fichier `config.php` :
```php
$config["graphiques"]["history"] = array(
    "show3D"     => true,       // true : affiche le graphique en 3D
    //...//
```

### Type de graphique
Il est possible de choisir le type de repr�sentation des s�ries de donn�es.
Certaines combinaisons n'ont pas de sens ou sont mal g�r�es par HighCharts.
A vous de tester�

Les options se situent dans le fichier `config.php` :
```php
$config["graphiques"]["history"] = array(
    //...//
    "typeSerie"  => "column",    // Type de graphique pour les s�ries de donn�es (syntaxe HighCharts)
    "typePrec"   => "spline",    // Type de graphique pour les p�riodes pr�c�dentes (syntaxe HighCharts)
    //...//
```

### P�riode pr�c�dente
Il sera possible de choisir entre un affichage simple ou d�taill� des donn�es de la p�riode pr�c�dente.

Les options se situent dans le fichier `config.php` :
```php
$config["graphiques"]["history"] = array(
    //...//
    "detailPrec" => false,       // true : d�taille les diff�rentes p�riodes tarifaires pour les p�riodes pr�c�dentes
    //...//
```

Cette option n'est pas encore impl�ment�e.

## Couleur des graphiques
Chaque donn�e affich� en graphique a une couleur param�trable.

Pour changer les couleurs, il faut adapter le fichier `config.php` :
```php
// couleurs de chacune des s�ries des graphiques
$teleinfo["COULEURS"] = array(
    "MIN"  => "green",   // Seuil de consommation minimale sur la p�riode
    "MAX"  => "red",     // Seuil de consommation maximale sur la p�riode
    "PREC" => "#DB843D", // P�riode pr�c�dente
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
    "I"    => "blue"     // Intensit�
);
```

# Tarifs EDF
Le fichier `tarifs.php` contient l'historique de tous les tarifs EDF pour chaque formule.
Les donn�es sont nationales et communes pour tout le monde, sauf certaines taxes locales.
* Pour un calcul plus juste, il est n�cessaire d'adapter la `TCFE`.
* A chaque �volution tarifaire, il est n�cessaire d'ajouter les nouveaux tarifs dans le fichier.

