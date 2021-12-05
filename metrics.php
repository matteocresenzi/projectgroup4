<?php session_start(); require(__DIR__ . '/dbcred.php');
    $dbconn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $dbconn->connect_error ) die( "Internal Error (database): " . $dbconn->connect_error );
    require(__DIR__ . '/add_data.php');
    
    $metrics = new add_data(); 
    
    //Arrays For US Total Volume
    $total_us_plu4046 = []; 
    $total_us_plu4225 = [];
    $total_us_plu4770 = [];
    $total_us_unc = [];
    
    //Arrays For Top Region Chart
    $total_r_strings = [];
    $total_r_plu4046 = [];
    $total_r_plu4225 = [];
    $total_r_plu4770 = [];
    $total_r_unc = [];
    
    //Arrays For Top Cities Charts
    $total_c_strings = [];
    $total_c_plu4046 = [];
    $total_c_plu4225 = [];
    $total_c_plu4770 = [];
    $total_c_unc = [];
    
    //Retrieve Data From Dataset
    for ($i = 2015; $i <= 2018; $i++ ) { 
    
        //Seciton 1: Sort Data For Top Regions
        $data_array = $metrics->get_top_volume( 0, $i );
        $tmp_labels = []; $tmp_4046 = []; $tmp_4225 = []; $tmp_4770 = []; $tmp_unc = [];
        for ($j = 0; $j <= 4; $j++) {
            array_push($tmp_labels, $data_array[$j][1]);
            array_push($tmp_4046, $data_array[$j][4]);
            array_push($tmp_4225, $data_array[$j][5]);
            array_push($tmp_4770, $data_array[$j][6]);
            array_push($tmp_unc, ($data_array[$j][3] - ( $data_array[$j][4] + $data_array[$j][5] + $data_array[$j][6] ) ));
        }
        array_push($total_r_strings, $tmp_labels);
        array_push($total_r_plu4046, $tmp_4046);
        array_push($total_r_plu4225, $tmp_4225);
        array_push($total_r_plu4770, $tmp_4770);
        array_push($total_r_unc, $tmp_unc);
        
        //Section 2: Sort Data For Top Cities
        $data_array = $metrics->get_top_volume( 1, $i );
        $tmp_labels = []; $tmp_4046 = []; $tmp_4225 = []; $tmp_4770 = []; $tmp_unc = [];
        for ($j = 0; $j <= 4; $j++) {
            array_push($tmp_labels, $data_array[$j][1]);
            array_push($tmp_4046, $data_array[$j][4]);
            array_push($tmp_4225, $data_array[$j][5]);
            array_push($tmp_4770, $data_array[$j][6]);
            array_push($tmp_unc, ($data_array[$j][3] - ( $data_array[$j][4] + $data_array[$j][5] + $data_array[$j][6] ) ));
        }
        array_push($total_c_strings, $tmp_labels);
        array_push($total_c_plu4046, $tmp_4046);
        array_push($total_c_plu4225, $tmp_4225);
        array_push($total_c_plu4770, $tmp_4770);
        array_push($total_c_unc, $tmp_unc);

        // Section 3: Us Totals
        $data_array = $metrics->get_top_volume( 2, $i );
        
        array_push( $total_us_plu4046, $data_array[0][4] );
        array_push( $total_us_plu4225, $data_array[0][5] );
        array_push( $total_us_plu4770, $data_array[0][6] );
        array_push( $total_us_unc, ($data_array[0][3] - ($data_array[0][4]+$data_array[0][5]+$data_array[0][6])));
    }
    
    //Retrieve ADD Internal Metrics Data
    $demographics   = $metrics->get_metrics_demographics();
    $demo_data      = []; for($i=0;$i<5;$i++) {array_push($demo_data, $demographics[$i][1]);}
    $interests      = $metrics->get_metrics_interests();
    $metr_labels    = []; for($i=0;$i<5;$i++) {array_push($metr_labels, $interests[$i][1]);}
    $metr_count     = []; for($i=0;$i<5;$i++) {array_push($metr_count, $interests[$i][2]);}    
?>
<!DOCTYPE html>
<html>
<head>
    <!--META-->
    <meta charset="UTF-8">
    <meta name="author" content="Darrenmond Chao, Matteo Cresenzi, Jason Fox, Seth Vance">
    <meta name="keywords" content="avocados">
    <meta name="description" content="Avocado price comparison app.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avoca-Do's and Don'ts</title>
    <!--EXT RESOURCES-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Mochiy+Pop+P+One&display=swap" rel="stylesheet"> 
    <script src="https://kit.fontawesome.com/c92e9a1909.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!--ADD ASSETS-->
    <link rel="stylesheet" href="assets/style/add_main.css">
    <script src="assets/scripts/general.js"></script>
    <script src="assets/scripts/metrics.js"></script>
    <style>
        .chartcontainer {background: #fff;}
        #yFilter {
            color: #fff;
            border: none;
            border-radius: 5px;
            width: 50%;
            max-width: 150px;
            display: block;
            margin: 0 auto;
            text-align: center;
        }
        .filt {
            color: #fff;
            font-style: italic;
            font-size: 16px;
            width: 100%;
            text-align: center;
        }
        #chartcontainer0{max-height: 200px;}
        #chart0{max-height: 200px;}
    </style>
</head>
<body>
<header class="cp1">
    <a href="./"><img src="assets/images/avocado.png"></a>
    <span class="title HOM">Avoca-</span>
    <span class="title">Do's and Don'ts</span>
    <span class="menu"><i id="menu" class="fas fa-bars"></i></span>
</header>
<div id="menu-bg" class="sitebody"></div>
<div id="menu-fg" class="sitebody"><hr>
    <div class="cp2"><a href="./">Avocado Finder</a></div><hr>
    <div class="cp2"><a href="./analysis.php">Avocado Analysis</a></div><hr>
    <div class="cp2"><a href="./metrics.php">Query Metrics</a></div><hr>
    <div class="cp2"><a href="./about.html">About</a></div><hr>
</div>
<div class="sitebody sitevp cp2">
    <div class="container">
        <div class="contbox cp1">
            <h1 class="title">Avocado Interest Metrics</h1>
            <hr>
            <div class="inner">
                <div>
                    <!--NOTE: User Metrics-->
                    <h2>Avoca-Do's and Don'ts Query Metrics:</h2>
                    <div class="chartcontainer" id="chartconatiner5"><canvas id="chart5"></canvas></div>
                    <p>The chart above shows the top five regions of interest in avacado pricing in the US based on Avaca-Do's And Don'ts page requests. These page requests are recorded specifically from the Avocado Price Finder page. This can provide helpful insight into where consumer avacado interests lie and where greater demand, or, at the very least a consumer populous contientious about avocado pricing exists.</p>
                    <h2>Avoca-Do's and Don'ts User Demographics</h2>
                    <div class="chartcontainer" id="chartconatiner0"><canvas id="chart0"></canvas></div>
                    <ol>
                    <?php for ($i=0;$i<=4;$i++) { echo "<li>" . $demographics[$i][0] . "</li>";} ?>
                    </ol>
                    <p>The chart above shows the demographic makeup of Avaca-Do's and Don'ts user's device user agents. Correlative trends may be derived from this information, such as Apple device users or users of Google Chrome having more of an interest in avocado prices than others.</p>
                    <!--NOTE: Volume Bar: US Total-->
                    <h2>US Avocado Volume:</h2>
                    <div class="chartcontainer" id="chartconatiner1"><canvas id="chart1"></canvas></div>
                    <p>The chart above shows avocado volume information for the entire United States over the entire time period covered within the dataset. This is further broken down by PLU information to show changes in availability/populatity in a particular type of hass avocado during this time period.</p>
                    <hr>
                    <div class="filt">Filter Top Cities and Regions By Year:</div>
                    <select id="yFilter" class="cp5">
                        <option val="2015" selected>2015</option>
                        <option val="2016">2016</option>
                        <option val="2017">2017</option>
                        <option val="2018">2018</option>
                    </select>
                    <hr>
                    <!--NOTE: Volume Bar: Regions-->
                    <h2>Top Regions:</h2>
                    <div class="chartcontainer" id="chartconatiner2"><canvas id="chart2"></canvas></div>
                    <p>The chart above shows the top five regions in the US in <span class="year">2015</span> for avocado sales by total volume, broken down by PLUs. Note that the ranking of these regions from highest to lowest avocado volume (left to right) is based on total volume, thus the breakdown of these regions by PLU may seem misleading due to a perceived greater interest or greater availability of certain PLUs over other PLUs within that region.</p>
                    <!--NOTE: Volume Bar: Cities-->
                    <h2>Top Cities:</h2>
                    <div class="chartcontainer" id="chartconatiner3"><canvas id="chart3"></canvas></div>
                    <p>The chart above shows the top five cities/metropolitan areas in the US in <span class="year">2015</span> for avocado sales by total volume, broken down by PLUs. Note that the ranking of these regions from highest to lowest avocado volume (left to right) is based on total volume, thus the breakdown of these regions by PLU may seem misleading due to a perceived greater interest or greater availability of certain PLUs over other PLUs within that city/metropolitan area.</p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <footer class="cp1"><span>Copyright 2021 ITSC3155 Group4</span></footer>
</div>
<script>
    //Data Setup
    <?php 
    echo "const tr0 = " . json_encode( $total_r_strings );
    echo "\n\tconst tr1 = " . json_encode( $total_r_plu4046 ) . ";";
    echo "\n\tconst tr2 = " . json_encode( $total_r_plu4225 ) . ";";
    echo "\n\tconst tr3 = " . json_encode( $total_r_plu4770 ) . ";";
    echo "\n\tconst tr4 = " . json_encode( $total_r_unc ) . ";\n";
    echo "\n\tconst tc0 = " . json_encode( $total_c_strings ) . ";";
    echo "\n\tconst tc1 = " . json_encode( $total_c_plu4046 ) . ";";
    echo "\n\tconst tc2 = " . json_encode( $total_c_plu4225 ) . ";";
    echo "\n\tconst tc3 = " . json_encode( $total_c_plu4770 ) . ";";
    echo "\n\tconst tc4 = " . json_encode( $total_c_unc ) . ";\n";
    echo "\n\tconst tu0 = " . json_encode( $total_us_plu4046 ) . ";";
    echo "\n\tconst tu1 = " . json_encode( $total_us_plu4225 ) . ";";
    echo "\n\tconst tu2 = " . json_encode( $total_us_plu4770 ) . ";";
    echo "\n\tconst tu3 = " . json_encode( $total_us_unc ) . ";\n";
    echo "\n\tconst me0 = " . json_encode( $metr_labels ) . ";";
    echo "\n\tconst me1 = " . json_encode( $metr_count ) . ";";
    echo "\n\tconst me2 = " . json_encode( $demo_data ) . ";";
    ?>
    
    let chart = new Charter( tr0, tr1, tr2, tr3, tr4, tc0, tc1, tc2, tc3, tc4, tu0, tu1, tu2, tu3, me0, me1, me2);
    chart.makeDemographicsChart();
    chart.makeMetricsChart();
    chart.makeUSChart();
    chart.makeTopCharts(2015);
    
    //Interactive
    $(document).ready(function(){
        $("#yFilter").change(function(){
            chart.makeTopCharts( $("#yFilter").val() );
            $(".year").text( $("#yFilter").val() );
        });
    
    });
</script>
</body>
</html>
