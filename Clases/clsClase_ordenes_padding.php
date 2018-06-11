<?php

include_once 'Conn.php';


class Clase_Orden_Padding {

    var $con;

    function Clase_Orden_Padding() {
        $this->con = new Conn();
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_padding e, erp_i_productos p, erp_empresa m where e.pro_id=p.pro_id and m.emp_id=e.emp_id ORDER BY opp_id DESC");
        }
    }
    
    function lista_mostrar($emp){
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$emp ");
        }
        
    }
    function lista_capturar($emp){
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM  erp_i_productos p, erp_i_orden_produccion_padding e where e.pro_id=p.pro_id and p.pro_id=$emp ORDER BY e.opp_id DESC LIMIT 1");
        }
        
    }
            function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e, erp_i_orden_produccion_padding p
where e.emp_id=p.emp_id and e.emp_id=$emp ORDER BY opp_codigo DESC LIMIT 1 ");
        }
    } 
    
    function lista_uno($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_padding where opp_id=$id");
        }
    }
    function lista_buscador($fec1,$fec2,$ord) {
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
            return pg_query("SELECT * FROM erp_i_orden_produccion_padding op, erp_i_productos p, erp_empresa e where p.pro_id=op.pro_id and e.emp_id=op.emp_id and op.opp_fec_pedido between '$fec1' and '$fec2' and op.opp_codigo like '$ord%'");
        
            
            }
    }
    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_padding(
              opp_codigo,
              cli_id,
              pro_id,
              emp_id,
              opp_cantidad,
              opp_fec_pedido,
              opp_fec_entrega,
              pro_ancho,
              pro_largo,
              pro_peso,
              pro_gramaje,
              opp_refilado1,
              opp_refilado2,
              pro_mp1,
              pro_mp2,
              pro_mp3,
              pro_mp4,
              pro_mf1,
              pro_mf2,
              pro_mf3,
              pro_mf4,
              opp_kg1,
              opp_kg2,
              opp_kg3,
              opp_kg4,
              opp_velocidad,
              opp_temp_rodillosup,
              opp_temp_rodilloinf,
              opp_observaciones,
              opp_status
            )
    VALUES ('$data[0]',$data[1],$data[2],4,'$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]',$data[12],$data[13],$data[14],$data[15],'$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]','$data[24]','$data[25]','$data[26]','$data[27]','PROGRAMADO')");
        }
    }
    function upd($data,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_orden_produccion_padding SET opp_codigo='$data[0]',cli_id=$data[1],pro_id=$data[2],opp_cantidad='$data[3]',opp_fec_pedido='$data[4]',opp_fec_entrega='$data[5]',pro_ancho='$data[6]',pro_largo='$data[7]',pro_peso='$data[8]',pro_gramaje='$data[9]',opp_refilado1='$data[10]',opp_refilado2='$data[11]',pro_mp1=$data[12],pro_mp2=$data[13],pro_mp3=$data[14],pro_mp4=$data[15],pro_mf1='$data[16]',pro_mf2='$data[17]',pro_mf3='$data[18]',pro_mf4='$data[19]',opp_kg1='$data[20]',opp_kg2='$data[21]',opp_kg3='$data[22]',opp_kg4='$data[23]',opp_velocidad='$data[24]',opp_temp_rodillosup='$data[25]',opp_temp_rodilloinf='$data[26]',opp_observaciones='$data[27]' WHERE opp_id=$id");
        }
    }
    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_orden_produccion_padding WHERE opp_id=$id");
        }
    }
    
    function lista_combo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }
    function lista_combopro($emp){
        if($this->con->Conectar() == true){
            return pg_query("SELECT *FROM erp_i_productos ORDER BY pro_descripcion where emp_id=$emp");
        }
    }
            function lista_combomp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and emp_id=$emp ORDER BY mp_referencia");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
