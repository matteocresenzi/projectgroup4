<?php session_start(); require(__DIR__ . '/dbcred.php');
    //General Setup
    $location;
    $dbconn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $dbconn->connect_error ) die( "Internal Error (database): " . $dbconn->connect_error );
    if ( !isset( $_GET['t'] ) ) { header('Location: ./?e=1'); exit; }
    
    //Setup Variables
    $target = (int)$_GET['t'];
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    $useraddr = $_SERVER['REMOTE_ADDR'];
    $lat = ""; $lon = "";
    if ( isset( $_SESSION['add_lat'] ) && isset( $_SESSION['add_lon'] ) ) { $lat = isset( $_SESSION['add_lat'] ); $lon = isset( $_SESSION['add_lon'] ); }
    
    //Retrieve Page Base Content
    $query = $dbconn->prepare("SELECT * FROM `add_regions` WHERE `region_ID` = ?");
    if ( $query === FALSE ) { header('Location: ./?e=3'); exit; }
    $query->bind_param("i", $target );
    $query->execute();
    $result = $query->get_result();
    if ( $result->num_rows == 1 ) {
        $location = $result->fetch_assoc();
    } else { header('Location: ./?e=2&r=' . $result->num_rows); exit; }
    
    //New Metric Entry
    $query = $dbconn->prepare("INSERT INTO `add_metrics` (`metric_useragent`,`metric_ipaddress`,`metric_reg`) VALUES (?,?,?)");
    $query->bind_param("ssi", $useragent, $useraddr, $target);
    if ($query->execute() === FALSE) { header('Location: ./?e=2&r=' . $dbconn->error); exit; }
    
    //Close DB Connection
    $dbconn->close();
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
    <link rel="stylesheet" href="assets/style/add_main.css" type="text/css">
    <link rel="stylesheet" href="assets/style/add_style4finder.css" type="text/css">
    <script src="assets/scripts/general.js"></script>
    <script src="assets/scripts/finder.js"></script>
    <style>
        #transition {
            text-align: center;
            position: relative;
            margin: 0 auto;
            width: 100%;
        }
    </style>
</head>
<body>
<header class="cp1">
    <a href="./"><a href="./"><img src="assets/images/avocado.png"></a></a>
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
            <h1 class="title">Avocado Price Finder</h1>
            <?php echo '<div id="transition"><a href="analysis.php?t=' . $location['region_ID'] . '"><i class="fa fas fa-exchange-alt"> View Volume Data</i></a></div>'; ?><hr>
            <div class="inner">
                <!--<iframe style="height:100%;width:100%;border:0;" frameborder="0" src="https://www.google.com/maps/embed/v1/place?q=Eiffel+Tower&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&maptype=satellite"></iframe>-->
                <div id="chartcontainer"><canvas id="chartviewport"></canvas></div>
                <!--<div style="background:#fff;"><canvas id="momYChart" style="height:100%;width:100%;"></canvas></div>-->
                <!--<div style="background:#fff;"><canvas id="momAChart" style="height:100%;width:100%;"></canvas></div>-->
                
                <div class="toolbar">
                    <select name="cparam1" id="cparam1" class="sel" autocomplete="off">
                        <option value="0" default>Year-Over Year</option>
                        <option value="1">Month-Over-Month (ALL)</option>
                        <option value="2">Month-Over-Month (By Year)</option>
                    </select>
                    <select name="cparam2" id="cparam2" class="sel" autocomplete="off">
                        <option value="0" default>2015</option>
                        <option value="1">2016</option>
                        <option value="2">2017</option>
                        <option value="3">2018 (limited)</option>
                    </select>
                </div>
            </div>
            <div class="inner">
                <div class="subtitle"><span id="city"><?php echo $location['region_str'];?></span></div>
                <div class="stats">
                    <p id="chartdescription"></p>
                    <h4>Quick Statistics:</h4>
                    <h6>For Conventional Avocados:</h6>
                    <ul>
                        <li>Highest Average Price: <strong id="stat_1"></strong></li>
                        <li>Lowest Average Price: <strong id="stat_2"></strong></li>
                    </ul>
                    <h6>For Organic Avocados:</h6>
                    <ul>
                        <li>Highest Average Price: <strong id="stat_3"></strong></li>
                        <li>Lowest Average Price: <strong id="stat_4"></strong></li>
                    </ul>
                    <p>In the <?php echo $location['region_str']; ?> region organic avocados cost on average <strong id="ovsp"></strong>% <strong id="ovst"></strong> than conventional avocados.</p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <footer class="cp1"><span>Copyright 2021 ITSC3155 Group4</span></footer>
</div>
<script>
let mainchart = new Charter();
dataReq();
function dataReq() {
    let txt = { request:"ajax-get01", cid:<?php echo $location['region_ID'];?>};
    let json = "req=" + JSON.stringify( txt );
    console.log("add: ajax - requesting \"" + json + "\"");
    $.post("ajax_handler.php", json, function(response) {
        console.log(response);
        if (response.status === "success" ) {
            console.log("add: success(" + response.code + "): " + response.response );
            mainchart.setupCharts( $("#city").text(), response.nat_avg_YOY, response.reg_avg_YOY, response.nat_avg_MOM, response.reg_avg_MOM);
            updatechartview(0); //mainchart.updateChartView(0);
            setPageStats( response.reg_stats );
        } else { console.log("add: error(" + response.code +") - " + response.response); } 
    });
}
function updatechartview( input ) {
    var param1 = $("#cparam1").val();
    var param2 = $("#cparam2").val();
    var ctrl = param1;
    if ( input != null ) { ctrl = input; }
    if ( param1 == 2 ) { ctrl = (Number(param1)) + (Number(param2)); }
    mainchart.updateChartView( ctrl );

    //Update Description
    var yearstr = "";
    var year = $("#cparam2").val();
    if ( year == 0 ) { yearstr = "2015"; } else if ( year == 1) { yearstr = "2016"; } else if ( year == 2) { yearstr = "2017"; } else { yearstr = "2018"; }
    if ( ctrl == 0 ) { $("#chartdescription").text("The above chart compares the change of average prices of avocados in the <?php echo $location['region_str'];?> region by year. This graph does not exlcude information based the type or size of avocados in the dataset.");
    } else if ( ctrl == 1) { $("#chartdescription").text("The above chart shows the change in the price of the average avocado in the <?php echo $location['region_str'];?> region, compared to national averages. This graph does not exlcude information based the type or size of avocados in the dataset.");
    } else if ( ctrl > 1) { $("#chartdescription").text("The above chart shows changes in the average price of avocados in the <?php echo $location['region_str'];?> region compared to the nation in the year " + yearstr + ".");
    } else { $("#chartdescription").hide(); }
}
function setPageStats( data ) {
    for( var i = 0; i < 4; i++) { $("#stat_" + ( i + 1 ) ).text( "$" + data[i].toFixed(2) ); }
    var ovsp = ( ( data[4] / data[5] ) * 100 ).toFixed(2);
    var ovst = "more"; if ( ovsp < 0) { ovst = "less"; }
    $("#ovsp").text( Math.abs( ovsp ) );
    $("#ovst").text( ovst );
}
//Chart Interactivity
$(document).ready(function(){
    $("#cparam1").change(function(){
        if ( $(this).val() == 2 ) { $("#cparam2").fadeIn(); } else { $("#cparam2").fadeOut(); }
        updatechartview(null);
    });
    $("#cparam2").change(function(){
        updatechartview(null);
    });
});
</script>
</body>
</html>
