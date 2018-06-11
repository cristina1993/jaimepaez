<?php

include_once 'Conn.php';

class Clase_preciospt {

    var $con;

    function Clase_preciospt() {
        $this->con = new Conn();
    }

    function lista_precios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios order by pre_id asc");
        }
    }
    
     function lista_buscador_precios1($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pro_precios p, erp_i_productos pro where p.pro_id=pro.pro_id $txt order by pro.pro_descripcion");
        }
    }


    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where substr(cli_codigo,1,2)='$tp' order by cli_codigo desc limit 1");
        }
    }

    function lista_precios_proid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id ");
        }
    }

    function lista_precios_proid_tabla($id, $tabla) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id and pro_tabla=$tabla");
        }
    }

    function lista_i_productos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function lista_productos_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$cod' ");
        }
    }
    
    function lista_productos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id=$id");
        }
    }
    

    function lista_buscador_precios($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pro_precios where $bod");
        }
    }

    function lista_buscador_i_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where $txt");
        }
    }

    function lista_buscador_productos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where $txt");
        }
    }

    function insert_precios($proid) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pro_precios (pro_id, pro_tabla, pre_precio) VALUES ($proid,0,'0')");
        }
    }

    function upd_precios($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_precio='$data[0]',pre_descuento='$data[1]',pre_iva='$data[2]',pre_precio2='$data[3]' WHERE pre_id=$id");
            //return pg_query("UPDATE erp_pro_precios SET pre_precio=$data[0],pre_iva='$data[1]' WHERE pre_id=$id");
        }
    }

    function ultimo_precios() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pro_precios order by pre_id desc limit 1");
        }
    }

    function del_pre($id, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_pro_precios WHERE pro_id=$id and pro_tabla=$tab");
        }
    }

    function upd_precios_todos($desc) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_descuento='$desc'");
        }
    }
    
    function upd_precios2($desc) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_precio2='$desc', pre_vald_precio1 = 1, pre_vald_precio2=0");
        }
    }
    
    function upd_precios_precios2() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_precio = pre_precio2");
        }
    }
    
    function upd_vald_pre1() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio2=0");
        }
    }
    
    function upd_vpre1_pre2() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio1=1");
        }
    }
    
    function upd_vald_pre2() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio1=0");
        }
    }
    
    function upd_vpre2_pre1() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pro_precios SET pre_vald_precio2=1");
        }
    }

    function ultimo_producto_comercial() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_productos order by id desc limit 1");
        }
    }

    function ultimo_producto_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_productos order by pro_id desc limit 1");
        }
    }

    function lista_precios_id($id, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id ='$id' and pro_tabla=$tab");
        }
    }

    function lista_emisor() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor");
        }
    }

    function lista_emisor_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor where cod_punto_emision=$cod");
        }
    }

    function insert_descuento($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_descuentos(pre_id, des_fec_inicio, des_fec_fin, des_valor,cod_punto_emision) VALUES ($id,'$data[0]','$data[1]','$data[2]','$data[3]')");
        }
    }

    function update_descuentos($data, $id, $des) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_descuentos SET pre_id=$id, des_fec_inicio='$data[0]',des_fec_fin='$data[1]',des_valor='$data[2]',cod_punto_emision='$data[3]' where des_id=$des");
        }
    }

    function delete_descuentos($des) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_descuentos where des_id=$des");
        }
    }
    
    function lista_un_descuento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos where pre_id=$id");
        }
    }

    function lista_descuentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos");
        }
    }

    function lista_buscar_descuentos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos d, erp_pro_precios p where d.pre_id=p.pre_id and $txt");
        }
    }

    function lista_un_descuento_precio($id, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos d, erp_pro_precios p  where d.pre_id=p.pre_id and d.pre_id=$id and d.cod_punto_emision=$pto");
        }
    }

    function lista_un_descuento_fecha($id, $pto, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_descuentos d, erp_pro_precios p  where d.pre_id=p.pre_id and d.pre_id=$id and d.cod_punto_emision=$pto and '$fec' between d.des_fec_inicio  and d.des_fec_fin");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
