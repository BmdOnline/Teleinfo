<?php
    // Historique des tarifs
    // - http://rvq.fr/php/edf.php
    //
    // EDF est réévalué tous les étés
    // Liste des arrêtés publiés au bulletin officiel
    //   https://www.legifrance.gouv.fr : rechercher "arrêté relatif aux tarifs réglementés de vente de l électricité"
    // Note EDF
    //   http://particuliers.edf.com/fichiers/fckeditor/Particuliers/Offres/CGV_CRE/CRE_TB.pdf
    //
    // Explication des taxes perçues :
    // - http://particuliers.edf.com/gestion-de-mon-contrat/ma-facture/consulter-et-comprendre-ma-facture/detail-des-taxes-et-contributions-201733.html
    //
    // TCFE est réévaluée tous les 01/01
    // - Les montants dépendent des localités : TCCFE (par communale) + TDCFE (part départementale)
    // - http://faq.edf.com/ma-facture/composition-du-prix/que-sont-les-taxes-sur-la-consommation-finale-d-electricite-tcfe-y-200835.html
    // CSPE est réévaluée arbitrairement
    // - Le montant est le même pour tout le monde
    // - http://www.cre.fr/operateurs/service-public-de-l-electricite-cspe/montant#section1
    // - http://faq.edf.com/ma-facture/composition-du-prix/qu-est-ce-que-la-contribution-aux-charges-de-service-public-de-lyelectricite-cspey-201106.html
    // CTA est réévalué avec l'abonnement
    // Le montant est calculé fonction de l'abonnement
    // - http://faq.edf.com/ma-facture/composition-du-prix/qu-est-ce-que-la-contribution-tarifaire-d-acheminement-ctay-200834.html

    // CSPE, TVA incluse
    $taxesC["CSPE"] = array(
        mktime(0,0,0, 1, 1,2001) => 1.196*0.0045, // Avant 2011
        mktime(0,0,0, 1, 1,2011) => 1.196*0.0075,
        mktime(0,0,0, 8, 1,2011) => 1.196*0.0090,
        mktime(0,0,0, 7, 1,2012) => 1.196*0.0105,
        mktime(0,0,0, 1, 1,2013) => 1.196*0.0135,
        mktime(0,0,0, 1, 1,2014) => 1.20*0.0165 // 0.016759 sur facture de février ! A vérifier
    );

    // TCFE, TVA incluse
    $taxesC["TCFE"] = array(
        mktime(0,0,0, 1, 1,2001) => 1.196*0.009, // Avant 2011
        mktime(0,0,0, 1, 1,2011) => 1.196*0.009,
        mktime(0,0,0, 1, 1,2013) => 1.196*0.00905,
        mktime(0,0,0, 1, 1,2014) => 1.20*0.00919 // Arrondi à 0.0092 sur notification EDF
    );

    // CTA (depuis le 15/08/2009), TVA incluse
    $taxesA["CTA"] = array( // Montants issus de la facture... donc pour 2 mois
        mktime(0,0,0, 8,15,2009) => 1.055*6*1.80, // 21% Arrêté du 29 décembre 2005
        mktime(0,0,0, 8,15,2010) => 1.055*6*1.86, // Date reval abonnement
        mktime(0,0,0, 7, 1,2011) => 1.055*6*1.92, // Date reval abonnement
        mktime(0,0,0, 7,23,2012) => 1.055*6*1.96, // Date reval abonnement
        mktime(0,0,0, 5, 1,2013) => 1.055*6*2.43, // 27.04% - Arrêté du 26 avril 2013, publié au JO le 30 avril 2013
        mktime(0,0,0, 8, 1,2013) => 1.055*6*2.51, // Date reval abonnement
        mktime(0,0,0, 1, 1,2014) => 1.055*6*1.98  // 1.98 sur facture de février ! A vérifier
    );

    // Tarifs, TVA incluse
    //15/08/2009
    $tarifs[mktime(0,0,0, 8,15,2009)] = array(
        "BASE.3" => 1.196*0.0781, // Base 3kVA
        "BASE.6" => 1.196*0.0784, // Base 6kVA
        "BASE.9" => 1.196*0.0817, // Base 9kVA
        "HP"     => 1.196*0.0839, // Heures Pleines
        "HC"     => 1.196*0.0519, // Heures Creuses
        "HPJB"   => 1.196*0.0495, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 1.196*0.0383, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 1.196*0.0781, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 1.196*0.0627, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 1.196*0.3793, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 1.196*0.1329, // Tempo - Rouge - Heures Creuses
        "HN"     => 1.196*0.0624, // EJP - Heures Normales
        "HPM"    => 1.196*0.3999  // EJP - Heures de Pointe Mobile
    );

    //15/08/2010
    $tarifs[mktime(0,0,0, 8,15,2010)] = array(
        "BASE.3" => 1.196*0.0793, // Base 3kVA
        "BASE.6" => 1.196*0.0798, // Base 6kVA
        "BASE.9" => 1.196*0.0817, // Base 9kVA
        "HP"     => 1.196*0.0901, // Heures Pleines
        "HC"     => 1.196*0.0557, // Heures Creuses
        "HPJB"   => 1.196*0.0510, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 1.196*0.0395, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 1.196*0.0805, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 1.196*0.0646, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 1.196*0.3907, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 1.196*0.1369, // Tempo - Rouge - Heures Creuses
        "HN"     => 1.196*0.0641, // EJP - Heures Normales
        "HPM"    => 1.196*0.4107  // EJP - Heures de Pointe Mobile
    );

    //01/07/2011
    $tarifs[mktime(0,0,0, 7, 1,2011)] = array(
        "BASE.3" => 1.196*0.0806, // Base 3kVA
        "BASE.6" => 1.196*0.0812, // Base 6kVA
        "BASE.9" => 1.196*0.0831, // Base 9kVA
        "HP"     => 1.196*0.0916, // Heures Pleines
        "HC"     => 1.196*0.0567, // Heures Creuses
        "HPJB"   => 1.196*0.0519, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 1.196*0.0402, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 1.196*0.0818, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 1.196*0.0657, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 1.196*0.3972, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 1.196*0.1392, // Tempo - Rouge - Heures Creuses
        "HN"     => 1.196*0.0652, // EJP - Heures Normales
        "HPM"    => 1.196*0.4175  // EJP - Heures de Pointe Mobile
    );

    //23/07/2012
    $tarifs[mktime(0,0,0, 7,23,2012)] = array(
        "BASE.3" => 1.196*0.0822, // Base 3kVA
        "BASE.6" => 1.196*0.0828, // Base 6kVA
        "BASE.9" => 1.196*0.0848, // Base 9kVA
        "HP"     => 1.196*0.0935, // Heures Pleines
        "HC"     => 1.196*0.0578, // Heures Creuses
        "HPJB"   => 1.196*0.0530, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 1.196*0.0410, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 1.196*0.0835, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 1.196*0.0670, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 1.196*0.4052, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 1.196*0.1420, // Tempo - Rouge - Heures Creuses
        "HN"     => 1.196*0.0665, // EJP - Heures Normales
        "HPM"    => 1.196*0.4259  // EJP - Heures de Pointe Mobile
    );

    //01/08/2013
    $tarifs[mktime(0,0,0, 8, 1,2013)] = array(
        "BASE.3" => 1.196*0.0883, // Base 3kVA
        "BASE.6" => 1.196*0.0883, // Base 6kVA
        "BASE.9" => 1.196*0.0883, // Base 9kVA
        "HP"     => 1.196*0.0998, // Heures Pleines
        "HC"     => 1.196*0.0610, // Heures Creuses
        "HPJB"   => 1.196*0.0576, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 1.196*0.0440, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 1.196*0.0907, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 1.196*0.0719, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 1.196*0.4401, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 1.196*0.1525, // Tempo - Rouge - Heures Creuses
        "HN"     => 1.196*0.0673, // EJP - Heures Normales
        "HPM"    => 1.196*0.4313  // EJP - Heures de Pointe Mobile
    );

    //01/01/2014
    $tarifs[mktime(0,0,0, 1, 1,2014)] = array(
        "BASE.3" => 1.20*0.0883, // 0,1372 TTC // Base 3kVA
        "BASE.6" => 1.20*0.0883, // 0,1372 TTC // Base 6kVA
        "BASE.9" => 1.20*0.0883, // 0,1372 TTC // Base 9kVA
        "HP"     => 1.20*0.0998, // 0,1510 TTC // Heures Pleines
        "HC"     => 1.20*0.0610, // 0,1044 TTC // Heures Creuses
        "HPJB"   => 1.20*0.0576, // 0,1003 TTC // Tempo - Bleu - Heures Pleines
        "HCJB"   => 1.20*0.0440, // 0,0840 TTC // Tempo - Bleu - Heures Creuses
        "HPJW"   => 1.20*0.0907, // 0,1400 TTC // Tempo - Blanc - Heures Pleines
        "HCJW"   => 1.20*0.0719, // 0,1175 TTC // Tempo - Blanc - Heures Creuses
        "HPJR"   => 1.20*0.4401, // 0,5593 TTC // Tempo - Rouge - Heures Pleines
        "HCJR"   => 1.20*0.1525, // 0,2142 TTC // Tempo - Rouge - Heures Creuses
        "HN"     => 1.20*0.0673, // NON REVAL. // EJP - Heures Normales
        "HPM"    => 1.20*0.4313  // NON REVAL. // EJP - Heures de Pointe Mobile
    );

    // Abonnements, TVA incluse (peut être différente selon les abonnements)
    //15/08/2009
    $abonnements[mktime(0,0,0, 8,15,2009)] = array(
        "BASE" => array(
            "3"  => 1.055*51.24,
            "6"  => 1.055*58.32,
            "9"  => 1.055*73.56,
            "12" => 1.055*127.68,
            "15" => 1.055*156.12,
            "18" => 1.055*184.56,
            "24" => 1.055*299.04,
            "30" => 1.055*413.52,
            "36" => 1.055*528.00
        ),
        "HC" => array(
            "6"  => 1.055*78.48,
            "9"  => 1.055*121.20,
            "12" => 1.055*177.12,
            "15" => 1.055*224.28,
            "18" => 1.055*271.44,
            "24" => 1.055*452.16,
            "30" => 1.055*632.88,
            "36" => 1.055*813.60
        ),
        "BBR" => array(
            "9"  => 1.055*85.80,
            "12" => 1.055*159.24,
            "15" => 1.055*159.24,
            "18" => 1.055*159.24,
            "24" => 1.055*338.16,
            "30" => 1.055*338.16,
            "36" => 1.055*423.84
        ),
        "EJP" => array(
            "9"  => 1.055*116.88,
            "12" => 1.055*116.88,
            "15" => 1.055*116.88,
            "18" => 1.055*116.88,
            "36" => 1.055*393.00
        )
    );

    //15/08/2010
    $abonnements[mktime(0,0,0, 8,15,2010)] = array(
        "BASE" => array(
            "3"  => 1.055*53.52,
            "6"  => 1.055*53.24,
            "9"  => 1.055*73.32,
            "12" => 1.055*113.88,
            "15" => 1.055*130.80,
            "18" => 1.055*178.20,
            "24" => 1.055*288.72,
            "30" => 1.055*399.24,
            "36" => 1.055*509.76
        ),
        "HC" => array(
            "6"  => 1.055*76.32,
            "9"  => 1.055*90.96,
            "12" => 1.055*150.96,
            "15" => 1.055*177.12,
            "18" => 1.055*201.12,
            "24" => 1.055*432.00,
            "30" => 1.055*532.32,
            "36" => 1.055*613.20
        ),
        "BBR" => array(
            "9"  => 1.055*88.44,
            "12" => 1.055*164.04,
            "15" => 1.055*164.04,
            "18" => 1.055*164.04,
            "24" => 1.055*348.36,
            "30" => 1.055*348.36,
            "36" => 1.055*436.56
        ),
        "EJP" => array(
            "9"  => 1.055*120.00,
            "12" => 1.055*120.00,
            "15" => 1.055*120.00,
            "18" => 1.055*120.00,
            "36" => 1.055*403.56
        )
    );

    //01/07/2011
    $abonnements[mktime(0,0,0, 7, 1,2011)] = array(
        "BASE" => array(
            "3"  => 1.055*51.18,
            "6"  => 1.055*64.32,
            "9"  => 1.055*71.52,
            "12" => 1.055*115.80,
            "15" => 1.055*133.08,
            "18" => 1.055*181.20,
            "24" => 1.055*293.64,
            "30" => 1.055*406.08,
            "36" => 1.055*518.40
        ),
        "HC" => array(
            "6"  => 1.055*77.64,
            "9"  => 1.055*92.52,
            "12" => 1.055*153.60,
            "15" => 1.055*180.12,
            "18" => 1.055*204.60,
            "24" => 1.055*439.44,
            "30" => 1.055*544.44,
            "36" => 1.055*623.64
        ),
        "BBR" => array(
            "9"  => 1.055*89.88,
            "12" => 1.055*166.80,
            "15" => 1.055*166.80,
            "18" => 1.055*166.80,
            "24" => 1.055*351.42,
            "30" => 1.055*351.42,
            "36" => 1.055*443.88
        ),
        "EJP" => array(
            "9"  => 1.055*122.04,
            "12" => 1.055*122.04,
            "15" => 1.055*122.04,
            "18" => 1.055*122.04,
            "36" => 1.055*410.16
        )
    );

    //23/07/2012
    $abonnements[mktime(0,0,0, 7,23,2012)] = array(
        "BASE" => array(
            "3"  => 1.055*55.56,
            "6"  => 1.055*65.64,
            "9"  => 1.055*76.08,
            "12" => 1.055*118.08,
            "15" => 1.055*135.72,
            "18" => 1.055*184.92,
            "24" => 1.055*299.52,
            "30" => 1.055*414.24,
            "36" => 1.055*528.84
        ),
        "HC" => array(
            "6"  => 1.055*79.20,
            "9"  => 1.055*94.44,
            "12" => 1.055*156.72,
            "15" => 1.055*183.72,
            "18" => 1.055*208.68,
            "24" => 1.055*448.32,
            "30" => 1.055*552.36,
            "36" => 1.055*636.24
        ),
        "BBR" => array(
            "9"  => 1.055*91.68,
            "12" => 1.055*170.16,
            "15" => 1.055*170.16,
            "18" => 1.055*170.16,
            "24" => 1.055*361.32,
            "30" => 1.055*361.32,
            "36" => 1.055*452.88
        ),
        "EJP" => array(
            "9"  => 1.055*124.56,
            "12" => 1.055*124.56,
            "15" => 1.055*124.56,
            "18" => 1.055*124.56,
            "36" => 1.055*418.44
        )
    );

    //01/08/2013
    $abonnements[mktime(0,0,0, 8, 1,2013)] = array(
        "BASE" => array(
            "3"  => 1.055*38.88,
            "6"  => 1.055*66.72,
            "9"  => 1.055*89.76,
            "12" => 1.055*135.00,
            "15" => 1.055*153.84,
            "18" => 1.055*176.76,
            "24" => 1.055*366.72,
            "30" => 1.055*453.96,
            "36" => 1.055*522.84
        ),
        "HC" => array(
            "6"  => 1.055*71.64,
            "9"  => 1.055*97.44,
            "12" => 1.055*156.12,
            "15" => 1.055*180.00,
            "18" => 1.055*201.24,
            "24" => 1.055*420.12,
            "30" => 1.055*492.36,
            "36" => 1.055*562.68
        ),
        "BBR" => array(
            "9"  => 1.055*96.60,
            "12" => 1.055*152.76,
            "15" => 1.055*176.04,
            "18" => 1.055*190.92,
            "24" => 1.055*471.84,
            "30" => 1.055*471.84,
            "36" => 1.055*583.56
        ),
        "EJP" => array(
            "9"  => 1.055*92.04,
            "12" => 1.055*137.16,
            "15" => 1.055*156.12,
            "18" => 1.055*173.64,
            "36" => 1.055*521.16
        )
    );

// Revoie les tarifs, avec historique
function getTarifsEDF($optarif, $isousc) {
    global $taxesC, $taxesA, $abonnements, $tarifs;
    global $teleinfo;

    // Calcul de la puissance fonction de l'intensité
    //   isousc = 30 A => 6 kWh
    //   isousc = 45 A => 9 kWh
    $puissance = $isousc / 5;

    // Suffixe fonction de la puissance (sert pour les tarifs)
    if ($optarif == "BASE") {
        $suffix = "." . $puissance;
    } else {
        $suffix = "";
    }

    // Liste des périodes associées à l'offre souscrite (sert pour les tarifs)
    $periodes = array();
    foreach ($teleinfo["PERIODES"][$optarif] as $periode) {
        $periodes[$periode] = $periode . $suffix;
    }


    // Partie Taxes C (type consommation, dépend de la conso)
    foreach($taxesC as $tkey => $taxes) {
        ksort($taxes);
        $tab_taxesC[$tkey] = $taxes;
    };

    // Partie Taxes A (type abonnement, ne dépend pas de la conso)
    foreach($taxesA as $tkey => $taxes) {
        ksort($taxes);
        $tab_taxesA[$tkey] = $taxes;
    };

    // Partie Abonnement
    $tab_abonnements = array();
    foreach($abonnements as $date => $abonnement) {
        $tab_abonnements[$date] = array(
            $optarif => $abonnement[$optarif][$puissance]
        );
    }
    ksort($tab_abonnements);

    // Partie Consommation
    $tab_tarifs = array();
    foreach ($tarifs as $date => $tarif) {
        foreach ($periodes as $periode => $index) {
            $tab_tarifs[$date][$periode] = $tarif[$index];
        }
    }
    ksort($tab_tarifs);

    // Tableaux à retourner
    $tab_prix = array(
        "TAXES_C" => $tab_taxesC,
        "TAXES_A" => $tab_taxesA,
        "ABONNEMENTS" => $tab_abonnements,
        "TARIFS" => $tab_tarifs,
    );

    return $tab_prix;
}

// Extrait les tarifs correpondants à une date donnée
// 2 appels possibles :
// - 2 arguments : ($tab_prix, $date)
// - 3 arguments : ($optarif, $isousc, $date)
//
function getTarifs () {
    switch (func_num_args()) {
        case 2: // 2 arguments passés en paramètre
            $tab_prix = func_get_arg(0);
            $date    = func_get_arg(1);
            break;
        case 3: // 3 arguments passés en paramètre
            $optarif = func_get_arg(0);
            $isousc  = func_get_arg(1);

            $tab_prix = getTarifsEDF ($optarif, $isousc);
            $date    = func_get_arg(2);
            break;
        default:
            $tab_prix = null;
            $date = null;
            break;
    };

    // Pour chaque taxeC
    foreach($tab_prix["TAXES_C"] as $tkey => $tval) {
        //$cur_prix["TAXES_C"][$tkey] = end($tval);
        krsort($tval);
        reset($tval);
        while (key($tval) > $date) {
            next($tval);
        }
        $cur_prix["TAXES_C"][$tkey] = current($tval);
    }

    // Pour chaque taxeA
    foreach($tab_prix["TAXES_A"] as $tkey => $tval) {
        //$cur_prix["TAXES_A"][$tkey] = end($tval);
        krsort($tval);
        reset($tval);
        while (key($tval) > $date) {
            next($tval);
        }
        $cur_prix["TAXES_A"][$tkey] = current($tval);
    }

    // Puis les abonnement
    //$cur_prix["ABONNEMENTS"] = end($tab_prix["ABONNEMENTS"]);
    krsort($tab_prix["ABONNEMENTS"]);
    reset($tab_prix["ABONNEMENTS"]);
    while (key($tab_prix["ABONNEMENTS"]) > $date) {
        next($tab_prix["ABONNEMENTS"]);
    }
    $cur_prix["ABONNEMENTS"] = current($tab_prix["ABONNEMENTS"]);

    // Et enfin les tarifs
    //$cur_prix["TARIFS"] = end($tab_prix["TARIFS"]);
    krsort($tab_prix["TARIFS"]);
    reset($tab_prix["TARIFS"]);
    while (key($tab_prix["TARIFS"]) > $date) {
        next($tab_prix["TARIFS"]);
    }
    $cur_prix["TARIFS"] = current($tab_prix["TARIFS"]);


/*    // Pour chaque taxeC
    foreach($tab_prix["TAXES_C"] as $tkey => $tval) {
        $cur_prix["TAXES_C"][$tkey] = end($tval);
    }
    // Pour chaque taxeA
    foreach($tab_prix["TAXES_A"] as $tkey => $tval) {
        $cur_prix["TAXES_A"][$tkey] = end($tval);
    }
    // Puis les abonnement
    $cur_prix["ABONNEMENTS"] = end($tab_prix["ABONNEMENTS"]);
    // Et enfin les tarifs
    $cur_prix["TARIFS"] = end($tab_prix["TARIFS"]); /* */

    return $cur_prix;
}

?>