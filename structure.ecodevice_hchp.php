<?php
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
        "DATE"     => "TIMESTP",   // => généralement, vaut soit "DATE", soit "TIMESTAMP"
        "OPTARIF"  => "'HC..'",    // option tarifaire souscrite
        "ISOUSC"   => "'30'",      // intensité souscrite, adapter à son abonnement
        "BASE"     => "T1_BASE",   // BASE
        "HP"       => "T1_HCHP",   // HCHP
        "HC"       => "T1_HCHC",   // HCHC
        "HPJB"     => "null",      // BBRHPJB
        "HPJW"     => "null",      // BBRHPJW
        "HPJR"     => "null",      // BBRHPJR
        "HCJB"     => "null",      // BBRHCJB
        "HCJW"     => "null",      // BBRHCJW
        "HCJR"     => "null",      // BBRHCJR
        "HN"       => "null",      // EJPN
        "HPM"      => "null",      // EJPHPM
        "PTEC"     => "T1_PTEC",   // période tarifaire en cours
        "DEMAIN"   => "null",      // prévision du lendemain (formule Tempo)
        "IINST1"   => "null",      // => généralement, vaut soit "IINST1" soit "INST1"
        "PAPP"     => "T1_PAPP"    // puissance apparente
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

?>