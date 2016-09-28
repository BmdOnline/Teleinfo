# Sommaire
* [Templates] (#templates)
    * [Gestion par fichiers HTML] (#gestion-par-fichiers-html)
    * [Gestion par templates] (#gestion-par-templates)
* [Th�mes] (#th�mes)

# Templates
Il est possible de choisir entre plusieurs mises en page des graphiques.
Deux choix sont propos�s :
* Sous forme d'onglets
* Sous forme lin�raire

Chacune des 2 mises en page est pr�vue en version "desktop" et en version "mobile".

L'application est �galement capable de g�n�rer les pages � partir de templates complexes grace � la librairie `RainTPL`.
Par d�faut, cette option est d�sativ�e : l'affichage utilise des fichiers HTML pr�par�s.

## Gestion par fichiers HTML
Des fichiers sont propos�s pour chacun des modes d'affichage.

Pour changer de mod�le, il faut adapter le fichier `config.php` :
```php
/*********************************/
/*    Param�tres du programme    */
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
Le moteur de template utilis� est `RainTPL`. Il est possible de modifier les pages en utilisant la syntaxe ad�quate.

Pour changer de mod�le, il faut adapter le fichier `config.php` :
```php
/*********************************/
/*    Param�tres du programme    */
/*********************************/
$config["useTemplate"]           = true; // utilise les templates pour afficher les page HTML (utilise RainTPL)
$config["template"]["tpl_dir"]   = "tpl/files/"; // Attention au / final
$config["template"]["desktop"]   = "teleinfo";
$config["template"]["mobile"]    = "teleinfo.mobile";
//...//
```

Il faut �galement remplacer le contenu du r�pertoire `tpl/files`�
* Pour la vesion desktop, depuis :
    - `tpl/files/desktop - lineaire`
    - `tpl/files/desktop - onglets`
* Pour la vesion mobile, depuis :
    - `tpl/files/mobile - lineaire`
    - `tpl/files/mobile - onglets`
* Dans tous les cas, en ajoutant les fichiers communs depuis :
    - `tpl/files/commun`

Important :
A chaque changement de template, ne pas oublier de vider le contenu du r�pertoire `cache`.

Remarque :
Pour le bon fonctionnement du programme, il faut choisir un template desktop ET un template mobile.
Par d�faut, le programme est r�gl� sur les templates avec onglets.

# Th�mes
Actuellement, 3 th�mes sont propos�s (classique, clair & sombre).
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
Par d�faut, le programme est r�gl� sur le th�me _sombre_.

* En utilisant `RainTPL`, ne pas oublier le `#` en fin d'URL.
```php
<link rel="stylesheet" href="./css/ui-darkness/jquery-ui-1.11.0-pre.min.css#">
```