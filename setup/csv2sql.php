<?php
ini_set('memory_limit', '8000M');
$countries = array();
$inserts_regions = ""; $inserts_data = "";
if ( ( $file = fopen("avocado.csv", "r") ) !== FALSE ) {
    //Parse CSV and prepare inserts for add_data
    while ( ( $data = fgetcsv($file, 1000, ",") ) !== FALSE ) {
        if ( array_search( $data[13], $countries ) === FALSE ) { array_push($countries, $data[13]); }
        $str = "INSERT INTO `add_data` (`data_index`,`data_date`,`data_averageprice`,`data_totalvolume`,`data_plu4046`,`data_plu4225`,`data_plu4770`,`data_totalbags`,`data_smallbags`,`data_largebags`,`data_xlargebags`,`data_type`,`data_year`,`data_region`) VALUES ('"; 
        for ($i = 0; $i < 13; $i++) { $str .= $data[$i] . "','"; }
        $str .= array_search( $data[13], $countries ) . "');";
        $inserts_data .= $str . "\n";
    }
    //Prepare inserts for add_regions
    for ($i = 0; $i < sizeof($countries); $i++) { $inserts_regions .= "INSERT INTO `add_regions` (`region_str`) VALUES ('" . $countries[$i] . "');\n"; }
    //Output Inserts
    //echo $inserts_regions;
    //echo "\n\n\n\n";
    echo $inserts_data;
} else { echo "ERROR: Could not open file."; }
?>
