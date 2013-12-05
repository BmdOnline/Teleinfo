<?php
    require "lib/Mobile_Detect.php";
    require "lib/Rain/autoload.php";

    setlocale(LC_ALL , "fr_FR" );
    date_default_timezone_set("Europe/Paris");

    // Templace RainTPL
    if (!is_dir('cache')) { mkdir('cache',0705); chmod('cache',0705); }
    use Rain\Tpl;
    Tpl::configure( array(
        "tpl_dir"       => array("tpl/", "tpl/"), // !!! due to a bug, need to repeat twice !!!
        "cache_dir"     => "cache/",
        "debug"         => false, // set to false to improve the speed
    ) );

    // Add PathReplace plugin (necessary to load the CSS with path replace)
    Tpl::registerPlugin( new Tpl\Plugin\PathReplace() );

    // create the Tpl object
    $tpl = new Tpl;


    // Detect if Mobile or Computer is used
    $detect = new Mobile_Detect;
    $mobile = (($detect->isMobile()) || (isset($_GET['mobile']) && ($_GET['mobile']==1)));

    header("Vary: User-Agent");
    if ($mobile) {
        $tpl->draw("teleinfo.mobile");
    } else {
        $tpl->draw("teleinfo");
    }
?>
