<?php session_start(); ?>
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
    <link rel="stylesheet" href="assets/style/add_main.css">
    <link rel="stylesheet" href="assets/style/add_index.css">
    <script src="assets/scripts/general.js"></script>
    <style>
        .inner {
            background-image: url('assets/images/avocados-sliced.jpeg');
            background-repeat: no-repeat, repeat;
            background-size: cover;
            height: 400px;
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
            <h1 class="title">Avocado Finder</h1><hr>
            <div class="inner">
                <div class="shadow sh-init"></div>
                <input type="text" placeholder="Where?" id="sf" autocomplete="off" class="oSha"><br>
                <button id="autolocate" class="cp1 oSha">Locate Me <i class="fas fa-location-arrow"></i></button>
                <!--<div id="loader"></div> -->
                <div id="suggestions">
                    <div id="nlf">No Locations Found... <br><a href="locations.php">View All Locations <i class="fas fa-arrow-alt-circle-right"></i></a></div>
                    <div class="item cp1" id="sfr1"><a href="#"></a></div><br>
                    <div class="item cp1" id="sfr2"><a href="#"></a></div><br>
                    <div class="item cp1" id="sfr3"><a href="#"></a></div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <footer class="cp1"><span>Copyright 2021 ITSC3155 Group4</span></footer>
</div>
<script src="assets/scripts/index.js"></script>
</body>
</html>
