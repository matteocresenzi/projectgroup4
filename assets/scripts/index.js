    //Search Suggestions
    $(document).ready(function() {
        $('#sf').keyup(function(){
            let query = $('#sf').val();
            if (query.length > 2) {
                showSuggestions();
                presentSuggestions();
                loc_suggestion(query);
                console.log("searching for: " + query);
            } else { hideSuggestions();}
        });
    });
    
    //Location Services

        $("#autolocate").click( function(){
            $(".oSha").hide();
            $('.shadow').delay(500).removeClass('sh-init');
            $('.shadow').delay(500).addClass('sh-full');
            gps_suggestions();
        });

    
    //AJAX Functions
    function loc_suggestion(query) {
        let txt = { request:"ajax-city", city: query };
        let json = "req=" + JSON.stringify( txt );
        console.log("add: ajax - requesting \"" + json + "\"");
        $.post("ajax_handler.php", json, function(response) {
            if (response.status === "success" ) {
                console.log("add: success! - " + response.suggestions );
                if (response.suggestions === "none") {
                    $("#nlf").fadeIn(1000);
                } else {
                    $("#nlf").hide();
                    var jsondata = JSON.parse( response.suggestions );
                    updateSuggestions( jsondata );
                }
            } else { console.log("add: error(" + response.code +") - " + response.response); }
            
        });
    }
    function gps_suggestions() {
        if ( navigator.geolocation ) {
            navigator.geolocation.getCurrentPosition( function(position){
               
                let lat = position.coords.latitude;
                let lon = position.coords.longitude;
                let txt = { request:"ajax-loc", lat:lat, lon:lon};
                let json = "req=" + JSON.stringify( txt );
                console.log("add: ajax - requesting \"" + json + "\"");
                $.post("ajax_handler.php", json, function(response) {
                    if (response.status === "success" ) {
                        console.log("add: success! - " + response.suggestions );
                        presentSuggestions();
                        if (response.suggestions === "none") {
                            $("#nlf").fadeIn(1000);
                        } else {
                            var jsondata = JSON.parse( response.suggestions );
                            setTimeout( updateSuggestions( jsondata ), 30000 );
                        }
                    } else { console.log("add: error(" + response.code +") - " + response.response); }
                });
            }, function( error ) {
                presentSuggestions();
                $("#nlf").show();
                switch ( error.code ) {
                    case error.PERMISSION_DENIED:       console.log("add: js - PERMISSION_DENIED" );        break;    //TODO
                    case error.POSITION_UNAVAILABLE:    console.log("add: js - POSITION_UNAVAILABLE" );     break;    //TODO
                    case error.TIMEOUT:                 console.log("add: js - TIMEOUT" );                  break;    //TODO
                    case error.UNKNOWN_ERROR:           console.log("add: js - UNKNOWN_ERROR" );            break;    //TODO
                        default: console.log("add: js - other location error" ); break;
                }
            });
        } else {
            //TODO - Better error handling
            console.log("add: js - location services error" );
            presentSuggestions();
                $("#nlf").show();
        }
    }
    
    //DOM Manipulation Functions
    function showSuggestions() {
        $('.shadow').removeClass('sh-init');
        $('.shadow').addClass('sh-full');
        $('#sf').addClass('sfoffset');
        $('#autolocate').hide();
    }
    function hideSuggestions() {
        resetSuggestions();
        $('.shadow').removeClass('sh-full');
        $('.shadow').addClass('sh-init');
        $('#sf').removeClass('sfoffset');
        $('#autolocate').show();
    }
    function presentSuggestions() {
        $('#suggestions').show();
    }
    function resetSuggestions() {
        $('#suggestions').hide();
        $('#sfr1').hide();
        $('#sfr2').hide();
        $('#sfr3').hide();
    }
    function updateSuggestions( jsondata ) {
        for (var i = 0; i < jsondata.length; i++) {
            let target =  "#sfr" + String( i +  1 );
            $( target + " a" ).text( jsondata[i].cnm );//1 - viz
            $( target + " a" ).attr( "href", "finder.php?t=" + jsondata[i].cid ); //2 - loc
            $( target ).fadeIn( 500 * ( i + 1 ) );
        }
    }
