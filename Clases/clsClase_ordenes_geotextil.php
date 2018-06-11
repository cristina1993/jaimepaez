<?php

include_once 'Conn.php';


class Clase {

    var $con;

    function Clase() {
        $this->con = new Conn();
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_geotexti e, erp_i_productos p, erp_empresa m, erp_i_clientes c where e.pro_id=p.pro_id and m.emp_id=e.emp_id and c.cli_id=e.cli_id ORDER BY opg_id DESC");
        }
    }
    
    function lista_mostrar($emp){
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$emp");//no esta cojiendo de la tabala productos esta cojiendo de la misma tabla geotexti
        }
        
    }
    function lista_recupera($emp){
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM  erp_i_productos p, erp_i_orden_produccion_geotexti e where p.pro_id=e.pro_id and p.pro_id=$emp ORDER BY e.opg_id DESC LIMIT 1");//no esta cojiendo de la tabala productos esta cojiendo de la misma tabla geotexti
        }
        
    }
            function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_geotexti p, erp_empresa e 
where e.emp_id=p.emp_id 
and e.emp_id=$emp ORDER BY p.opg_codigo DESC LIMIT 1");
        }
    } 
    
    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }
  
    function lista_uno($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_geotexti where opg_id=$id");
        }
    }
    function lista_buscador($fec1,$fec2,$ord,$st) {
        if ($this->con->Conectar() == true) {
            if($fec1 == ''){
                $fec1 = '1991-09-20';
            }
            if($fec2 == ''){
                $fec2 = date('Y-m-d');
            }
            if ($ord == ''){
                $ord = '%';
            }
             if ($st == ''){
                $st = '%';
            }
            return pg_query("SELECT * FROM erp_i_orden_produccion_geotexti op, erp_i_productos p, erp_empresa e, erp_i_clientes c where p.pro_id=op.pro_id and e.emp_id=op.emp_id and c.cli_id=op.cli_id and op.opg_fec_pedido between '$fec1' and '$fec2' and op.opg_codigo like '$ord%' and op.opg_status like '$st%'");
        
            
            }
    }
    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_geotexti(         
              opg_codigo,
              cli_id,
              pro_id,
              emp_id,
              opg_num_rollos,
              opg_peso_producir,
              opg_fec_pedido,
              opg_fec_entrega,
              pro_mp1,
              pro_mp2,
              pro_mp3,
              pro_mp4,
              pro_mf1,
              pro_mf2,
              pro_mf3,
              pro_mf4,
              opg_kg1,
              opg_kg2,
              opg_kg3,
              opg_kg4,
              opg_ancho_total,
              opg_prod_principal,
              pro_largo,
              pro_ancho,
              pro_gramaje,
              opg_refilado,
              opg_caja1,
              opg_caja2,
              opg_caja3,
              opg_vel_transporte,
              opg_frecuencia,
              opg_capas,
              opg_doffer,
              opg_front,
              opg_random,
              opg_conveyor,
              opg_compensacion,
              opg_sensor1,
              opg_sensor2,
              opg_sensor3,
              opg_sensor4,
              opg_sensor5,
              opg_sensor6,
              opg_dosi_alimentacion,
              opg_mot_alimentacion,
              opg_mot_carda2,
              opg_mot_cilindro,
              opg_mot_gramaje,
              opg_hz,
              opg_vel_trans_madera,
              opg_vel_trans_caucho,
              opg_num_punzonadora,
              opg_vel_rod_salida,
              opg_vel_rod_compensadores,
              opg_vel_rod_entradawinder,
              opg_numpunzo_winder,
              opg_velrod_salidawinder,
              opg_numgolpes_punzo,
              opg_vel_enrolladora,
              opg_rev_min_calan,
              opg_observaciones,
              opg_status
              )
    VALUES ('$data[0]',$data[1],$data[2],3,$data[3],'$data[4]','$data[5]','$data[6]',$data[7],$data[8],$data[9],$data[10],'$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','$data[28]','$data[29]',$data[30],'$data[31]','$data[32]','$data[33]','$data[34]','$data[35]','$data[36]','$data[37]','$data[38]','$data[39]','$data[40]','$data[41]','$data[42]','$data[43]','$data[44]','$data[45]','$data[46]','$data[47]','$data[48]','$data[49]','$data[50]','$data[51]','$data[52]','$data[53]','$data[54]','$data[55]','$data[56]','$data[57]','$data[58]','$data[59]','PROGRAMADO')");
        }
    }
    function upd($data,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_orden_produccion_geotexti SET opg_codigo='$data[0]',
                    cli_id=$data[1],
                    pro_id=$data[2],
                    opg_num_rollos=$data[3],
                    opg_peso_producir='$data[4]',
                    opg_fec_pedido='$data[5]',
                    opg_fec_entrega='$data[6]',
                    pro_mp1=$data[7],
                    pro_mp2=$data[8],
                    pro_mp3=$data[9],
                    pro_mp4=$data[10],
                    pro_mf1='$data[11]',
                    pro_mf2='$data[12]',
                    pro_mf3='$data[13]',
                    pro_mf4='$data[14]',
                    opg_kg1='$data[15]',
                    opg_kg2='$data[16]',
                    opg_kg3='$data[17]',
                    opg_kg4='$data[18]',
                    opg_ancho_total='$data[19]',
                    opg_prod_principal='$data[20]',
                    pro_largo='$data[21]',
                    pro_ancho='$data[22]',
                    pro_gramaje='$data[23]',
                    opg_refilado='$data[24]',
                    opg_caja1='$data[25]',
                    opg_caja2='$data[26]',
                    opg_caja3='$data[27]',
                    opg_vel_transporte='$data[28]',
                    opg_frecuencia='$data[29]',
                    opg_capas='$data[30]',
                    opg_doffer='$data[31]',
                    opg_front='$data[32]',
                    opg_random='$data[33]',
                    opg_conveyor='$data[34]',
                    opg_compensacion='$data[35]',
                    opg_sensor1='$data[36]',
                    opg_sensor2='$data[37]',
                    opg_sensor3='$data[38]',
                    opg_sensor4='$data[39]',
                    opg_sensor5='$data[40]',
                    opg_sensor6='$data[41]',
                    opg_dosi_alimentacion='$data[42]',
                    opg_mot_alimentacion='$data[43]',
                    opg_mot_carda2='$data[44]',
                    opg_mot_cilindro='$data[45]',
                    opg_mot_gramaje='$data[46]',
                    opg_hz='$data[47]',
                    opg_vel_trans_madera='$data[48]',
                    opg_vel_trans_caucho='$data[49]',
                    opg_num_punzonadora='$data[50]',
                    opg_vel_rod_salida='$data[51]',
                    opg_vel_rod_compensadores='$data[52]',
                    opg_vel_rod_entradawinder='$data[53]',
                    opg_numpunzo_winder='$data[54]',
                    opg_velrod_salidawinder='$data[55]',
                    opg_numgolpes_punzo='$data[56]',
                    opg_vel_enrolladora='$data[57]',
                    opg_rev_min_calan='$data[58]',
                    opg_observaciones='$data[59]' WHERE opg_id=$id");
        }
    }
    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_orden_produccion_geotexti WHERE opg_id=$id");
        }
    }
     function lista_combo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_empresa ORDER BY emp_descripcion");
        }
    }
            function lista_combocli($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }
    function lista_combopro(){
        if($this->con->Conectar() == true){
            return pg_query("SELECT *FROM erp_i_productos ORDER BY pro_descripcion");
        }
    }
            function lista_combomp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and emp_id=$emp ORDER BY mpt_nombre");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
