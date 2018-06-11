<?php

include_once 'Conn.php';

class Clase_pagos {

    var $con;

    function Clase_pagos() {
        $this->con = new Conn();
    }

////CAMBIOS 30/12/2014
    function insert_pagos($data) {
        if ($this->con->Conectar() == true) {
             return pg_query($data);
//            return pg_query("INSERT INTO erp_pagos_factura 
//(
// com_id,
//  pag_tipo,
//  pag_porcentage,
//  pag_dias,
//  pag_valor,
//  pag_fecha_v,
//  pag_forma,
//  pag_banco,
//  pag_tarjeta,
//  pag_cant,
//  pag_contado,
//  chq_numero,
//  pag_id_chq
//) values ('$data[0]',
//'$data[1]',
//'$data[2]',
//'$data[3]',
//'$data[9]',
//'$data[5]',
//'$data[6]',
//'$data[7]',
//'$data[8]',
//'$data[9]',
//'$data[10]',               
//'$data[11]',               
//'$data[12]')               
//                ");
        }
    }
    
    
    function insert_ctasxcobrar($data, $pln) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar(
                com_id, 
                cta_fecha, 
                cta_monto, 
                cta_forma_pago, 
                cta_banco,
                pln_id,
                cta_fecha_pago,
                pag_id,
                num_documento,
                cta_concepto,
                asiento)
        VALUES ($data[0],'$data[1]','$data[2]','$data[3]','$data[4]',$pln,'$data[6]',$data[7],'$data[8]','$data[11]','$data[12]')");
        }
    }


    

//    
//    function lista_detalle_pagos($fact) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_pago_factura where num_factura='$fact'   ");
//        }
//    }

    function lista_pago_factura($pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pago_factura where num_factura='$pag'");
        }
    }

    function lista_un_comprobante($cod) {
//        print_r($cod);
        if ($this->con->Conectar() == true) {
//               return pg_query("SELECT * FROM  comprobantes where num_documento='$cod' and tipo_comprobante=1");
            return pg_query("SELECT * FROM  comprobantes where num_secuencial='$cod' and tipo_comprobante=1 and cod_punto_emision=1");
        }
    }

    function delete_pagos($pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_pagos_factura where com_id='$pag'");
        }
    }

    function lista_detalle_pagos($fact) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura where com_id='$fact' and pag_estado=0");
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////         
}

?>
