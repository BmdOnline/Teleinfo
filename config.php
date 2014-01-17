<?php

/***********************/
/*    Données MySQL    */
/***********************/
$db_serveur="localhost";
$db_base="teleinfo";
$db_table="teleinfo";
$db_login="teleinfo";
$db_pass="teleinfo";

// Nom des champs de la table Teleinfo
//   Adapter ici le nom des champs en cas de structures de données légèrement différentes
//     Champs habituellement différents d'une environnement à l'autre
$db_teleinfo ["date"]    = "date"; // vaut soit "date" soit "timestamp"
$db_teleinfo ["iinst"]   = "iinst1"; // vaut soit "iinst1" soit "inst1"
//     Champs ayant peu de chances d'avoir une autre appellation
$db_teleinfo ["optarif"] = "optarif"; // afin de récupérer l'option souscrite
$db_teleinfo ["demain"]   = "demain"; // afin de récupérer la prévision du lendemain (formule Tempo)

// Ne pas modifier ces quelques lignes
$db_timestamp = array(
    "date" => "UNIX_TIMESTAMP(date)",
    "timestamp" => "timestamp");
$db_rec_date = array (
    "date" => "DATE(date)",
    "timestamp" => "rec_date");
$db_select_date = array (
    "date" => "UNIX_TIMESTAMP(date) as timestamp, DATE(date) as rec_date, TIME(date) as rec_time",
    "timestamp" => "timestamp, rec_date, rec_time");
$db_select_mesures = array (
    "iinst1" => "ptec, papp, iinst1",
    "inst1" => "ptec, papp, inst1 as iinst1");
$db_select_max_mesures = array (
    "iinst1" => "MAX(papp) AS maxpapp, MAX(iinst1) AS maxiinst1",
    "inst1" => "MAX(papp) AS maxpapp, MAX(inst1) AS maxiinst1");

// Liste des champs de la table, pour chaque option tarifaire
$liste_ptec = array(
    "BASE" => array (
        "BASE" => "Heures de Base"
    ),
    "HC.." => array (
        "HP" => "Heures Pleines",
        "HC" => "Heures Creuse"
    ),
    //"BBRX" => array ( // A priori, la trame téléinfo renvoie BBR.
    "BBR" => array (
        "HPJB" => "Heures Pleines Jours Bleus",
        "HPJW" => "Heures Pleines Jours Blancs",
        "HPJR" => "Heures Pleines Jours Rouges",
        "HCJB" => "Heures Creuses Jours Bleus",
        "HCJW" => "Heures Creuses Jours Blancs",
        "HCJR" => "Heures Creuses Jours Rouges"
    ),
    "EJP." => array (
        "HN" => "Heures Normales",
        "HPM" => "Heures de Pointe Mobile"
    )
);

/*********************/
/*    Données EDF    */
/*********************/
$nbPhasesCompteur = 1; // 1 pour monophasé ou 3 pour triphasé

$refresh_auto = true; // active le rafraichissement automatique
$refresh_delay = 120; // relancé toutes les 120 secondes

// Quelques informations sur Teleinfo et les formules EDF :
//   http://www.yadnet.com/index.php?page=protocole-teleinfo

// Revoie les tarifs, avec hitorique
function getTarifs($optarif) {
    // prix du kWh :
    switch($optarif) {
        // Tarif de base
        case "BASE" :
            $tab_prix[mktime(0,0,0,01,01,2012)] = array(
                "date" => "1/01/2012",
                "AboAnnuel" => 12*(5.36+1.92/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => (0.0812+0.009+0.009)*1.196 // kWh + CSPE + TCFE, TVA 19.6%
                )
            );
            $tab_prix[mktime(0,0,0,07,23,2012)] = array(
                "date" => "23/07/2012",
                "AboAnnuel" => 12*(7.34+1.92/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => (0.1249+0.009+0.009)*1.196 // kWh + CSPE + TCFE, TVA 19.6%
                )
            );
            $tab_prix[mktime(0,0,0,08,01,2013)] = array(
                "date" => "01/08/2013",
                "AboAnnuel" => 125.13*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => 0.1329 // kWh + CSPE + TCFE, TVA 19.6%
                )
            );
            break;

        // Tarif Heures Creuses / Heures pleines
        case "HC.." :
            $tab_prix[mktime(0,0,0,01,01,2012)] = array(
                "date" => "1/01/2012",
                "AboAnnuel" => 112.87, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "HP" => 0.1312,
                    "HC" => 0.0895
                )
            );
            $tab_prix[mktime(0,0,0,07,23,2012)] = array(
                "date" => "23/07/2012",
                "AboAnnuel" => 12*(9.07+1.92/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "HP" => 0.1353,
                    "HC" => 0.0926
                )
            );
            $tab_prix[mktime(0,0,0,08,01,2013)] = array(
                "date" => "01/08/2013",
                "AboAnnuel" => 137.01*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "HP" => 0.1467,
                    "HC" => 0.1002
                )
            );
            break;

        // Tarif Tempo
        case "BBR" :
        //case "BBRX" : // A priori, la trame téléinfo renvoie BBR.
            $tab_prix[mktime(0,0,0,07,23,2012)] = array(
                "date" => "23/07/2012",
                "AboAnnuel" => 12*(8.84+1.92/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "HPJB" => 0.0725,
                    "HCJB" => 0.0869,
                    "HPJW" => 0.1036,
                    "HCJW" => 0.1234,
                    "HPJR" => 0.1933,
                    "HCJR" => 0.5081
                )
            );
            break;


        // Tarif EJP (a définir)
        case "EJP." :
            $tab_prix[mktime(0,0,0,07,23,2012)] = array(
                "date" => "23/07/2012",
                "AboAnnuel" => 12*(8.84+1.92/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "HN" => 0,
                    "HPM" => 0
                )
            );
            break;
    };

    return $tab_prix;
}

?>
