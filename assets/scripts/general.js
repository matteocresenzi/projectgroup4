$(document).ready(function(){
    $('#menu').click(function(){ toggleMenu(); });
    $('#menu-bg').click(function(){ toggleMenu(); });
});
function toggleMenu(){
    $('#menu-bg').fadeToggle(1000);
    $('#menu-fg').fadeToggle(500);
}
