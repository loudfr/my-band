<?php
    $LOGOS_DIR = "./logos";
    $logo_img = $_GET["logo"];

    echo file_get_contents($LOGOS_DIR."/".$logo_img);
?>