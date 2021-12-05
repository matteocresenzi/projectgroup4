<?php session_start();
    //General Setup
    
    
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
    <!--ADD ASSETS-->
    <link rel="stylesheet" href="assets/style/add_main.css" type="text/css">
    <script src="assets/scripts/general.js"></script>
    <style>
        .location {
            border-radius: 25px;
            font-family: 'Mochiy Pop P One', sans-serif;
            text-align: center;
            font-size: 36px;
            margin: 0 auto;
            color: #fff;
            width: 90%;
            transition: background 400ms;
            padding: 10px;
            overflow: hidden;
        }
        .location:hover { background: #cda989; }
        .tools {
            font-size: 18px;
            margin: 0 auto;
            width: 45%;
        }
        @media only screen and (max-width: 420px) {
            .location {font-size:24px;}
            .tools {font-size: 14px;}
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
</div>
<div class="sitebody sitevp cp2">
    <div class="container">
        <div class="contbox cp1">
            <h1 class="title">All Regions:</h1><hr><br>
            <?php require(__DIR__ . '/dbcred.php');
                $dbconn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if ( $dbconn->connect_error ) die( "Internal Error (database): " . $dbconn->connect_error );
    
                //Retrieve Page Base Content
                $result = $dbconn->query("SELECT `region_ID`,`region_str` FROM `add_regions`");
                if ( $result->num_rows > 0 ) {
                    while ( $row = $result->fetch_assoc() ) {
                        echo '<div class="location cp5"><div class="name">' . $row['region_str'] . '</div><hr><div class="tools"><a class="goto" href="./finder.php?t=' . $row['region_ID'] . '">Pricing <i class="fas fa-chart-line"></i></a> | <a class="goto" href="./analysis.php?t=' . $row['region_ID'] . '">Analysis <i class="fas fa-chart-pie"></i></a></div></div><br>'; }
                } else { header('Location: ./?e=2&r=' . $result->num_rows); exit; }
            ?>
        </div>
    </div>
</div>
</body>
</html>
