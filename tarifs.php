<?php
    // Historique des tarifs
    // - http://rvq.fr/php/edf.php
    //
    // EDF est réévalué tous les étés
    // - 15/08/2009 (http://www.leparticulier.fr/jcms/c_76396/arrete-13082009-tarif-reglemente-electricite)
    // - 15/08/2010 (http://www.energie2007.fr/images/upload/jo_130810_trv_1.pdf)
    // - 01/07/2011 (http://www-rodin.cea.fr/home/liblocal/docs/Textes%20actualite/ARE28062011v0.pdf)
    // - 23/07/2012 (http://www.legifrance.gouv.fr/jopdf/common/jo_pdf.jsp?numJO=0&dateJO=20120722&numTexte=7&pageDebut=12068&pageFin=12079)
    // - 01/08/2013 (http://www.legifrance.gouv.fr/jopdf/common/jo_pdf.jsp?numJO=0&dateJO=20130731&numTexte=21&pageDebut=12815&pageFin=12830)
    //
    // Explication des taxes perçues :
    // - http://particuliers.edf.com/gestion-de-mon-contrat/ma-facture/consulter-et-comprendre-ma-facture/detail-des-taxes-et-contributions-201733.html
    //
    // TCFE est réévaluée tous les 01/01
    // - Les montants dépendent des localités : TCCFE (par communale) + TDCFE (part départementale)
    // - http://faq.edf.com/ma-facture/composition-du-prix/que-sont-les-taxes-sur-la-consommation-finale-d-electricite-tcfe-y-200835.html
    // CSPE est réévaluée arbitrairement
    // - Le montant est le même pour tout le monde
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
        mktime(0,0,0, 1, 1,2014) => 1.196*0.0165
    );

    // TCFE, TVA incluse
    $taxesC["TCFE"] = array(
        mktime(0,0,0, 1, 1,2001) => 1.196*0.009, // Avant 2011
        mktime(0,0,0, 1, 1,2011) => 1.196*0.009,
        mktime(0,0,0, 1, 1,2013) => 1.196*0.00905
    );

    // CTA (depuis le 15/08/2009), TVA incluse
    $taxesA["CTA"] = array(
        mktime(0,0,0, 8,15,2009) => 1.055*12*1.80, // 21% Arrêté du 29 décembre 2005
        mktime(0,0,0, 8,15,2010) => 1.055*12*1.86, // Date reval abonnement
        mktime(0,0,0, 7, 1,2011) => 1.055*12*1.92, // Date reval abonnement
        mktime(0,0,0, 7,23,2012) => 1.055*12*1.96, // Date reval abonnement
        mktime(0,0,0, 5, 1,2013) => 1.055*12*2.43, // 27.04% - Arrêté du 26 avril 2013, publié au JO le 30 avril 2013
        mktime(0,0,0, 8, 1,2013) => 1.055*12*2.51  // Date reval abonnement
    );

    // Tarifs, TVA isolée
    //15/08/2009
    $tarifs[mktime(0,0,0, 8,15,2009)] = array(
        "TVA"    => 1.196,
        "BASE.3" => 0.0781, // Base 3kVA
        "BASE.6" => 0.0784, // Base 6kVA
        "BASE.9" => 0.0817, // Base 9kVA
        "HP"     => 0.0839, // Heures Pleines
        "HC"     => 0.0519, // Heures Creuses
        "HPJB"   => 0.0495, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 0.0383, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 0.0781, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 0.0627, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 0.3793, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 0.1329, // Tempo - Rouge - Heures Creuses
        "HN"     => 0.0624, // EJP - Heures Normales
        "HPM"    => 0.3999  // EJP - Heures de Pointe Mobile
    );

    //15/08/2010
    $tarifs[mktime(0,0,0, 8,15,2010)] = array(
        "TVA"    => 1.196,
        "BASE.3" => 0.0793, // Base 3kVA
        "BASE.6" => 0.0798, // Base 6kVA
        "BASE.9" => 0.0817, // Base 9kVA
        "HP"     => 0.0901, // Heures Pleines
        "HC"     => 0.0557, // Heures Creuses
        "HPJB"   => 0.0510, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 0.0395, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 0.0805, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 0.0646, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 0.3907, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 0.1369, // Tempo - Rouge - Heures Creuses
        "HN"     => 0.0641, // EJP - Heures Normales
        "HPM"    => 0.4107  // EJP - Heures de Pointe Mobile
    );

    //01/07/2011
    $tarifs[mktime(0,0,0, 7, 1,2011)] = array(
        "TVA"    => 1.196,
        "BASE.3" => 0.0806, // Base 3kVA
        "BASE.6" => 0.0812, // Base 6kVA
        "BASE.9" => 0.0831, // Base 9kVA
        "HP"     => 0.0916, // Heures Pleines
        "HC"     => 0.0567, // Heures Creuses
        "HPJB"   => 0.0519, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 0.0402, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 0.0818, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 0.0657, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 0.3972, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 0.1392, // Tempo - Rouge - Heures Creuses
        "HN"     => 0.0652, // EJP - Heures Normales
        "HPM"    => 0.4175  // EJP - Heures de Pointe Mobile
    );

    //23/07/2012
    $tarifs[mktime(0,0,0, 7,23,2012)] = array(
        "TVA"    => 1.196,
        "BASE.3" => 0.0822, // Base 3kVA
        "BASE.6" => 0.0828, // Base 6kVA
        "BASE.9" => 0.0848, // Base 9kVA
        "HP"     => 0.0935, // Heures Pleines
        "HC"     => 0.0578, // Heures Creuses
        "HPJB"   => 0.0530, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 0.0410, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 0.0835, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 0.0670, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 0.4052, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 0.1420, // Tempo - Rouge - Heures Creuses
        "HN"     => 0.0665, // EJP - Heures Normales
        "HPM"    => 0.4259  // EJP - Heures de Pointe Mobile
    );

    //01/08/2013
    $tarifs[mktime(0,0,0, 8, 1,2013)] = array(
        "TVA"    => 1.196,
        "BASE.3" => 0.0883, // Base 3kVA
        "BASE.6" => 0.0883, // Base 6kVA
        "BASE.9" => 0.0883, // Base 9kVA
        "HP"     => 0.0998, // Heures Pleines
        "HC"     => 0.0610, // Heures Creuses
        "HPJB"   => 0.0576, // Tempo - Bleu - Heures Pleines
        "HCJB"   => 0.0440, // Tempo - Bleu - Heures Creuses
        "HPJW"   => 0.0907, // Tempo - Blanc - Heures Pleines
        "HCJW"   => 0.0719, // Tempo - Blanc - Heures Creuses
        "HPJR"   => 0.4401, // Tempo - Rouge - Heures Pleines
        "HCJR"   => 0.1525, // Tempo - Rouge - Heures Creuses
        "HN"     => 0.0673, // EJP - Heures Normales
        "HPM"    => 0.4313  // EJP - Heures de Pointe Mobile
    );

    // Abonnements, TVA isolée
    //15/08/2009
    $abonnements[mktime(0,0,0, 8,15,2009)] = array(
        "TVA"      => 1.055,
        "BASE" => array(
            "3"  => 51.24,
            "6"  => 58.32,
            "9"  => 73.56,
            "12" => 127.68,
            "15" => 156.12,
            "18" => 184.56,
            "24" => 299.04,
            "30" => 413.52,
            "36" => 528.00
        ),
        "HC" => array(
            "6"  => 78.48,
            "9"  => 121.20,
            "12" => 177.12,
            "15" => 224.28,
            "18" => 271.44,
            "24" => 452.16,
            "30" => 632.88,
            "36" => 813.60
        ),
        "BBR" => array(
            "9"  => 85.80,
            "12" => 159.24,
            "15" => 159.24,
            "18" => 159.24,
            "24" => 338.16,
            "30" => 338.16,
            "36" => 423.84
        ),
        "EJP" => array(
            "9"  => 116.88,
            "12" => 116.88,
            "15" => 116.88,
            "18" => 116.88,
            "36" => 393.00
        )
    );

    //15/08/2010
    $abonnements[mktime(0,0,0, 8,15,2010)] = array(
        "TVA"      => 1.055,
        "BASE" => array(
            "3"  => 53.52,
            "6"  => 53.24,
            "9"  => 73.32,
            "12" => 113.88,
            "15" => 130.80,
            "18" => 178.20,
            "24" => 288.72,
            "30" => 399.24,
            "36" => 509.76
        ),
        "HC" => array(
            "6"  => 76.32,
            "9"  => 90.96,
            "12" => 150.96,
            "15" => 177.12,
            "18" => 201.12,
            "24" => 432.00,
            "30" => 532.32,
            "36" => 613.20
        ),
        "BBR" => array(
            "9"  => 88.44,
            "12" => 164.04,
            "15" => 164.04,
            "18" => 164.04,
            "24" => 348.36,
            "30" => 348.36,
            "36" => 436.56
        ),
        "EJP" => array(
            "9"  => 120.00,
            "12" => 120.00,
            "15" => 120.00,
            "18" => 120.00,
            "36" => 403.56
        )
    );

    //01/07/2011
    $abonnements[mktime(0,0,0, 7, 1,2011)] = array(
        "TVA"      => 1.055,
        "BASE" => array(
            "3"  => 51.18,
            "6"  => 64.32,
            "9"  => 71.52,
            "12" => 115.80,
            "15" => 133.08,
            "18" => 181.20,
            "24" => 293.64,
            "30" => 406.08,
            "36" => 518.40
        ),
        "HC" => array(
            "6"  => 77.64,
            "9"  => 92.52,
            "12" => 153.60,
            "15" => 180.12,
            "18" => 204.60,
            "24" => 439.44,
            "30" => 544.44,
            "36" => 623.64
        ),
        "BBR" => array(
            "9"  => 89.88,
            "12" => 166.80,
            "15" => 166.80,
            "18" => 166.80,
            "24" => 351.42,
            "30" => 351.42,
            "36" => 443.88
        ),
        "EJP" => array(
            "9"  => 122.04,
            "12" => 122.04,
            "15" => 122.04,
            "18" => 122.04,
            "36" => 410.16
        )
    );

    //23/07/2012
    $abonnements[mktime(0,0,0, 7,23,2012)] = array(
        "TVA"      => 1.055,
        "BASE" => array(
            "3"  => 55.56,
            "6"  => 65.64,
            "9"  => 76.08,
            "12" => 118.08,
            "15" => 135.72,
            "18" => 184.92,
            "24" => 299.52,
            "30" => 414.24,
            "36" => 528.84
        ),
        "HC" => array(
            "6"  => 79.20,
            "9"  => 94.44,
            "12" => 156.72,
            "15" => 183.72,
            "18" => 208.68,
            "24" => 448.32,
            "30" => 552.36,
            "36" => 636.24
        ),
        "BBR" => array(
            "9"  => 91.68,
            "12" => 170.16,
            "15" => 170.16,
            "18" => 170.16,
            "24" => 361.32,
            "30" => 361.32,
            "36" => 452.88
        ),
        "EJP" => array(
            "9"  => 124.56,
            "12" => 124.56,
            "15" => 124.56,
            "18" => 124.56,
            "36" => 418.44
        )
    );

    //01/08/2013
    $abonnements[mktime(0,0,0, 8, 1,2013)] = array(
        "TVA"      => 1.055,
        "BASE" => array(
            "3"  => 38.88,
            "6"  => 66.72,
            "9"  => 89.76,
            "12" => 135.00,
            "15" => 153.84,
            "18" => 176.76,
            "24" => 366.72,
            "30" => 453.96,
            "36" => 522.84
        ),
        "HC" => array(
            "6"  => 71.64,
            "9"  => 97.44,
            "12" => 156.12,
            "15" => 180.00,
            "18" => 201.24,
            "24" => 420.12,
            "30" => 492.36,
            "36" => 562.68
        ),
        "BBR" => array(
            "9"  => 96.60,
            "12" => 152.76,
            "15" => 176.04,
            "18" => 190.92,
            "24" => 471.84,
            "30" => 471.84,
            "36" => 583.56
        ),
        "EJP" => array(
            "9"  => 92.04,
            "12" => 137.16,
            "15" => 156.12,
            "18" => 173.64,
            "36" => 521.16
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
            $optarif => $abonnement["TVA"] * $abonnement[$optarif][$puissance]
        );
    }
    ksort($tab_abonnements);

    // Partie Consommation
    $tab_tarifs = array();
    foreach ($tarifs as $date => $tarif) {
        foreach ($periodes as $periode => $index) {
            $tab_tarifs[$date][$periode] = $tarif["TVA"] * $tarif[$index];
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