<?php

include_once 'Conn.php';

class Clase_industrial_movimientopt {

    var $con;

    function Clase_industrial_movimientopt() {
        $this->con = new Conn();
    }

    function lista_ingreso_industrial($bod, $trs) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id $trs and m.bod_id=$bod ORDER BY mov_documento desc");
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id $trs and m.bod_id=$bod ORDER BY mov_documento desc");
        }
    }

    function lista_movimiento_industrial($bod, $trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id $trs and m.bod_id=$bod ORDER BY m.mov_fecha_trans desc, m.mov_documento desc");
        }
    }

    function lista_ingreso_bodega($bod, $trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c  ,erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.cli_id=c.cli_id  $trs and m.bod_id=$bod ORDER BY mov_documento desc");
        }
    }

//    function lista_ingreso_bodega_producto($id, $x) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_i_cliente c  ,erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.cli_id=c.cli_id and p.id=$id and m.mov_tabla=1 and m.mov_id=$x ORDER BY mov_documento desc");
//        }
//    }
//
//    function lista_ingreso_bodega_iproducto($id, $x) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT m.*,p.*,t.*,c.* FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c  where  m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and p.pro_id=$id and m.mov_tabla=0 and m.mov_id=$x ORDER BY mov_documento desc");
//        }
//    }

    function lista_prod_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps  where ps.ids=p.ids and id=$id");
        }
    }

    function lista_prod_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function lista_un_prod_industriales_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$cod' ");
        }
    }

    function lista_un_prod_comercial_codlote($cod, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$cod' and pro_ac='$lote' ");
        }
    }

    function lista_secuencial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt ORDER BY mov_id DESC LIMIT 1");
        }
    }

    function lista_ultimo_secuencial_tp($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt where substr(mov_documento,4,1)='-' and substr(mov_documento,1,3)='$tp'  ORDER BY mov_documento DESC LIMIT 1");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt");
        }
    }

    function lista_un_ingreso_industrial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_id=$id");
        }
    }

    function lista_ingreso_industrial_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_documento='$id'");
        }
    }

    function lista_transaccion($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_transaccion where trs_id=$emp");
        }
    }

    function lista_buscador_industrial_ingresopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_mp p where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id $txt order by m.mov_fecha_trans, m.mov_documento asc");
        }
    }

    function insert_industrial_ingresopt($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_num_trans,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tranportista
            )
    VALUES ($data[0],$data[1],$data[2],$data[7],'$data[3]','$data[4]','1000','$data[5]','" . date('Y-m-d') . "','" . date("H:i:s") . "','$data[6]','')");
        }
    }

    function insert_movimiento_pt($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
            pro_id,
            trs_id,
            cli_id,
            bod_id,
            mov_documento,
            mov_fecha_trans,
            mov_fecha_registro,
            mov_hora_registro, 
            mov_cantidad,
            mov_tranportista,
            mov_tabla,
            mov_val_unit,
            mov_val_tot)
    VALUES ('$data[0]',
    '$data[1]',
    '$data[2]',
    '$data[3]',
    '$data[4]',
    '$data[5]',
    '$data[6]',
    '$data[7]',
    '$data[8]',
    '$data[9]',
    '$data[10]',
    '$data[11]',
    '$data[12]')"
            );
        }
    }

    function upd_industrial_ingreso($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_mov_inv_pt SET 
                mov_fecha_entrega='$data[1]', 
                mov_num_factura='$data[2]', 
                mov_pago='$data[3]', 
                mov_direccion='$data[4]', 
                mov_val_unit='$data[5]', 
                mov_descuento='$data[6]', 
                mov_iva=$data[7], 
                mov_flete='$data[8]' 
                WHERE mov_id=$data[0]");
        }
    }

    function delete_industrial_ingreso($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

    function lista_combo_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones order by trs_descripcion");
        }
    }

    function lista_combo_fabricas_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id>2 ORDER BY emp_descripcion");
        }
    }

    function lista_combo_fabricas_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id<=2 ORDER BY emp_descripcion");
        }
    }

    function lista_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos p, erp_empresa e where p.emp_id=e.emp_id and e.emp_id=$id ORDER BY p.pro_descripcion");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id'");
        }
    }

    function lista_proveedor($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_un_proveedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres FROM  erp_i_cliente where cli_id=$id");
        }
    }

    function lista_ultimo_ingreso_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id ORDER BY mov_id desc LIMIT 1");
        }
    }

    function lista_buscar_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id' or pro_descripcion='$id'");
        }
    }

    function lista_buscar_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps where ps.ids=p.ids and (p.pro_a='$id' or p.pro_b='$id')");
        }
    }

    function buscar_un_movimiento($id, $tab, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and m.pro_id='$id' and m.mov_tabla='$tab' and m.bod_id=$emi ORDER BY m.pro_id,m.mov_tabla");
        }
    }

    function lista_productos_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$id' and mp_i='0'");
        }
    }

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set WHERE ids<>79 and ids<>80 order by split_part(mp_tipo,'&',10) desc");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
