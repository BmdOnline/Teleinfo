<?php
/*********************************/
/*    Paramètres du programme    */
/*********************************/
$config["useTemplate"]           = false; // utilise les templates pour afficher les page HTML (utilise RainTPL)
$config["template"]["tpl_dir"]   = "tpl/files/"; // Attention au / final
$config["template"]["desktop"]   = "teleinfo";
$config["template"]["mobile"]    = "teleinfo.mobile";
$config["notemplate"]["desktop"] = "tpl/teleinfo.tabs.html";
$config["notemplate"]["mobile"]  = "tpl/teleinfo.tabs.mobile.html";

/*******************************/
/*    Données EDF & Téléinfo   */
/*******************************/
$config["recalculPuissance"]     = false; // true : calcule la puissance en se basant sur le relevé d'index plutôt que PAPP
$config["afficheIndex"]          = true;  // true : affiche les index pour chaque période tarifaire (relevé de compteur EDF)

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
// Selon la configuration de la base de données téléinfo,
//   choisir la structure à utiliser :
// - structure.date.php
// - structure.timestp.php
// - structure.ftimestp.php
// - structure.timestp_triphase.php
// Il est également possible de se créer une structure personnalisée
// - structure.custom.php (par exemple)
include_once("structure.date.php");


/***************************/
/*    Libellés TéléInfo    */
/***************************/
// Libellés des offres et des périodes EDF
// Attention à ne pas modifier les clés, mais seulement les libellés
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
        "HC"   => "Heures Creuses",
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

/********************************/
/*    Données des Graphiques    */
/********************************/
$config["graphiques"]["instantly"] = array(
    "refreshAuto"  => true,      // active le rafraichissement automatique
    "refreshDelay" => 120,       // relancé toutes les 120 secondes
    "intensity"    => true,      // true : affiche intensité en plus de la puissance
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
);

$config["graphiques"]["daily"] = array(
    "intensity"    => false,      // true : affiche intensité en plus de la puissance
);

$config["graphiques"]["history"] = array(
    "show3D"     => true,       // true : affiche le graphique en 3D (uniquement avec HighCharts)
    "typeSerie"  => "column",    // Type de graphique pour les séries de données (syntaxe HighCharts)
    "typePrec"   => "spline",    // Type de graphique pour les périodes précédentes (syntaxe HighCharts)
    //"typePrec"   => "column",    // Type de graphique pour les périodes précédentes (syntaxe HighCharts)
    "detailPrec" => false,       // true : détaille les différentes périodes tarifaires pour les périodes précédentes
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
    "I"    => "#0000FF",  // Intensité
    "I1"   => "#C8C8FF",  // Intensité Phase 1
    "I2"   => "#7F7FFF",  // Intensité Phase 2
    "I3"   => "#0000FF"   // Intensité Phase 3
);

?>
