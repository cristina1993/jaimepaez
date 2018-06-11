<?php
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ordenes_padding.php';
$Clase = new Clase_Orden_Padding();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        if (empty($id)) {
            if ($Clase->insert($data) == true) {
                $sms = 0;
            } else {
                $sms = pg_last_error();
            }
        } else {
            if ($Clase->upd($data, $id) == true) {
                $sms = 0;
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase->delete($id) == true) {
            $sms = 0;
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
      
        $rst=  pg_fetch_array($Clase->lista_secuencial($id));
         $sec=  (substr($rst[opp_codigo],-5)+1);
        if($sec>=0 && $sec<10){
            $txt='0000';
        }else if($sec>=10 && $sec<100){
            $txt='000';
        }else if($sec>=100 && $sec<1000){
            $txt='00';
        }else if($sec>=1000 && $sec<10000){
            $txt='0';
        }else if($sec>=10000 && $sec<100000){
            $txt='';
        }
        $retorno=$rst[emp_sigla].$txt.$sec;
        echo $retorno;
        break;
    case 3:
        
        $rst=  pg_fetch_array($Clase->lista_mostrar($id));
        $ancho=$rst[pro_ancho];
        $largo=$rst[pro_largo];
        $peso=$rst[pro_peso];
        $gramaje=$rst[pro_gramaje];
        $mf1=$rst[pro_mf1];
        $mf2=$rst[pro_mf2];
        $mf3=$rst[pro_mf3];
        $mf4=$rst[pro_mf4];
        $mp1=$rst[pro_mp1];
        $mp2=$rst[pro_mp2];
        $mp3=$rst[pro_mp3];
        $mp4=$rst[pro_mp4];
        $velocidad=$rst[opp_velocidad];
        $rodillosup=$rst[opp_temp_rodillosup];
        $rodilloinf=$rst[opp_temp_rodilloinf];
        
        
        $cns=$Clase->lista_combomp($id);
        $combo="<option value='0'>Seleccione</option>";
        while($rst=  pg_fetch_array($cns)){
            $combo.="<option value='$rst[mpt_id]'>$rst[mpt_nombre]</option>";
        }     
        echo $ancho.'&'.$largo.'&'.$peso.'&'.$gramaje.'&'.$mf1.'&'.$mf2.'&'.$mf3.'&'.$mf4.'&'.$mp1.'&'.$mp2.'&'.$mp3.'&'.$mp4.'&'.$velocidad.'&'.$rodillosup.'&'.$rodilloinf.'&'.$combo;
        break;
}
?>
