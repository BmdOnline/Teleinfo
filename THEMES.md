# Sommaire
* [Templates] (#templates)
    * [Gestion par fichiers HTML] (#gestion-par-fichiers-html)
    * [Gestion par templates] (#gestion-par-templates)
* [Thèmes] (#thèmes)

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