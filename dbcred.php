<?php
    $dev = TRUE;
    if ( $dev ) {
        define("DB_USER","add");
        define("DB_PASS","avocado");
        define("DB_HOST","localhost");
        define("DB_NAME","ADD_DB");
    } else {
        define("DB_USER","faraqvlh_add");
        define("DB_PASS","avocadosanddonts");
        define("DB_HOST","localhost");
        define("DB_NAME","faraqvlh_add");
    }
?>
