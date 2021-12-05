<?php
    for ($i = 2015; $i <= 2018; $i++) {
        for ($j = 1; $j <= 54; $j++) {
            echo "INSERT INTO `add_topRegions`(`tr_regionID`,`tr_totalvolume`,`tr_total4046`,`tr_total4225`,`tr_total4770`,`tr_year`)\n\tSELECT '" . $j . "', SUM(`data_totalvolume`), SUM(`data_plu4046`), SUM(`data_plu4225`), SUM(`data_plu4770`), '" . $i . "' FROM `add_data` WHERE `data_year` = '" . $i . "' AND `data_region` = '" . $j . "';\n";

        }
    }
?>
