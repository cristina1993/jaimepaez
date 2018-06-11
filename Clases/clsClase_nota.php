<?php

include_once 'Conn.php';

class Clase_nota_Credito {

    var $con;

    function Clase_nota_Credito() {
        $this->con = new Conn();
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_i_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$id'");
        }
    }

    function insert_movimiento($data) {
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
                mov_tabla,
                mov_val_unit,
                mov_val_tot
                            )
    VALUES ($data[0],$data[1],$data[2],$data[3],'$data[4]','$data[5]','" . date('Y-m-d') . "','" . date("H:i:s") . "','$data[6]','$data[7]','$data[8]','$data[9]')");
        }
    }

    function lista_motivo($num_secuencial, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and mov_documento='$num_secuencial' and bod_id=$emi");
        }
    }

    function delete_movimiento($emi, $num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt WHERE mov_documento='$num_secuencial' and bod_id=$emi and (trs_id=12 or trs_id=13)");
        }
    }
   
    function delete_asientos_notacredito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_documento='$num_secuencial'");
        }
    }
    
    function delete_movimiento_notacredito($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt WHERE mov_documento='$num_secuencial' and (trs_id=12 or trs_id=13)");
        }
    }
    
////////////////////////////////////////////////////////// LISTA NOTA CREDITO COMPLETO
    
    function lista_nota_credito_completo_noaut() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_credito WHERE  ncr_estado_aut is null or length(ncr_estado_aut) = 0 ORDER BY ncr_numero");
        }
    }
    
    function lista_una_nota_credito_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_id='$id'");
        }
    }
    
    function lista_detalle_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_nota_credito WHERE ncr_id='$id'");
        }
    }
    
    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }
    
    function lista_buscador_facturas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_credito $txt ");
        }
    }
    
}

?>
