<?php
class add_data {
    protected $dbconn;
    protected static $sqllog = "";
    function __construct() { 
        require(__DIR__ . '/dbcred.php');
        $this->dbconn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $this->dbconn->connect_error ) die( json_encode('{"status":"error","code":"9000","response": db_err:"' . $this->dbconn->connect_error . '"}') );
    }
    //Get Low Prices From DB
    protected function get_region_extrema($region,$type,$year,$high) {
        $sql = "SELECT `data_averageprice` FROM `add_data` WHERE `data_region` = ? AND `data_year` > ?";
        
        switch ( $type ) {  case "org": $sql .= " AND `data_type` = 'organic'";      break;
                            case "con": $sql .= " AND `data_type` = 'conventional'"; break;
                                default: $sql .= "";                                 break; }
        
        $sql .= " ORDER BY `data_averageprice`";
        if ( $high ) { $sql .= " DESC"; } else { $sql .= " ASC"; }
        $sql .= " LIMIT 1";
        $query = $this->dbconn->prepare($sql);
        if ( $query !== FALSE ) {
            $query->bind_param( "ii", $region, $year );
            $query->execute();
            $result = $query->get_result();
            if ( $result->num_rows > 0 ) {
                $row = $result->fetch_assoc();
                return $row['data_averageprice'];
            } else { return false; }
        } else { die( json_encode('{"status":"error","code":"9001","response": db_err(' . $sql . ')}') ); }
    }
    //Get Average of Averages From DB
    protected function get_averages($ctrl, $region, $in_y, $in_m, $in_d) {
        $dateparam = "";   $month = ""; $day = ""; //NOTE Additional Variables needed for wildcarding in SQL query binding
        $sql = "SELECT AVG(`data_averageprice`) AS PriceAverage FROM `add_data`";
        //Convert date to string and compensate for < 10 numbers
        $year = (string)$in_y;
        if ( $in_m < 10 ) { $month = "0" . $in_m; } else { $month = $in_m; }
        if ( $in_d < 10 ) { $day = "0" . $in_d; } else { $day = $in_d; }
        //Main Control Structure
        switch ( $ctrl ) {
            case 0: $sql .= " WHERE `data_date` LIKE ?"; 
                    $dateparam = $year . "-%"; 
                    break; // ^ Get National Year Average ^
            case 1: $sql .= " WHERE `data_date` LIKE ?"; 
                    $dateparam = $year . "-" . $month . "-__"; 
                    break; // ^ Get National Month Average ^
            case 2: $sql .= " WHERE `data_date` LIKE ?"; 
                    $dateparam = $year . "-" . $month . "-" . $day;
                    break; // ^ Get National Day Average ^
            case 3: $sql .= " WHERE `data_region` = ? AND `data_date` LIKE ?"; 
                    $dateparam = $year . "-%"; 
                    break; // ^ Get Regional Year Average ^
            case 4: $sql .= " WHERE `data_region` = ? AND `data_date` LIKE ?"; 
                    $dateparam = $year . "-" . $month . "-__"; 
                    break; // ^ Get Regional Month Average ^
            case 5: $sql .= " WHERE `data_region` = ? AND `data_date` LIKE ?"; 
                    $dateparam = $year . "-" . $month . "-" . $day;
                    break; // ^ Get National Day Average ^
                default: break; // <- Get Average Of ALL Data
        }
        if ( ( $ctrl <= 2 ) && ( $ctrl >= 0 ) ) {
            $query = $this->dbconn->prepare($sql);
            static::$sqllog .= " [sql(" . $sql . "), dateparam(" . $dateparam . "), region(" . $region . ")];";  //TESTING
            if ( $query !== FALSE ) {
                $query->bind_param( "s", $dateparam ); 
                $query->execute();
                $result = $query->get_result();
                if ( $result->num_rows > 0 ) {
                    $row = $result->fetch_assoc();
                    return $row['PriceAverage'];
                } else { die( json_encode('{"status":"error","code":"9112","response(' . $ctrl . ')": db_err(' . $sql . ')}') ); }
            } else { die( json_encode('{"status":"error","code":"9012","response": db_err(' . $sql . ')}') ); }
        } else if ( ( $ctrl <= 5 ) && ( $ctrl >= 3 ) ) {
            $query = $this->dbconn->prepare($sql); 
            static::$sqllog .= " " . "[sql(" . $sql . "), dateparam(" . $dateparam . "), region(" . $region . ")];";  //TESTING
            if ( $query !== FALSE ) {
                $query->bind_param( "ss", $region, $dateparam );
                $query->execute();
                $result = $query->get_result();
                if ( $result->num_rows > 0 ) {
                    $row = $result->fetch_assoc();
                    return $row['PriceAverage'];
                } else { die( json_encode('{"status":"error","code":"9111","response(' . $ctrl . ')": db_err(' . $sql . ')}') ); }
            } else { die( json_encode('{"status":"error","code":"9011","response": db_err(' . $sql . ')}') ); }
        } else {
            $query = $this->dbconn->prepare($sql); 
            static::$sqllog .= " " . "[sql(" . $sql . "), dateparam(" . $dateparam . "), region(" . $region . ")];";  //TESTING
            if ( $query !== FALSE ) {
                $query->execute();
                $result = $query->get_result();
                if ( $result->num_rows > 0 ) {
                    $row = $result->fetch_assoc();
                    return $row['PriceAverage'];
                } else { die( json_encode('{"status":"error","code":"9110","response(' . $ctrl . ')": db_err(' . $sql . ')}') ); }
            } else { die( json_encode('{"status":"error","code":"9010","response": db_err(' . $sql . ')}') ); }
        }
    }
    //Get Volume by PLUs From DB
    protected function get_volume_plu($region, $in_y, $in_m, $type) {
        $dateparam = (string)$in_y . "-";
        if ( is_null( $in_m ) ) { $dateparam .= "%"; } else if ( $in_m < 10 ) { $dateparam .= "0" . $in_m . "-%"; } else { $dateparam .= $in_m . "-%"; }
        $sql = "SELECT AVG(`data_averageprice`) AS AverageSpent, ROUND( SUM(`data_totalvolume`), 0) AS TotalVolume, ROUND( SUM(`data_plu4046`), 0) AS TotalPLU4046, ROUND( SUM(`data_plu4225`), 0) AS TotalPLU4225, ROUND( SUM(`data_plu4770`), 0) AS TotalPLU4770 FROM `add_data` WHERE `data_region`=? AND `data_date` LIKE ?";
        switch ( $type ) {
            case "org": $sql .= " AND `data_type` = 'conventional'"; break;
            case "con": $sql .= " AND `data_type` = 'organic'"; break;
                default: break;
        }
        $query = $this->dbconn->prepare($sql);
        static::$sqllog .= " [sql(" . $sql . "), dateparam(" . $dateparam . "), region(" . $region . ")];";  //TESTING
        if ( $query !== FALSE ) {
                $query->bind_param( "is", $region, $dateparam ); 
                $query->execute();
                $result = $query->get_result();
                if ( $result->num_rows > 0 ) {
                    $row = $result->fetch_assoc();
                    return $row;
                } else { die( json_encode('{"status":"error","code":"9120","response(' . $ctrl . ')": db_err(' . $sql . ')}') ); }
        } else { die( json_encode('{"status":"error","code":"9020","response": db_err(' . $sql . ')}') ); }
    }
    //Get Volume by Bag From DB
    protected function get_volume_bag($region, $in_y, $in_m, $type) {
        $dateparam = (string)$in_y . "-";
        if ( is_null( $in_m ) ) { $dateparam .= "%"; } else if ( $in_m < 10 ) { $dateparam .= "0" . $in_m . "-%"; } else { $dateparam .= $in_m . "-%"; }
        $sql = "SELECT AVG(`data_averageprice`) AS AverageSpent, ROUND( SUM(`data_totalbags`), 0) AS TotalBags, ROUND( SUM(`data_smallbags`), 0) AS TotalSmallBags, ROUND( SUM(`data_largebags`), 0) AS TotalLargeBags, ROUND( SUM(`data_xlargebags`), 0) AS TotalXLargeBags FROM `add_data` WHERE `data_region`=? AND `data_date` LIKE ?";
        switch ( $type ) {
            case "org": $sql .= " AND `data_type` = 'conventional'"; break;
            case "con": $sql .= " AND `data_type` = 'organic'"; break;
                default: break;
        }
        $query = $this->dbconn->prepare($sql);
        static::$sqllog .= " [sql(" . $sql . "), dateparam(" . $dateparam . "), region(" . $region . ")];";  //TESTING
        if ( $query !== FALSE ) {
                $query->bind_param( "is", $region, $dateparam ); 
                $query->execute();
                $result = $query->get_result();
                if ( $result->num_rows > 0 ) {
                    $row = $result->fetch_assoc();
                    return $row;
                } else { die( json_encode('{"status":"error","code":"9120","response(' . $ctrl . ')": db_err(' . $sql . ')}') ); }
        } else { die( json_encode('{"status":"error","code":"9020","response": db_err(' . $sql . ')}') ); }
    }
    
    //////////////// METRICS PAGE - SECURITY: PUBLICLY ACCESSED FUNCTIONS ///////////////////
    
    public function get_metrics_demographics() {
        $sql = "SELECT `metric_useragent`, COUNT(`metric_useragent`) AS `occurences` FROM `add_metrics` GROUP BY `metric_useragent` ORDER BY `occurences` DESC LIMIT 5";
        $result = $this->dbconn->query( $sql );
        if ( $result !== FALSE ){
            if ( $result->num_rows > 0 ) {
                $return = []; 
                while ( $row = $result->fetch_assoc() ) {
                    $tmp = array($row['metric_useragent'], $row['occurences']);
                    array_push($return, $tmp);
                }
                return $return;
            } else { die("DEBUG INFO: SQL ERROR"); }
        } else { die("DEBUG INFO: SQL ERROR"); }
    }
    public function get_metrics_interests() {
        $sql = "SELECT `met`.`metric_reg`,`reg`.`region_str`, COUNT(`met`.`metric_reg`) AS `occurences` FROM `add_metrics` AS `met` INNER JOIN `add_regions` AS `reg` ON `reg`.`region_ID` = `met`.`metric_reg` GROUP BY `met`.`metric_reg` ORDER BY `occurences` DESC LIMIT 5";
        $result = $this->dbconn->query( $sql );
        if ( $result !== FALSE ){
            if ( $result->num_rows > 0 ) {
                $return = []; 
                while ( $row = $result->fetch_assoc() ) {
                    $tmp = array($row['metric_reg'], $row['region_str'], $row['occurences']);
                    array_push($return, $tmp);
                }
                return $return;
            } else { die("DEBUG INFO: SQL ERROR"); }
        } else { die("DEBUG INFO: SQL ERROR"); }
    }
    public function get_top_volume($regions,$y) {
        $rtn = []; $filter= " AND `tr_regionID` != 52";
        switch( $regions ) { 
            case 0: $filter .= " AND `region_state` IS NULL"; break;
            case 1: $filter .= " AND `region_state` IS NOT NULL"; break;
            case 2: $filter = " AND `tr_regionID` = 52"; break; }
        $sql = "SELECT * FROM `add_topRegions` INNER JOIN `add_regions` ON `add_regions`.`region_ID` = `add_topRegions`.`tr_regionID` WHERE `tr_year` = " . $y . $filter . " ORDER BY `tr_totalvolume` DESC LIMIT 5;";
        $result = $this->dbconn->query( $sql );
        if ( $insert !== FALSE ) {
            if ( $result->num_rows > 0 ) {
                while ( $row = $result->fetch_assoc() ) {
                    $tmp = array( $row['tr_regionID'], $row['region_str'],  $row['tr_year'], $row['tr_totalvolume'], $row['tr_total4046'], $row['tr_total4225'], $row['tr_total4770'] );
                    array_push($rtn, $tmp);
                }
            } else return FALSE;
        } else return FALSE;
        return $rtn;
    }
}
?>
