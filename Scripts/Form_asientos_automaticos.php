<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_asientos.php';
$Clase_asientos = new Clase_asientos();
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>

            $(function () {
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("id");
                    var i = a.substring(4, 5);
                    if ($('#con_documento' + i).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
            });

            function save(op) {
                alert(op);
                $.ajax({
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: op}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            alert('INSERCION CORRECTA')
                            window.history.go(0);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }



        </script>
    </head>
    <body>

        <?PHP
        if ($x != 1) {
            ?> 
            <button id="guardar" onclick="save(0)">Factura</button>   
            <button id="nota_cred" onclick="save(1)">Nota Credito</button>   
            <button id="pagos_fac" onclick="save(2)">Pagos</button>   
            <?PHP
        }
        ?>

    </body>
</html>    
