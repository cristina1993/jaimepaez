<?php

include_once 'Conn.php';

class Clase_asientos_automaticos {

    var $con;

    function Clase_asientos_automaticos() {
        $this->con = new Conn();
    }

    function lista_facturas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura order by num_documento asc");
        }
    }

    function lista_asientos($doc, $con) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where con_documento='$doc' and con_concepto='$con'");
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_asientos_contables(
                con_asiento,
                con_concepto,
                con_documento,
                con_fecha_emision,
                con_concepto_debe,
                con_concepto_haber,
                con_valor_debe,
                con_valor_haber,
                con_estado,
                mod_id,
                doc_id,
                cli_id
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',1,'$data[8]','$data[9]','$data[10]')");
        }
    }

    function lista_plan($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_id=$id");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function siguiente_asiento() {
        if ($this->con->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asiento());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = substr($rst[con_asiento], 0, (12 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
            print_r($n_sec);
        }
    }

    ////AUMENTA DESCUENTO 14%
    function lista_suma_descuentos_factura($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='0') as desc0,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='12') as desc12,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='14') as desc14,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='EX') as descex,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='NO') as descno");
        }
    }

    function lista_notas_credito() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 order by num_documento asc");
        }
    }

    function lista_suma_descuentos_nota_cred($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='0') as desc0,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='12') as desc12,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='EX') as descex,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='NO') as descno");
        }
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_det_fac($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where nfac_id='$num'");
        }
    }

//    function lista_facturas_documento($doc) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and num_documento='$doc' order by num_documento asc");
//        }
//    }
    
     function lista_facturas_documento($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_id=$doc");
        }
    }

//    function lista_notacre_documento($doc) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 and num_documento='$doc' order by num_documento asc");
//        }
//    }
    
    function lista_notacre_documento($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_id='$doc'");
        }
    }

    function delete_asientos($doc,$con) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_asientos_contables where con_documento='$doc' and con_concepto='$con'");
        }
    }
    
    
    function lista_pagos($doc,$con) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pagos_factura p, comprobantes f where p.com_id=f.num_documento and p.pag_forma='9'");
        }
    }
    
    function update_pagos($id,$doc,$fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_pagos_factura set pag_fecha_v='$fec' where pag_id=$id and com_id='$doc' and pag_forma='9'");
        }
    }
    
    function lista_configuraciones_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=$id");
        }
    }
    
     function lista_emisor_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor where emi_id=$id");
        }
    }
    
    
    function lista_suma_fletes_factura($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(dfc_val_descuento) as desc, sum(dfc_precio_total) as tot FROM  erp_det_factura where fac_id=$doc and dfc_descripcion like '%FLETE%')");
        }
    }
    
    function lista_asientos_ctas($id,$ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.emi_id='$id' and a.cas_orden_emi='$ord' and c.pln_estado=0");
        }
    }
    
     function lista_cliente_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_id='$id'");
        }
    }
    
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
