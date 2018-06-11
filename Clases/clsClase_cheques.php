<?php

include_once 'Conn.php';

class Clase_cheques {

    var $con;

    function Clase_cheques() {
        $this->con = new Conn();
    }

    function lista_clientes_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%' 
                            
                            Order by cli_raz_social");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

    function insert_cheque($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_cheques(
                                                    cli_id, 
                                                    chq_nombre, 
                                                    chq_banco, 
                                                    chq_numero, 
                                                    chq_recepcion,
                                                    chq_fecha, 
                                                    chq_monto, 
                                                    chq_tipo_doc,
                                                    chq_cuenta,
                                                    pln_id,
                                                    chq_concepto
                                                    )
                                            VALUES (
                                                    $data[0],
                                                    '$data[1]',
                                                    '$data[2]',
                                                    '$data[3]',
                                                    '$data[4]',
                                                    '$data[5]',
                                                    '$data[6]',
                                                    '$data[7]',
                                                    '$data[8]',
                                                    '$data[9]',
                                                    '$data[10]')");
        }
    }

    function lista_buscador_cheques($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id $txt order by chq_recepcion desc");
        }
    }

    function lista_un_cheque($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id and h.chq_id='$id'");
        }
    }

    function upd_estado($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques $txt WHERE chq_id=$id");
        }
    }

    function upd_cheques($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques SET 
                                                    cli_id=$data[0], 
                                                    chq_nombre='$data[1]', 
                                                    chq_banco='$data[2]', 
                                                    chq_numero='$data[3]', 
                                                    chq_recepcion='$data[4]',
                                                    chq_fecha='$data[5]', 
                                                    chq_monto='$data[6]', 
                                                    chq_tipo_doc='$data[7]',
                                                    chq_cuenta='$data[8]',
                                                    pln_id='$data[9]',
                                                    chq_concepto='$data[10]'
                                                    WHERE chq_id='$id'");
        }
    }

    function delete_cheque($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_cheques WHERE chq_id='$id'"
            );
        }
    }

    function delete_asientos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_cod='$txt'"
            );
        }
    }

    function delete_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_ctasxcobrar WHERE chq_id='$id'"
            );
        }
    }

    function lista_plan_cuentas() {
        if (
                $this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas p, erp_bancos_y_cajas where p.pln_id=byc_id_cuenta and p.pln_estado='0' ORDER BY p.pln_codigo");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas p, erp_bancos_y_cajas where p.pln_id=byc_id_cuenta  and  p.pln_id = $id and p.pln_estado='0'");
        }
    }

    function lista_ultimo_cheque($c, $b, $n) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_cheques where cli_id=$c and chq_banco=$b and chq_numero=$n");
        }
    }

    //////////////////////control de cobros////////////////////

    function lista_factras_clientes($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  f.* , (select sum(c.cta_monto) from erp_ctasxcobrar c where c.com_id=f.fac_id and c.cta_forma_pago='NOTA DE DEBITO') as debito, 
                            (select sum(c.cta_monto) from erp_ctasxcobrar c where c.com_id=f.fac_id and c.cta_forma_pago<>'NOTA DE DEBITO') as monto FROM erp_factura f 
                            where f.fac_identificacion='$id' order by fac_numero"
            );
        }
    }

    function lista_un_cliente_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_id='$id'"
            );
        }
    }

    function suma_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(select sum(h.chq_monto) from erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id and c.cli_ced_ruc='$id' and h.chq_tipo_doc<>4 and h.chq_estado<>2) as sum1, 
                        (select sum(h.chq_monto) from erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id and c.cli_ced_ruc='$id' and h.chq_tipo_doc=4 and h.chq_estado<>2) as sum2,
                        (select sum(h.chq_cobro) from erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id and c.cli_ced_ruc='$id' and h.chq_tipo_doc<>4 and h.chq_estado<>2) as sum3,
                        (select sum(h.chq_cobro) from erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id and c.cli_ced_ruc='$id' and h.chq_tipo_doc=4 and h.chq_estado<>2) as sum4");
        }
    }

    function suma_pagos($id, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(cta_monto) from erp_ctasxcobrar where com_id=$id and cta_forma_pago<>'NOTA DE DEBITO') as monto,
                                    (select sum(pag_valor) from erp_pagos_factura where com_id='$id') as pago,
                                    (select sum(cta_monto) from erp_ctasxcobrar where com_id=$id and cta_forma_pago='NOTA DE DEBITO') as debito");
        }
    }

    function lista_fecha_vence($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id' order by pag_fecha_v desc limit 1");
        }
    }

    function listar_una_cta_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxcobrar where pag_id=$id");
        }
    }

    function listar_una_ctapagar_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where reg_id=$id");
        }
    }

    function insert_ctasxcobrar($data) {
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
                                                         asiento,
                                                         chq_id)
                                                 VALUES ($data[0],
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
                                                         '$data[11]'
                                                         )");
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
                $n_sec = 'AS'.substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into erp_asientos_contables(
                                                                con_asiento,
                                                                con_concepto, 
                                                                con_documento, 
                                                                con_fecha_emision,
                                                                con_concepto_debe, 
                                                                con_concepto_haber, 
                                                                con_valor_debe, 
                                                                con_valor_haber,
                                                                con_estado)
                                                        VALUES ('$data[0]',
                                                                '$data[1]',
                                                                '$data[2]',
                                                                '$data[3]',
                                                                '$data[4]',
                                                                '$data[5]',
                                                                '$data[6]',
                                                                '$data[7]',
                                                                '$data[8]')");
        }
    }

    function updte_cheque($id, $mto) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques set chq_cobro='$mto' where chq_id=$id");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id'");
        }
    }

    function listar_una_cta_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxcobrar where com_id=$id");
        }
    }

    function lista_pagos($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id' order by pag_id $txt");
        }
    }

    function buscar_un_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura p WHERE p.com_id='$id' and not exists(SELECT * FROM erp_ctasxcobrar c where c.pag_id=p.pag_id)");
        }
    }
    
     function lista_cuentasxcobrar_chq($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_ctasxcobrar ct, erp_factura f where ct.com_id=f.fac_id and ct.chq_id=$id order by f.fac_numero");
        }
    }
    
    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor WHERE identificacion='$id'");
        }
    }
    
    
    function lista_una_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas p, erp_bancos_y_cajas where p.pln_id=byc_id_cuenta  and  p.pln_codigo ='$id' and p.pln_estado='0'");
        }
    }
    
    function lista_una_cuenta_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_id='$id'");
        }
    }
    
     function suma_cheque_cuentas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(cta_monto) from erp_ctasxcobrar where chq_id=$id");
        }
    }

    function lista_plan_cuentas_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas p, erp_bancos_y_cajas where p.pln_id=byc_id_cuenta  and  p.pln_codigo = '$id' and p.pln_estado='0'");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
