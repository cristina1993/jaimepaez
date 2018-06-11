<?php

include_once 'Conn.php';

class Clase_industrial_egresopt {

    var $con;

    function Clase_industrial_egresopt() {
        $this->con = new Conn();
    }

    function lista($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_i_productos p, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id and t.trs_id=20 and m.bod_id=$bod order by ped_documento ");
        }
    }
    
    function lista_ped_nop($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=16 and m.bod_id=$bod order by ped_documento desc");
        }
    }
    

    function lista_reg($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_i_productos p, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id and m.ped_documento='$id' order by p.pro_codigo");
        }
    }
    
    function lista_mov_ped($ped) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt where mov_documento='$ped' limit 1 ");
        }
    }
    
    
    function lista_reg_nop($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_productos p, erp_transacciones t, erp_i_cliente c  where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_documento='$id' order by p.pro_a");
        }
    }

    function lista_cantidad($doc,$cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.mov_documento='$doc' and p.pro_codigo='$cod' and t.trs_operacion= 1");
        }
    }
    
    function lista_cantidad_nop($doc,$cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t WHERE m.pro_id=p.id and m.trs_id=t.trs_id and m.mov_documento='$doc' and p.pro_a='$cod' and t.trs_operacion= 1");
        }
    }
    

    function lista_pedido() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_i_productos p, erp_i_transaccion t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id order by ped_documento desc");
        }
    }

    function lista_ingreso_pedido_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_id='$id' ORDER BY p.pro_codigo");
        }
    }
    
    function lista_ingreso_pedido_documento_nop($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_id='$id' ORDER BY p.pro_a");
        }
    }
    

    function lista_buscador_industrial_egresopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_producto_terminado m, erp_i_productos p,erp_transacciones t, erp_i_cliente c where p.pro_id= m.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id $txt order by m.ped_documento desc");
        }
    }
    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where cod_cli=$id ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
