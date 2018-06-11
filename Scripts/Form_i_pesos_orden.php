<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
$barcode = $_GET[barcode];
$peso_reg = $_GET[p_entregado];
$doc = $_GET[doc];
if (isset($_GET[id])) {
    $cns_pesos = $Set->lista_pesos_barcode($barcode);
    if (pg_num_rows($cns_pesos) == 0) {
        $cns_pesos = $Set->lista_pesos_det_id($id);
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                //    $(this).keypress(function(e){
                //        if(e.keyCode ==13){
                //            save('<?php //echo $id   ?>','<?php //echo $barcode   ?>',0,'<?php //echo $doc   ?>');
                //            return false;
                //        };
                //    });
                $('#save').click(function () {
                    save('<?php echo $id ?>', '<?php echo $barcode ?>', 0, '<?php echo $doc ?>');
                    return false;
                });
                $('#print').click(function () {
                    save('<?php echo $id ?>', '<?php echo $barcode ?>', 1, '<?php echo $doc ?>');
                    return false;
                });

            })
            function save(id, barcode, x, doc) {
                var f = new Date();
                var fecha = f.getFullYear() + '-' + (f.getMonth() + 1) + '-' + f.getDate();
                var dat = Array();
                $('.datos').each(function () {
                    if ((this.value * 1) > 0 && this.name == 'nodato' && this.value.length > 0) {
                        dat.push(id + '%' +
                                '1' + '%' +
                                this.value + '%' +
                                fecha + '%' +
                                barcode + '%' +
                                '0');
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: "actions.php",
                    beforeSend: function () {
                        if ((sum_peso.value * 1) > (peso_reg.value * 1)) {
                            alert('El peso Ingresado supera al Registrado');
                            $("#etq_peso1").focus();
                            return false;
                        } else {
                            return true;
                        }
                    },
                    data: {act: 33, 'data[]': dat, doc: doc},
                    success: function (dt) {
                        if (dt == 0) {
                            if (x == 0) {
                                window.history.go(0);
                            } else {
                                window.location = "../Reports/etq_ord_compra.php?id=" +<?php echo $id ?>
                            }

                        } else {
                            alert(dt);
                        }
                    }
                })
            }
            function calcula_peso(obj) {
            
                var t = devuelve_peso();
                peso=peso_reg.value.replace(',', '')

                if (t*1 > (peso*1)){
                    $(obj).val(0);
                    alert('El peso Ingresado supera al Registrado');
                    $(obj).focus();
                    t = devuelve_peso();
                }
                $('#sum_peso').val(t);
            }

            function devuelve_peso() {
                var total = 0;
                $('.datos').each(function () {
                    if (this.value.length > 0) {
                        total = (total * 1 + this.value * 1).toFixed(1);
                    }
                });

                return total;

            }


            function cancelar() {
                window.history.go();
            }
        </script>
        <style>
            #tbl_form{
                float:left;
                border: solid 1px #ccc !important;
                margin: 1px;
            }
            .datos{
                border:solid 1px #63b8ff !Important;  
                width:70px; 
                color:black !Important; 
            }
        </style>
    </head>
    <body>
        <table id="tbl_form" cellpadding="1"  >
            <?PHP
            $n = 0;
            $sum_peso = 0;
            while ($rst_pesos = pg_fetch_array($cns_pesos)) {
                $n++;
                if ($n == 11 || $n == 21 || $n == 31 || $n == 41 || $n == 51 || $n == 61 || $n == 71 || $n == 81 || $n == 91) {
                    echo "</table><table id='tbl_form' cellpadding='1' >";
                }
                $sum_peso+=$rst_pesos[etq_peso];
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><input style="text-align:right " class="datos" readonly name="sidato" type="text" id="<?php echo 'etq_peso' . $n ?>" lang="<?php echo $n ?>" value="<?php echo $rst_pesos[etq_peso] ?>"  onchange="calcula_peso()" onkeyup="this.value = this.value.replace(/[^0-9-]/, '');" /></td>
                </tr>
                <?php
            }
            while ($n < 100) {
                $n++;
                if ($n == 11 || $n == 21 || $n == 31 || $n == 41 || $n == 51 || $n == 61 || $n == 71 || $n == 81 || $n == 91) {
                    echo "</table><table id='tbl_form' cellpadding='1' >";
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><input class="datos" name="nodato" type="text" id="<?php echo 'etq_peso' . $n ?>" lang="<?php echo $n ?>" value="<?php echo $rst_pesos[etq_peso] ?>"  onchange="calcula_peso(this)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '');" /></td>
                </tr>        
                <?php
            }
            ?>
        </table><br/>
        <table width="100%">
            <tr style="font-size:11px;border-bottom:solid 3px #ccc; ">
                <td >
                    Peso Maximo:<input type="text" readonly id="peso_reg" value="<?php echo number_format($peso_reg, 1) ?>" />
                    Suma:<input type="text" readonly id="sum_peso" value="<?php echo number_format($sum_peso, 1) ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <button id="save" >Guardar</button>  
                    <button id="print" >Guardar e Imprimir</button>                      
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>
        </table>
    </body>
</html>