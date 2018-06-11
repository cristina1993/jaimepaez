<?php

include_once 'Conn.php';


class Clase_Productos {

    var $con;

    function Clase_Productos() {
        $this->con = new Conn();
    }

    function lista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_i_productos p where e.emp_id=p.emp_id ORDER BY pro_id DESC");
        }
    }
    
    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_empresa e 
where e.emp_id=p.emp_id
and e.emp_id=$emp ORDER BY p.pro_codigo DESC LIMIT 1 ");
        }
    }
    
    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }
    
    
    function lista_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa e ,erp_i_productos p where e.emp_id=p.emp_id and pro_codigo='$id'");
        }
    }
    
    function lista_buscador($codigo,$emp) {
        if ($this->con->Conectar() == true) {
            if($codigo !=''){
                return pg_query("SELECT * FROM erp_i_productos WHERE pro_descripcion like '%$codigo%' or pro_codigo like '%$codigo%'");
            }
            
                return pg_query("SELECT * FROM erp_empresa e ,erp_i_productos p where e.emp_id=p.emp_id and e.emp_id=$emp");
        
        }
    }
    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_productos(
            emp_id, 
            pro_codigo,
            pro_familia, 
            pro_descripcion, 
            pro_uni,
            pro_largo,
            pro_ancho,
            pro_capa, 
            pro_espesor, 
            pro_gramaje, 
            pro_peso, 
            pro_medvul,
            pro_mp1,
            pro_mp2,
            pro_mp3,
            pro_mp4,
            pro_mp5,
            pro_mp6,
            pro_mf1,
            pro_mf2,
            pro_mf3,
            pro_mf4,
            pro_mf5,
            pro_mf6,
            tpt_id
            )
    VALUES ($data[0],'$data[1]',$data[2],'$data[3]','$data[4]','$data[5]','$data[6]',$data[7],'$data[8]','$data[9]','$data[10]','$data[11]',$data[12],$data[13],$data[14],$data[15],$data[16],$data[17],'$data[18]','$data[19]','$data[20]','$data[21]','$data[22]','$data[23]',$data[24])");
        }
    }
    function upd($data,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_productos SET emp_id=$data[0],pro_codigo='$data[1]',pro_familia=$data[2],pro_descripcion='$data[3]',pro_uni='$data[4]',pro_largo='$data[5]',pro_ancho='$data[6]',pro_capa=$data[7],pro_espesor='$data[8]',pro_gramaje='$data[9]',pro_peso='$data[10]',pro_medvul='$data[11]',pro_mp1=$data[12],pro_mp2=$data[13],pro_mp3=$data[14],pro_mp4=$data[15],pro_mp5=$data[16],pro_mp6=$data[17],pro_mf1='$data[18]',pro_mf2='$data[19]',pro_mf3='$data[20]',pro_mf4='$data[21]',pro_mf5='$data[22]',pro_mf6='$data[23]',tpt_id=$data[24] WHERE pro_id=$id");
        }
    }
    function delete($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_productos WHERE pro_id=$id");
        }
    }
    function lst_emp(){
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id>2 ORDER BY emp_descripcion");
        }
    }
//    function lista_empresas() {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_empresa where emp_id>2 ORDER BY emp_descripcion");
//        }
//    }
    function lista_combomp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mp m, erp_i_tpmp t where m.mpt_id=t.mpt_id and emp_id=$emp ORDER BY mpt_nombre");
        }
    }
    
    
     function insert_precios($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pro_precios (pro_id,  pre_precio,pre_descuento,pre_iva,pro_tabla,pre_vald_precio1) VALUES ($data[0],'$data[1]','$data[2]','$data[3]','0','1')");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
