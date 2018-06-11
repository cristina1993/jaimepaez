<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_cupo($id));
} else {
    $id = 0;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
$(function(){
    $('#usu_id').val(<?php echo $rst[usu_id]?>);
    
    $(this).keypress(function(e){
        if(e.keyCode ==13){
            save(this.lang);
            return false;
        }
    })
    $('#save').click(function(){
           save(this.lang);
           return false;
      })
     $('#cancel').click(function(){
         cancelar();
         return false;
    });
})
            
function save(id){
                    var dat = Array(
                            usu_id.value,
                            cup_mensual.value,
                            cup_xorden.value );
                            
                        $.ajax({
                            type: 'POST',
                            url: "actions.php",
                            data:{act:47,'data[]':dat,id:id},
                            beforeSend:function() {
                                if(usu_id.value=='0'){
                                    $("#usu_id").css({borderColor:"red"});
                                    $("#usu_id").focus();
                                    return false;
                                }else if(cup_mensual.value.length==0){
                                    $("#cup_mensual").css({borderColor:"red"});
                                    $("#cup_mensual").focus();
                                    return false;
                                }else if(cup_xorden.value.length==0){
                                    $("#cup_xorden").css({borderColor:"red"});
                                    $("#cup_xorden").focus();
                                    return false;
                                }
                                
                            },
                            success: function (dt) {
                                if(dt==0){
                                    window.history.go(0);
                                }else{
                                    dat=dt.split(' ');
                                    if(dat[2]=='llave'){
                                        alert('El usuario ya tiene asignado un cupo');
                                    }else{
                                        alert(dt)
                                    }
                                    
                                }
                                
                                            }
                        })
}

function cancelar(){
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
}



        </script>
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" >Cupos</th></tr>
            </thead>
            <tr>
                <td>Usuario:</td>
                <td>
                    <select name="usu_id" id="usu_id">
                        <option value="0">Elija un Usuario</option>
                        <?php 
                        $cns_user=$User->lista_usuarios_estado('t');
                        while($rst_user=pg_fetch_array($cns_user)){
                            echo "<option value='$rst_user[usu_id]'>$rst_user[usu_person]</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Cupo Mensual:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="cup_mensual" id="cup_mensual" size="10" value="<?php echo $rst[cup_mensual]?>"  /></td>
            </tr>
            <tr>
                <td>Cupor x Orden:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="cup_xorden" id="cup_xorden" size="10" value="<?php echo $rst[cup_xorden] ?>"  /></td>
            </tr>                    
            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                            <button id="save" lang="<?php echo $id ?>" >Guardar</button>                           
                    <?php }
                    ?>
                    <button id="cancel" >Cancelar</button>                           
                </td>
            </tr>                    
        </table>
    </body>
</html>