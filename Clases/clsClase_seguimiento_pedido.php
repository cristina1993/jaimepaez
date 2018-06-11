<?php

include_once 'Conn.php';

class Clase_seguimiento_pedido {

    var $con;

    function Clase_seguimiento_pedido() {
        $this->con = new Conn();
    }

    function lista_seguimiento_pedido($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_seguimiento_pedido
                where bod_id=$bod
                order by seg_orden");
        }
    }

    function lista_seguimiento_pedido_orden($ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_seguimiento_pedido where seg_orden= '$ord' order by seg_id");
        }
    }
    function lista_un_seguimiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_seguimiento_pedido where seg_id=$id");
        }
    }

    function lista_producto_industrial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where pro_id=$id");
        }
    }

    function lista_producto_comercial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_productos where id=$id");
        }
    }

    function update_seguimiento($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_seguimiento_pedido SET seg_cantidad_recibida=$data where seg_id=$id");
        }
    }

    function insert_movimiento($data) {
        if ($this->con->Conectar() == true) {
            $fecha=  date('Y-m-d');
            $hora=  date('H:i:s');
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,
                mov_tranportista,
                mov_fecha_registro,
                mov_hora_registro
                )
                values(
                $data[0],
                $data[1],
                $data[2],
                $data[3],    
               '$data[4]',    
               '$data[5]',
               '$data[6]',
                $data[7],
               '$data[8]',
               '$fecha',
               '$hora'    
                )
                
                
                "
            );
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
