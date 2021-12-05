<?php session_start();
define("DEBUG", FALSE);
header("Content-Type: application/json; charset=UTF-8", true);
require(__DIR__ . '/add_data.php');

//ADD AJAX Class
class add_ajax extends add_data {
    protected $reqOBJ;
    protected $rtnOBJ;
    
    function __construct() {
        parent::__construct(); //NOTE: Sets up database var in parent class
        $this->reqOBJ = json_decode( $_POST['req'], false);
        $this->rtnOBJ = new \stdClass();
    }
    public function run() {
        $this->build_rtnOBJ();
        if ( isset( $this->reqOBJ->request ) ) {
            switch ( $this->reqOBJ->request ) {
                case "ajax-city"    : $this->ajax_city();           break;
                case "ajax-loc"     : $this->ajax_loc();            break;
                case "ajax-get01"   : $this->ajax_finder_data();    break;
                case "ajax-get02"   : $this->ajax_analysis_data();  break;
                //TODO - MORE: case "ajax"
                    default: $this->error_response("Access Error: Requested Resource Unavailable.", 9999); break;
            }
        } else $this->error_response("Access Error: Bad Request Received.", 9997); 
    
        //Close DB connection and return encoded response
        $this->dbconn->close();
        echo json_encode( $this->rtnOBJ ); 
        exit;
    }
    //Locationing Functions
    private function ajax_city() {
        if ( isset( $this->reqOBJ->city ) ) {
            if ( !is_null( $this->reqOBJ->city ) || $this->reqOBJ->city = "" || $this->reqOBJ->city = " " ) {
                $query = $this->dbconn->prepare("SELECT * FROM `add_regions` WHERE `region_str` LIKE ? LIMIT 3");
                if ( $query !== FALSE ) {   
                    $param = "%" . $this->reqOBJ->city . "%" ;
                    $query->bind_param("s", $param );
                    $query->execute();
                    $result = $query->get_result();
                    $this->rtnOBJ->status = "success";
                    if ( $result->num_rows > 0 ) {
                        $rtnstr = "[";
                        while ( $row = $result->fetch_assoc() ) { $rtnstr .= "{\"cid\":" . $row['region_ID'] . ", \"cnm\": \"" . $row['region_str'] .  "\"},"; }
                        $this->rtnOBJ->suggestions = substr( $rtnstr, 0, -1 ) . "]";
                    } else { $this->rtnOBJ->suggestions = "none"; }                
                } else { $this->error_response("DB Error: Internal Error", 1002); }
            } else { $this->error_response("Input Error: Empty Parameter", 1001); }
        } else { $this->error_response("Input Error: Invalid Parameters", 1000); }
    }
    private function ajax_loc() {
        if ( isset( $this->reqOBJ->lat ) && isset( $this->reqOBJ->lon ) ) {
            if ( ( abs( $this->reqOBJ->lat ) <= 90 ) || ( abs( $this->reqOBJ->lon ) <= 180 ) ) {
                $query = $this->dbconn->prepare("SELECT `reg`.`region_ID`, `reg`.`region_str`, ( SQRT( POWER( ( `reg`.`region_lon` - ? ), 2 ) + POWER( ( `reg`.`region_lat` - ? ), 2 ) ) - ( ( `reg`.`region_rad` / 40000 ) * 360 ) ) AS `reg_dist` FROM `add_regions` AS `reg` WHERE `reg`.`region_lat` IS NOT NULL AND `reg`.`region_state` IS NOT NULL ORDER BY `reg_dist` ASC LIMIT 3 ;");
                if ( $query !== FALSE ) {
                    $query->bind_param("dd", $this->reqOBJ->lon, $this->reqOBJ->lat );
                    $query->execute();
                    $result = $query->get_result();
                    $this->rtnOBJ->status = "success";
                    if ( $result->num_rows > 0 ) {
                        $rtnstr = "[";
                        while ( $row = $result->fetch_assoc() ) { $rtnstr .= "{\"cid\":" . $row['region_ID'] . ", \"cnm\": \"" . $row['region_str'] .  "\"},"; }
                        $this->rtnOBJ->suggestions = substr( $rtnstr, 0, -1 ) . "]";
                    } else { $this->rtnOBJ->suggestions = "none"; }
               } else { $this->error_response("DB Error: Internal Error", 2002); }
                $_SESSION['add_gps'] = true;
                $_SESSION['add_lat'] = $this->reqOBJ->lat;
                $_SESSION['add_lon'] = $this->reqOBJ->lon;
            } else { $this->error_response("Input Error: Invalid Cooridnates", 2001); }
        } else { $this->error_response("Input Error: Invalid Parameters", 2000); }
    }
    
    //Avocado Finder and Analytic Data Functions
    private function ajax_finder_data() {
        if ( isset( $this->reqOBJ->cid ) ) {
            ////////////      VARIABLES     /////////////
            $cid = (int)$this->reqOBJ->cid; 
            //NOTE: Finder page AJAX now only required to send the cid variable
            //WARNING: DEPRICATED $type = $this->reqOBJ->type;
            //WARNING: DEPRICATED $year = (int)$this->reqOBJ->year;
            $year = 2015;
            $nationYoY = [];
            $nationMoM = [];
            $regionYoY = [];
            $regionMoM = [];
            $tmp = [];
            //////////// DATA FOR INFOCARD /////////////
            
            //Get Regional Max/Min Price for Org and Conn Avocados
            $region_stats = array( 
                $this->get_region_extrema( $cid, "con", $year, TRUE ),
                $this->get_region_extrema( $cid, "con", $year, FALSE ),
                $this->get_region_extrema( $cid, "org", $year, TRUE ),
                $this->get_region_extrema( $cid, "org", $year, FALSE ) 
            );
            foreach ( $region_stats as $i ) { if ( $region_stats[$i] === FALSE) { $this->error_response("Data Retrieval Error: ", 3000 + $i ); return; } }
            array_push( $region_stats, ( ( $region_stats[0] + $region_stats[1] ) / 2 ) );
            array_push( $region_stats, ( ( $region_stats[2] + $region_stats[3] ) / 2 ) );
            ////////////  DATA FOR CHARTS  /////////////
            
            //Get National YoY Average (CASE 0)
            for ( $y = 2015; $y <= 2018; $y++ ) {
                $yAvg = $this->get_averages( 0, $cid, $y, null, null );
                if ( $yAvg === FALSE ) { $this->error_response("Data Retrieval Error", 3003); return; }
                else { array_push( $nationYoY, $yAvg ); }
                array_push($tmp, $y);
                //Get National MoM Average (CASE 1)
                $tmparray = [];
                for ( $m = 1; $m <= 12; $m++ ) {
                    $mAvg = $this->get_averages( 1, $cid, $y, $m, null );
                    if ( $mAvg === FALSE ) { $this->error_response("Data Retrieval Error", 3004); return; }
                    else { array_push( $tmparray, $mAvg ); }
                    //TODO: Future Plan: National DoD Average (CASE 2)
                }
                array_push( $nationMoM, $tmparray);
            }
            
            //Get Regional YoY Average (CASE 3)
            for ( $y = 2015; $y <= 2018 ; $y++ ) {
                $yAvg = $this->get_averages( 3, $cid, $y, null, null );
                if ( $yAvg === FALSE ) { $this->error_response("Data Retrieval Error", 3005); return;}
                else { array_push( $regionYoY, $yAvg ); }
                
                //Get National MoM Average (CASE 4)
                $tmparray = [];
                for ( $m = 1; $m <= 12; $m++ ) {
                    $mAvg = $this->get_averages( 4, $cid, $y, $m, null );
                    if ( $mAvg === FALSE ) { $this->error_response("Data Retrieval Error", 3006); return;}
                    else { array_push( $tmparray, $mAvg ); }
                    //TODO: Future Plan: National DoD Average (CASE 5)
                }
                array_push( $regionMoM, $tmparray);
            }
            
            ////////////       OUTPUT      /////////////
                    
            $this->rtnOBJ->status = "success";
            $this->rtnOBJ->reg_stats = $region_stats;
            $this->rtnOBJ->reg_avg_YOY = $regionYoY;
            $this->rtnOBJ->reg_avg_MOM = $regionMoM;
            $this->rtnOBJ->nat_avg_YOY = $nationYoY;
            $this->rtnOBJ->nat_avg_MOM = $nationMoM;
            if ( DEBUG ) { $this->rtnOBJ->SQLLOG = parent::$sqllog; }
        } else { $this->error_response("Input Error: Invalid Parameters", 3000); return; }
    }
    private function ajax_analysis_data() {
        if ( isset( $this->reqOBJ->cid ) && isset( $this->reqOBJ->year ) && isset( $this->reqOBJ->type ) ) {
            //Variables
            $cid = (int)$this->reqOBJ->cid; $year;
            switch ( (int)$this->reqOBJ->year ) {
                case 1: $year = 2018; break;
                case 2: $year = 2017; break;
                case 3: $year = 2016; break;
                case 4: $year = 2015; break;
                    default: $year = 0; break;  }
            $vol_plu; $vol_bag; //Output Arrays                                                 
            
            //Temporary Arrays --> Read side commenting:                                        // -----------------------------------------------------------------------
            $array00 = []; $array01 = []; $array02 = []; $array03 = []; $array04 = [];          // NOTE: Due to issues with order integrity of the data being manipulated,
            $array10 = []; $array11 = []; $array12 = []; $array13 = []; $array14 = [];          // this was the best way that would ensure all data would be kept in the
            $totalCountPLUs = [0,0,0,0]; $totalCountBags = [0,0,0,0];                           // proper order
            //Primary Control Structure                                                         // -----------------------------------------------------------------------
            if ( $year == 0 ) {                                                                 // TODO: Future plan: use SQL to organize this into a temporary table,
                for ( $y = 2015; $y <= 2018 ; $y++ ) {                                          // export columns as arrays, then access add_data object using direct 
                    $plu = $this->get_volume_plu( $cid, $y, null, $this->reqOBJ->type );        // scope resolution to access and push them into the two output arrays.
                    array_push( $array00,   $plu['AverageSpent']    );                          // -----------------------------------------------------------------------                  
                    array_push( $array01,   $plu['TotalVolume']     );      $totalCountPLUs[0] += $plu['TotalVolume'];                       
                    array_push( $array02,   $plu['TotalPLU4046']    );      $totalCountPLUs[1] += $plu['TotalPLU4046'];                     
                    array_push( $array03,   $plu['TotalPLU4225']    );      $totalCountPLUs[2] += $plu['TotalPLU4225'];
                    array_push( $array04,   $plu['TotalPLU4770']    );      $totalCountPLUs[3] += $plu['TotalPLU4770'];
                                                                        
                    $bag = $this->get_volume_bag( $cid, $y, null, $this->reqOBJ->type );
                    array_push( $array10,   $bag['AverageSpent']    );  
                    array_push( $array11,   $bag['TotalBags']       );      $totalCountBags[0] += $bag['TotalBags'];
                    array_push( $array12,   $bag['TotalSmallBags']  );      $totalCountBags[1] += $bag['TotalSmallBags'];
                    array_push( $array13,   $bag['TotalLargeBags']  );      $totalCountBags[2] += $bag['TotalLargeBags'];
                    array_push( $array14,   $bag['TotalXLargeBags'] );      $totalCountBags[3] += $bag['TotalXLargeBags'];
                }
                $vol_plu = array( "AverageSpent"  => $array00, "TotalVolume"   => $array01, "TotalPLU4046"  => $array02, "TotalPLU4225"  => $array03, "TotalPLU4770"  => $array04 );
                $vol_bag = array( "AverageSpent"  => $array10, "TotalBags"     => $array11, "TotalSmallBags" => $array12, "TotalLargeBags" => $array13, "TotalXLargeBags"  => $array14 );
            } else { 
                for ( $m = 1; $m <= 12; $m++ ) {
                    $plu = $this->get_volume_plu( $cid, $year, $m, $this->reqOBJ->type );
                    array_push( $array00,   $plu['AverageSpent']    );
                    array_push( $array01,   $plu['TotalVolume']     );      $totalCountPLUs[0] += $plu['TotalVolume'];                       
                    array_push( $array02,   $plu['TotalPLU4046']    );      $totalCountPLUs[1] += $plu['TotalPLU4046'];                     
                    array_push( $array03,   $plu['TotalPLU4225']    );      $totalCountPLUs[2] += $plu['TotalPLU4225'];
                    array_push( $array04,   $plu['TotalPLU4770']    );      $totalCountPLUs[3] += $plu['TotalPLU4770'];
                    
                    $bag = $this->get_volume_bag( $cid, $year, $m, $this->reqOBJ->type );
                    array_push( $array10,   $bag['AverageSpent']    );  
                    array_push( $array11,   $bag['TotalBags']       );      $totalCountBags[0] += $bag['TotalBags'];
                    array_push( $array12,   $bag['TotalSmallBags']  );      $totalCountBags[1] += $bag['TotalSmallBags'];
                    array_push( $array13,   $bag['TotalLargeBags']  );      $totalCountBags[2] += $bag['TotalLargeBags'];
                    array_push( $array14,   $bag['TotalXLargeBags'] );      $totalCountBags[3] += $bag['TotalXLargeBags'];
                }
                $vol_plu = array( "AverageSpent"  => $array00, "TotalVolume"   => $array01, "TotalPLU4046"  => $array02, "TotalPLU4225"  => $array03, "TotalPLU4770"  => $array04 );
                $vol_bag = array( "AverageSpent"  => $array10, "TotalBags"     => $array11, "TotalSmallBags" => $array12, "TotalLargeBags" => $array13, "TotalXLargeBags"  => $array14 );
            }
            ///// RETURNS //////
            $this->rtnOBJ->volume_plus = $vol_plu;
            $this->rtnOBJ->volume_bags = $vol_bag;
            $this->rtnOBJ->volume_pie_plus = $totalCountPLUs;
            $this->rtnOBJ->volume_pie_bags = $totalCountBags;
            $this->rtnOBJ->status = "success";
            if ( DEBUG ) { $this->rtnOBJ->SQLLOG = parent::$sqllog; }
        } else { $this->error_response("Input Error: Invalid Parameters", 4000); return; }
    }
    
    //Return Functions
    private function build_rtnOBJ() {
        $this->rtnOBJ->status= "";
        $this->rtnOBJ->code = 0;
        $this->rtnOBJ->response = "";
    }
    private function error_response($response, $code) {
        $this->rtnOBJ->status = "error";
        $this->rtnOBJ->code = $code;
        $this->rtnOBJ->response = $response;
    }
}

//RUN AJAX Handler
try {
    $ADD = new add_ajax();
    $ADD->run();
} catch ( Exception $e ) { echo json_encode('{"status":"error","code":"9009","response":"' . $e . '"}'); }

?>
