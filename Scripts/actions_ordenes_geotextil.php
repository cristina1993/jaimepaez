<?php
$_SESSION[User]='PRUEBA';          
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_ordenes_geotextil.php';
$Clase = new Clase();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$Adt = new Auditoria();  
switch ($op) {
    case 0:
        if (empty($id)) {
            if ($Clase->insert($data) == true) {
                $sms = 0;
                $fields = str_replace("&", ",", $fields[0]);
                $modulo='Produccion Geotextil';
                $accion='Insert';
                 if($Adt->insert_audit_general($modulo,$accion,$fields)==false){
                 $sms="Auditoria".pg_last_error(); 
                        }
            } else {
                $sms = pg_last_error();
            }
        } else {
            if ($Clase->upd($data, $id) == true) {
                $sms = 0;
                $fields = str_replace("&", ",", $fields[0]);
                $modulo='Produccion Geotextil';
                $accion='Editar';
                 if($Adt->insert_audit_general($modulo,$accion,$fields)==false){
                 $sms="Auditoria".pg_last_error(); 
                        }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase->delete($id) == true) {
            $sms = 0;
            $fields = str_replace("&", ",", $fields[0]);
                $modulo='Produccion Geotextil';
                $accion='Eliminar';
                 if($Adt->insert_audit_general($modulo,$accion,$fields)==false){
                 $sms="Auditoria".pg_last_error(); 
                        }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
      
        $rst=  pg_fetch_array($Clase->lista_secuencial($id));
         $sec=  (substr($rst[opg_codigo],-5)+1);
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
        $rst1= pg_fetch_array($Clase->lista_siglas($id));
        $retorno='P'.$rst1[emp_sigla].$txt.$sec;
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
        $descripcion=$rst[pro_descripcion];
        
        $rst1=  pg_fetch_array($Clase->lista_recupera($id));
        $caja1=$rst1[opg_caja1];
        $caja2=$rst1[opg_caja2]; 
        $caja3=$rst1[opg_caja3]; 
        $transporte=$rst1[opg_vel_transporte]; 
        $frecuencia=$rst1[opg_frecuencia]; 
        $capas=$rst1[opg_capas]; 
        $doffer=$rst1[opg_doffer]; 
        $front=$rst1[opg_front]; 
        $random=$rst1[opg_random]; 
        $conveyor=$rst1[opg_conveyor]; 
        $compensacion=$rst1[opg_compensacion]; 
        $sensor1=$rst1[opg_sensor1]; 
        $sensor2=$rst1[opg_sensor2];
        $sensor3=$rst1[opg_sensor3]; 
        $sensor4=$rst1[opg_sensor4]; 
        $sensor5=$rst1[opg_sensor5]; 
        $sensor6=$rst1[opg_sensor6]; 
        $dosiali=$rst1[opg_dosi_alimentacion]; 
        $motali=$rst1[opg_mot_alimentacion]; 
        $carda2=$rst1[opg_mot_carda2]; 
        $cilindro=$rst1[opg_mot_cilindro]; 
        $motgra=$rst1[opg_mot_gramaje]; 
        $hz=$rst1[opg_hz]; 
        $tmadera=$rst1[opg_vel_trans_madera]; 
        $tcaucho=$rst1[opg_vel_trans_caucho]; 
        $punzadora=$rst1[opg_num_punzonadora]; 
        $rodsalida=$rst1[opg_vel_rod_salida]; 
        $rodcomp=$rst1[opg_vel_rod_compensadores]; 
        $entradawin=$rst1[opg_vel_rod_entradawinder]; 
        $winder=$rst1[opg_numpunzo_winder]; 
        $dalidawin=$rst1[opg_velrod_salidawinder]; 
        $punzo=$rst1[opg_numgolpes_punzo]; 
        $enrolladora=$rst1[opg_vel_enrolladora]; 
        $calan=$rst1[opg_rev_min_calan];  
        $observaciones=$rst1[opg_observaciones]; 
        
        
        $cns=$Clase->lista_combomp($id);
        $combo="<option value='0'>Seleccione</option>";
        while($rst=  pg_fetch_array($cns)){
            $combo.="<option value='$rst[mpt_id]'>$rst[mpt_nombre]</option>";
        }     
        echo $ancho.'&'.$largo.'&'.$peso.'&'.$gramaje.'&'.$mf1.'&'.$mf2.'&'.$mf3.'&'.$mf4.'&'.$mp1.'&'.$mp2.'&'.$mp3.'&'.$mp4.'&'.$descripcion.'&'.$caja1.'&'.$caja2.'&'.$caja3.'&'.$transporte.'&'.$frecuencia.'&'.$capas.'&'.$doffer.'&'.$front.'&'.$random.'&'.$conveyor.'&'.$compensacion.'&'.$sensor1.'&'.$sensor2.'&'.$sensor3.'&'.$sensor4.'&'.$sensor5.'&'.$sensor6.'&'.$dosiali.'&'.$motali.'&'.$carda2.'&'.$cilindro.'&'.$motgra.'&'.$hz.'&'.$tmadera.'&'.$tcaucho.'&'.$punzadora.'&'.$rodsalida.'&'.$rodcomp.'&'.$entradawin.'&'.$winder.'&'.$dalidawin.'&'.$punzo.'&'.$enrolladora.'&'.$calan.'&'.$observaciones.'&'.$combo;
        break;
}
?>
