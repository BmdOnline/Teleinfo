<?php
    setlocale(LC_ALL , "fr_FR" );
    date_default_timezone_set("Europe/Paris");
    error_reporting(E_ERROR);

    include_once("config.php");
    require "vendor/Mobile_Detect.php";
    require "vendor/Rain/autoload.php";
    use Rain\Tpl;

    // Detect if Mobile or Computer is used
    $detect = new Mobile_Detect;
    $mobile = (($detect->isMobile()) || (isset($_GET['mobile']) && ($_GET['mobile']==1)));

    // Use template ?
    if ($config["useTemplate"]) {
        // Templace RainTPL
        if (!is_dir('cache')) { mkdir('cache',0705); chmod('cache',0705); }

        Tpl::configure( array(
            "tpl_dir"       => array(
                $config["template"]["tpl_dir"],
                $config["template"]["tpl_dir"]), // !!! due to a bug, need to repeat twice !!!
            "cache_dir"     => "cache/",
            "debug"         => false, // set to false to improve the speed
        ) );

        // Add PathReplace plugin (necessary to load the CSS with path replace)
        Tpl::registerPlugin( new Tpl\Plugin\PathReplace() );

        // create the Tpl object
        $tpl = new Tpl;

        header("Vary: User-Agent");
        if ($mobile) {
            $tpl->draw($config["template"]["mobile"]);
        } else {
            $tpl->draw($config["template"]["desktop"]);
        }
    } else {
        header("Vary: User-Agent");
        if ($mobile) {
            //readfile("tpl/teleinfo.tabs.mobile.html");
            readfile($config["notemplate"]["mobile"]); // FIXME: Security hole
        } else {
            //readfile("tpl/teleinfo.tabs.html");
            readfile($config["notemplate"]["desktop"]); // FIXME: Security hole
        }
    }
?>
