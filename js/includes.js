$('body').append("<div id='loading'></div><img src='../img/ajax-loader.gif' id='loading-im' style='position:fixed;'/>");
var boxH = $(window).height();
var boxW = $(window).width();
var inicio = (new Date()).getTime();
$('#loading').width(boxW);
$('#loading').height(boxH);
$('#loading-im').css('margin-top', '150px');
$('#loading-im').css('margin-left', '40%');
//$('.timeload').css('margin-left', '40%');
function TiempoCarga() {
    var fin = (new Date()).getTime();
    return ((fin - inicio) / 1000).toFixed(2) + ' seg';
}

$(function () {
    parent.document.getElementById('bottomFrame').src = '';
    parent.document.getElementById('contenedor2').rows = "*,0%";
    $('.cont_title').append("<font class='timeload' style='float:right'>" + TiempoCarga() + "</font>");
    $("#loading,#loading-im").fadeOut(100, function () {
        $(this).remove();
    });
});