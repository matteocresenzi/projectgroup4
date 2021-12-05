<?php session_start(); require(__DIR__ . '/dbcred.php');
    $dbconn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $dbconn->connect_error ) die( "Internal Error (database): " . $dbconn->connect_error );
    $default    = 8;
    $useragent  = $_SERVER['HTTP_USER_AGENT'];
    $useraddr   = $_SERVER['REMOTE_ADDR'];
    $sql        = "SELECT `region_ID`,`region_str` FROM `add_regions`";
    $locations  = $dbconn->query( $sql );
    if ( $locations < 1 ) { header('Location: ./?e=2&r=' . $result->num_rows); exit; }
    if ( isset( $_GET['t'] ) ) {
        $sql = "INSERT INTO `add_metrics` (`metric_useragent`,`metric_ipaddress`,`metric_reg`) VALUES ('" . $useragent . "','" . $useraddr . "','" . $default . "');";
        if ($dbconn->query($sql) !== TRUE) { header('Location: ./?e=2&r=' . $conn->error); exit; }
        $default = (int)$_GET['t'];     //SECURITY: unlike finder.php, this shouldn't open the possibility of an injection attack
    }
    
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
    <link rel="stylesheet" href="assets/style/add_main.css">
    <link rel="stylesheet" href="assets/style/add_analysis.css">
    <script src="assets/scripts/general.js"></script>
    <script src="assets/scripts/analysis.js"></script>
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
            <h1 class="title"><span class="HOM">Avocado</span> Volume Analysis</h1>
            <div id="transition"><a href=""><i class="fa fas fa-exchange-alt"> View Pricing Data</i></a></div>
            <hr>
            <div class="toolbar">
                <div class="s1"><select class="selection" id="cparam1">
                    <?php   while ( $row = $locations->fetch_assoc() ) {
                                $def = ""; if ( $row['region_ID'] == $default ) { $def = " selected";}
                                echo "<option value=\"" . $row['region_ID'] . "\"" . $def . ">" . $row['region_str'] . "</option>";
                            } ?>
                </select></div>
                <div class="s1 middle">
                    <div class="s2 sl"><select class="selection" id="cparam2">
                        <option value="0">All</option>
                        <option value="1">2018 (limited)</option>
                        <option value="2">2017</option>
                        <option value="3">2016</option>
                        <option value="4">2015</option>
                    </select></div>
                    <div class="s2 sr"><select class="selection" id="cparam3">
                        <option value="all">All Types</option>
                        <option value="con">Conventional</option>
                        <option value="org">Organic</option>
                    </select></div>
                </div>
            </div>
            <br><br><hr>
            <div class="inner">
                <h4>Avocado Sales-Volume Information: </h4><i class="far fa-question-circle" id="pluInfo"></i>
                <div class="chartcontainer" id="cc0"><canvas id="chart0"></canvas></div>
                <hr><h4>% Make Up of Volume:</h4>
                <div class="block">
                    <div class="chartcontainer halfchart fl" id="cc1"><canvas id="chart1"></canvas></div>
                    <div class="chartcontainer halfchart fr" id="cc2"><canvas id="chart2"></canvas></div>
                </div>
                <hr>
                <p id="description"><span class="intro"></span> the <span class="region"></span> region purchased a total of <strong class="totalvolume"></strong> avocados at an average price of $<strong class="averageprice"></strong> per avocado. Regarding these averages, it can be approximated that the <span class="region"></span> region spent a total of $<strong class="totalcost"></strong> on avocados in this period of time.</p>
            </div>
        </div>
    </div>
    <br>
    <footer class="cp1"><span>Copyright 2021 ITSC3155 Group4</span></footer>
</div>
<div id="infooverlay" class="sitebody"></div>
<div id="infobox" class="sitebody">
    <div class="in cp1">
        <h4>Info on Avocado PLUs:</h4>
        <strong>Avocado PLU #4046</strong><br>
        <div class="imgs">
            <img src="assets/images/4046california.jpg" style="float:left;">
            <img src="assets/images/4046mexico.jpg" style="float:right;">
        </div>
        <ul>
            <li>Small/Medium Hass</li>
            <li>Typicall 3-5oz</li>
        </ul>
        <hr>
        <strong>Avocado PLU #4225</strong><br>
        <div class="imgs">
            <img src="assets/images/4225california.jpg" style="float: left;">
            <img src="assets/images/4225mexico.jpg">
        </div>
        <ul>
            <li>Large Hass</li>
            <li>Typically 8-10oz</li>
        </ul>
        <hr>
        <strong>Avocado PLU #4770</strong><br>
        <div class="imgs">
            <img src="assets/images/4770california.jpg" style="float: left;">
            <img src="assets/images/4770mexico.jpg">
        </div>
        <ul>
            <li>Extra Large Hass</li>
            <li>Typically 10-15oz</li>
        </ul>
        <hr>
        <a href="https://loveonetoday.com/how-to/identify-hass-avocados/">Click here for more information on the different types of Hass Avocados.</a>
    </div>
</div>
<script>
dataReq();
function dataReq() {
    let txt = { request:"ajax-get02", cid:(Number($("#cparam1").val())), year:(Number($("#cparam2").val())), type:$("#cparam3").val()};
    let json = "req=" + JSON.stringify( txt );
    console.log("add: ajax - requesting \"" + json + "\"");
    $.post("ajax_handler.php", json, function(response) {
        console.log(response);
        if (response.status === "success" ) {
            var chartography = new Charter();
            chartography.updateCharts( response.volume_plus, response.volume_bags, response.volume_pie_plus, response.volume_pie_bags, (Number($("#cparam2").val())) );  
            var chartography = null;
            updateText(response.volume_plus, response.volume_pie_plus);
        } else { console.log("add: error(" + response.code +") - " + response.response); } 
    });
}

//jQuery Interactivity
$(document).ready( function(){ 
    $(".selection").change( function(){ dataReq(); }); 
    $("#pluInfo").click(function(){ $("#infooverlay").fadeIn(); $("#infobox").fadeIn(); });
    $("#infooverlay").click(function(){ $("#infooverlay").fadeOut(); $("#infobox").fadeOut(); });
    $("#infobox").click(function(){ $("#infooverlay").fadeOut(); $("#infobox").fadeOut(); });
});
//Update Description
function updateText(volume, totals) {
    var intro_ctrl = (Number( $("#cparam2").val() )); var intro = ""; var avgPrice = 0.00; var totalspent = 0;
    if ( intro_ctrl == 0 ) { intro = "From January 2015 through March 2018, ";
    } else { intro = "In " + $("#cparam2 option:selected").text() + ", "; }
    for (var i = 0; i < volume['AverageSpent'].length; i++ ) { avgPrice += volume['AverageSpent'][0]; }
    avgPrice /= volume['AverageSpent'].length;
    totalspent = totals[0] * avgPrice;
    
    //Update Paragraph
    $("#description .intro").text( intro );
    $("#description .region").text( $("#cparam1 option:selected").text() );
    $("#description .averageprice").text( avgPrice.toFixed(5) );
    $("#description .totalcost").text( totalspent.toLocaleString('en-US') );
    $("#description .totalvolume").text( totals[0].toLocaleString('en-US') ); 
    $("#transition a").attr("href", "finder.php?t=" + $("#cparam1").val() );
}
</script>
</body>
</html>
