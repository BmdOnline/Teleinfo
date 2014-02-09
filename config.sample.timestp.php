<?php
/*********************************/
/*    Paramètres du programme    */
/*********************************/
$config["refreshAuto"]           = true;  // active le rafraichissement automatique
$config["refreshDelay"]          = 120;   // relancé toutes les 120 secondes
$config["doubleGauge"]           = true;  // true : affiche intensité en plus de la puissance


$config["useTemplate"]           = false; // utilise les templates pour afficher les page HTML (utilise RainTPL)
$config["template"]["desktop"]   = "teleinfo";
$config["template"]["mobile"]    = "teleinfo.mobile";
$config["notemplate"]["desktop"] = "tpl/teleinfo.tabs.html";
$config["notemplate"]["mobile"]  = "tpl/teleinfo.tabs.mobile.html";

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

/************************/
/*    Table TéléInfo    */
/************************/

// Quelques informations sur Teleinfo et les formules EDF :
//   http://norm.edf.fr/pdf/HN44S812emeeditionMars2007.pdf
//   http://www.yadnet.com/index.php?page=protocole-teleinfo

// Ces données permettent au programme de fonctionner avec différentes structures de données
$config_table = array (
    // Quelques informations sur la configuration
    "type_date" => "timestamp", // "date" ou "timestamp" selon le type de stockage de la date
    // Nom des champs de la table.
    //   Clé    = nom interne au programme : NE PAS MODIFIER
    //   Valeur = nom du champ dans la table téléinfo
    // Adapter les valeurs du tableau si le nom du champ est différent
    "table" => array (
        "DATE"     => "TIMESTAMP", // => généralement, vaut soit "DATE", soit "TIMESTAMP"
        "OPTARIF"  => "OPTARIF",   // option tarifaire souscrite
        "ISOUSC"   => "ISOUSC",    // intensité souscrite
        "BASE"     => "BASE",      // BASE
        "HP"       => "HCHP",      // HCHP
        "HC"       => "HCHC",      // HCHC
        "HPJB"     => "BBRHPJB",   // BBRHPJB
        "HPJW"     => "BBRHPJW",   // BBRHPJW
        "HPJR"     => "BBRHPJR",   // BBRHPJR
        "HCJB"     => "BBRHCJB",   // BBRHCJB
        "HCJW"     => "BBRHCJW",   // BBRHCJW
        "HCJR"     => "BBRHCJR",   // BBRHCJR
        "HN"       => "EJPHN",     // EJPN
        "HPM"      => "EJPHPM",    // EJPHPM
        "PTEC"     => "PTEC",      // période tarifaire en cours
        "DEMAIN"   => "null",      // prévision du lendemain (formule Tempo)
        "IINST1"   => "INST1",     // => généralement, vaut soit "IINST1" soit "INST1"
        "PAPP"     => "PAPP"       // puissance apparente
    )
);

/**************************/
/*    Données TéléInfo    */
/**************************/

// Liste des valeurs possibles pour le champ "OPTARIF"
//   Clé    = valeur OPTARIF reçue par le signal Teleinfo
//   Valeur = nom interne au programme : NE PAS MODIFIER
// Adapter les clés du tableau si le contenu du champ est différent
$teleinfo["OPTARIF"] = array(
    "BASE" => "BASE",
    "HC.." => "HC",
    "BBR"  => "BBR",
    "EJP." => "EPJ"
);

// Liste des valeurs possibles pour le champ "PTEC"
//   Clé    = valeur PTEC reçue par le signal Teleinfo
//   Valeur = nom interne au programme : NE PAS MODIFIER
// Adapter les clés du tableau si le contenu du champ est différent
$teleinfo["PTEC"] = array(
    "TH.." => "BASE",
    "HP"   => "HP",
    "HC"   => "HC",
    "HPJB" => "HPJB",
    "HPJW" => "HPJW",
    "HPJR" => "HPJR",
    "HCJB" => "HCJB",
    "HCJW" => "HCJW",
    "HCJR" => "HCJR",
    "HN.." => "HN",
    "PM.." => "HPM"
);

// Liste des periodes, pour chaque option tarifaire
$teleinfo["PERIODES"] = array(
    "BASE" => array ("BASE"),
    "HC"   => array ("HP", "HC"),
    "BBR"  => array ("HPJB", "HPJW", "HPJR", "HCJB", "HCJW", "HCJR"),
    "EJP"  => array ("HN", "HPM")
);

// Description des offres et des périodes EDF
$teleinfo["LIBELLES"] = array(
    "OPTARIF" => array (
        "BASE" => "EDF Bleu option Base",
        "HC"   => "EDF Bleu options Base + Heures Creuses",
        "BBR"  => "EDF Bleu Blanc Rouge (Tempo)",
        "EPJ"  => "EDF EJP (Effacement des Jours de Pointe)"
    ),
    "PTEC" => array (
        "BASE" => "Heures de Base",
        "HP"   => "Heures Pleines",
        "HC"   => "Heures Creuse",
        "HPJB" => "Heures Pleines Jours Bleus",
        "HPJW" => "Heures Pleines Jours Blancs",
        "HPJR" => "Heures Pleines Jours Rouges",
        "HCJB" => "Heures Creuses Jours Bleus",
        "HCJW" => "Heures Creuses Jours Blancs",
        "HCJR" => "Heures Creuses Jours Rouges",
        "HN"   => "Heures Normales",
        "HPM"  => "Heures de Pointe Mobile"
    )
);

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

// couleurs des bandes des gauges
$teleinfo["BANDS"] = array(
    "W" => array(
        300   => "#55BF3B", // de 0 à 300
        1000  => "#DDDF0D", // de 300 à 1000
        3000  => "#FFA500", // de 1000 à 3000
        10000 => "#DF5353"  // supérieur à 3000
    ),
    "I" => array(
        5   => "#55BF3B", // de 0 à 5
        10  => "#DDDF0D", // de 5 à 10
        20  => "#FFA500", // de 10 à 20
        100 => "#DF5353"  // supérieur à 20
    )
);

/*********************/
/*    Données EDF    */
/*********************/
$config["nbPhasesCompteur"] = 1;    // 1 pour monophasé ou 3 pour triphasé
?>
