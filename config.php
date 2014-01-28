<?php

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

// Ces données permettent au programme de fonctionner avec différentes structures de données
$config_table = array (
    // Quelques informations sur la configuration
    "type_date" => "date", // "date" ou "timestamp" selon le type de stockage de la date
    // Nom des champs de la table.
    //   Clé    = nom interne au programme : NE PAS MODIFIER
    //   Valeur = nom du champ dans la table téléinfo
    // Adapter les valeurs du tableau si le nom du champ est différent
    "table" => array (
        "DATE"     => "DATE",    // => vaut soit "date", soit "timestamp"
        "OPTARIF"  => "OPTARIF", // option tarifaire souscrite
        "ISOUSC"   => "ISOUSC",  // intensité souscrite
        "BASE"     => "BASE",    // BASE
        "HP"       => "HCHP",    // HCHP
        "HC"       => "HCHC",    // HCHC
        "HPJB"     => "BBRHPJB", // BBRHPJB
        "HPJW"     => "BBRHPJW", // BBRHPJW
        "HPJR"     => "BBRHPJR", // BBRHPJR
        "HCJB"     => "BBRHCJB", // BBRHCJB
        "HCJW"     => "BBRHCJW", // BBRHCJW
        "HCJR"     => "BBRHCJR", // BBRHCJR
        "HN"       => "EJPHN",   // EJPN
        "HPM"      => "EJPHPM",  // EJPHPM
        "PTEC"     => "PTEC",    // période tarifaire en cours
        "DEMAIN"   => "DEMAIN",  // prévision du lendemain (formule Tempo)
        "IINST1"   => "IINST1",  // => vaut soit "iinst1" soit "inst1"
        "PAPP"     => "PAPP"     // puissance apparente
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

/*********************/
/*    Données EDF    */
/*********************/
$nbPhasesCompteur = 1; // 1 pour monophasé ou 3 pour triphasé

$refresh_auto = true; // active le rafraichissement automatique
$refresh_delay = 120; // relancé toutes les 120 secondes

// Quelques informations sur Teleinfo et les formules EDF :
//   http://norm.edf.fr/pdf/HN44S812emeeditionMars2007.pdf
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
                "AboAnnuel" => 12*(5.47+1.96/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => (0.0828+0.0105+0.009)*1.196 // kWh + CSPE + TCFE, TVA 19.6%
                )
            );
            $tab_prix[mktime(0,0,0,01,01,2013)] = array( // Augmentation CSPE + TCFE
                "date" => "01/01/2013",
                "AboAnnuel" => 12*(5.47+1.96/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => (0.0828+0.0135+0.00905)*1.196 // kWh + CSPE + TCFE, TVA 19.6%
                )
            );
            $tab_prix[mktime(0,0,0,05,01,2013)] = array( // Augmentation CTA
                "date" => "01/05/2013",
                "AboAnnuel" => 12*(5.47+2.43/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => (0.0828+0.0135+0.00905)*1.196 // kWh + CSPE + TCFE, TVA 19.6%
                )
            );
            $tab_prix[mktime(0,0,0,08,28,2013)] = array( // Augmentation Abonnement + CTA + kWh
                "date" => "28/08/2013",
                "AboAnnuel" => 12*(5.56+2.51/2)*1.055, // Abonnement + CTA, TVA 5.5%
                "periode" => array(
                    "BASE" => (0.0883+0.0135+0.00905)*1.196 // kWh + CSPE + TCFE, TVA 19.6%
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
                    "HPJB" => 0.0869,
                    "HCJB" => 0.0725,
                    "HPJW" => 0.1234,
                    "HCJW" => 0.1036,
                    "HPJR" => 0.5081,
                    "HCJR" => 0.1933
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
