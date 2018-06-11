<?php

include_once 'Conn.php';


class Clase_egreso_nopertipt{

    var $con;

    function Clase_egreso_nopertipt() {
        $this->con = new Conn();
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_producto p where e.emp_id=p.emp_id ORDER BY pro_id DESC");
        }
    }
    
    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_producto p, erp_empresa e 
where e.emp_id=p.emp_id
and e.emp_id=$emp ORDER BY p.pro_codigo DESC LIMIT 1 ");
        }
    }
    
    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }
    
    
    function lista_uno($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_producto p where e.emp_id=p.emp_id and pro_id=$id");
        }
    }
    function lista_buscador($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_producto p where e.emp_id=p.emp_id and emp_descripcion ='$emp'");
        }
    }
    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_producto(
            emp_id, 
            pro_codigo,
            pro_familia, 
            pro_descripcion, 
            pro_ancho, 
            pro_largo, 
            pro_capa, 
            pro_espesor, 
            pro_gramaje, 
            pro_peso, 
            pro_medvul,
            pro_mp1,
            pro_mp2,
            pro_mp3,
            pro_mp4,
            pro_mf1,
            pro_mf2,
            pro_mf3,
            pro_mf4
            )
    VALUES ($data[0],'$data[1]',$data[2],'$data[3]','$data[5]','$data[4]',$data[6],'$data[7]','$data[8]','$data[9]','$data[10]',$data[11],$data[12],$data[13],$data[14],'$data[15]','$data[16]','$data[17]','$data[18]')");
        }
    }
    function upd($data,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_producto SET emp_id=$data[0],pro_codigo='$data[1]',pro_familia=$data[2],pro_descripcion='$data[3]',pro_ancho='$data[5]',pro_largo='$data[4]',pro_capa=$data[6],pro_espesor='$data[7]',pro_gramaje='$data[8]',pro_peso='$data[9]',pro_medvul='$data[10]',pro_mp1=$data[11],pro_mp2=$data[12],pro_mp3=$data[13],pro_mp4=$data[14],pro_mf1='$data[15]',pro_mf2='$data[16]',pro_mf3='$data[17]',pro_mf4='$data[18]' WHERE pro_id=$id");
        }
    }
    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_producto WHERE pro_id=$id");
        }
    }
    
    function lista_combo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa ORDER BY emp_descripcion");
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
