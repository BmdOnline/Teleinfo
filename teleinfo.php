<?php
    setlocale(LC_ALL , "fr_FR" );
    date_default_timezone_set("Europe/Paris");
    error_reporting(E_ERROR);

    include_once("config.php");
    require "lib/Mobile_Detect.php";
    require "lib/Rain/autoload.php";
    use Rain\Tpl;

    // Detect if Mobile or Computer is used
    $detect = new Mobile_Detect;
    $mobile = (($detect->isMobile()) || (isset($_GET['mobile']) && ($_GET['mobile']==1)));

    // Use template ?
    if ($config["usetemplate"]) {
        // Templace RainTPL
        if (!is_dir('cache')) { mkdir('cache',0705); chmod('cache',0705); }

        Tpl::configure( array(
            "tpl_dir"       => array("tpl/files/", "tpl/files/"), // !!! due to a bug, need to repeat twice !!!
            "cache_dir"     => "cache/",
            "debug"         => false, // set to false to improve the speed
        ) );

        // Add PathReplace plugin (necessary to load the CSS with path replace)
        Tpl::registerPlugin( new Tpl\Plugin\PathReplace() );

        // create the Tpl object
        $tpl = new Tpl;

        header("Vary: User-Agent");
        if ($mobile) {
            $tpl->draw("teleinfo.mobile");
        } else {
            $tpl->draw("teleinfo");
        }
    } else {
        header("Vary: User-Agent");
        if ($mobile) {
            readfile("tpl/teleinfo.tabs.mobile.html");
        } else {
            readfile("tpl/teleinfo.tabs.html");
        }
    }
?>
