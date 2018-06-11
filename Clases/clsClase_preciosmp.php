<?php

include_once 'Conn.php';

class Clase_preciosmp {

    var $con;

    function Clase_preciosmp() {
        $this->con = new Conn();
    }

    function lista_precios($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp order by mp_d");
        }
    }

    function lista_buscador_precios($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp p, erp_tipos t where cast(p.mp_a as integer)=t.tps_id $txt order by mp_d");
        }
    }

    function lista_buscador_precios_cero() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp p, erp_tipos t where cast(p.mp_a as integer)=t.tps_id and mp_e='0' order by mp_d");
        }
    }

    function upd_precios($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_e='$data[0]',mp_f='$data[1]',mp_g='$data[2]', mp_h='$data[3]', mp_j='$data[4]',mp_k='$data[5]',mp_l='$data[6]',mp_m='$data[7]' WHERE id=$id");
        }
    }

    function upd_precios_todos($desc, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_g='$desc' where id=$id");
        }
    }

    function upd_precios2($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_e = mp_f, mp_f='0' where id=$id");
        }
    }

    function update_descuentos($data, $id, $des) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_descuentos SET pre_id=$id, des_fec_inicio='$data[0]',des_fec_fin='$data[1]',des_valor='$data[2]',cod_punto_emision='$data[3]' where des_id=$des");
        }
    }

    function lista_productos_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$id' and mp_i='0'");
        }
    }

    function lista_buscador_agrup($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select p.mp_a, p.mp_b,t.tps_orden from  erp_mp p, erp_tipos t where cast(p.mp_a as integer)=t.tps_id and (t.tps_tipo='0&1&0' or t.tps_tipo='1&1&0') and $txt group by p.mp_a, p.mp_b,t.tps_orden order by t.tps_orden");
        }
    }

    function lista_tipos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_tipos where tps_id=$id");
        }
    }

    function lista_productos_ab($a, $b, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select *  from  erp_mp p, erp_tipos t where cast(p.mp_a as integer)=t.tps_id and (t.tps_tipo='0&1&0' or t.tps_tipo='1&1&0') and p.mp_a='$a' and p.mp_b='$b' and $txt order by p.mp_c,p.mp_e");
        }
    }

    function lista_impuesto($t) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion where por_siglas='$t' ORDER BY por_descripcion");
        }
    }

    function lista_un_impuesto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion where por_id=$id");
        }
    }

    function upd_costos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_p='$data[0]',mp_r='$data[1]' WHERE id=$id");
        }
    }

    function upd_costos2($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_p = mp_r, mp_r='0' where id=$id");
        }
    }

    function upd_costos_importe($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp SET mp_p='$data[0]',mp_r='$data[1]',mp_y='$data[2]',mp_z='$data[3]' WHERE id=$id");
        }
    }

    ////////////reporte PrecioVP
    
    function lista_productos_factura($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * from PrecioVP $txt");
        }
    }
    
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
