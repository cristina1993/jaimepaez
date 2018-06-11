<?php

include_once 'Conn.php';

class CuentasCobrar {

    var $con;

    function CuentasCobrar() {
        $this->con = new Conn();
    }

    function lista_asientos_contables() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14 and pln_id between 1033 and 1046
                    union SELECT * FROM erp_plan_cuentas where pln_id=878 
                    union SELECT * FROM erp_plan_cuentas where pln_id=887
                    union SELECT * FROM erp_plan_cuentas where pln_id=889
                    union SELECT * FROM erp_plan_cuentas where pln_id=890 order by pln_codigo");
        }
    }

    function lista_cuentas_bancos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14");
        }
    }

    function insert_ctasxcobrar($data, $pln) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar(com_id, cta_fecha, cta_monto, cta_forma_pago, cta_banco,pln_id,cta_fecha_pago,pag_id,num_documento,cta_concepto,asiento)
        VALUES ($data[0],'$data[1]','$data[2]','$data[3]','$data[4]',$pln,'$data[6]',$data[7],'$data[8]','$data[11]','$data[12]')");
        }
    }

    function suma_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(cta_monto) as cred FROM  erp_ctasxcobrar where pag_id=$id");
        }
    }

    function buscar_una_factura($id, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos WHERE reg_num_documento='$id' and reg_ruc_cliente='$ruc'");
        }
    }

    function lista_pago_vencer($id, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE pag_id=$id and com_id='$num'");
        }
    }

    function suma_pagos_documentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id) as monto,
                                    (select sum(pag_valor) from erp_pagos_documentos where reg_id=$id) as pago");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_identificacion='$id'");
        }
    }

    function lista_emisor_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function lista_documentos_grup($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select fac_nombre from erp_factura c $txt group by fac_nombre order by fac_nombre");
        }
    }

    function buscar_un_pago_fac($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura p WHERE com_id='$id' and not exists(SELECT * FROM erp_ctasxcobrar c where c.pag_id=p.pag_id) order by p.pag_id");
        }
    }

    //////nuevas tablas///
    function lista_documentos_buscador($nm) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura c $nm order by c.fac_nombre,c.fac_identificacion,c.fac_numero");
        }
    }

    function lista_documentos_ctas($nm) {
        if ($this->con->Conectar() == true) {
            return pg_query("select f.fac_identificacion, f.fac_nombre, c.pln_id from erp_factura f, erp_ctasxcobrar c where c.com_id=f.fac_id  $nm group by f.fac_nombre,f.fac_identificacion,c.pln_id
                     union
                     select f.fac_identificacion, f.fac_nombre,'0' as pln_id from erp_factura f where not exists(select * from erp_ctasxcobrar c where c.com_id=f.fac_id) $nm group by f.fac_nombre,f.fac_identificacion order by fac_nombre,pln_id desc ");
        }
    }

    function buscar_documentos_vencer($act, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM  erp_factura c, erp_pagos_factura p where c.fac_id=p.com_id  and p.pag_fecha_v > '$act' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and c.fac_fecha_emision between '$fec1' and '$fec2' and (c.fac_total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_estado='0') or not exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_estado='0'))group by c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id order by c.fac_nombre,c.fac_identificacion,c.fac_numero");
        }
    }

    function buscar_documentos_vencidos($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM  erp_factura c, erp_pagos_factura p where c.fac_id=p.com_id  and p.pag_fecha_v < '$act' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and c.fac_fecha_emision between '$fec1' and '$fec2' $txt and (c.fac_total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_forma_pago <>'NOTA DE DEBITO') or not exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id ) or exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_forma_pago ='NOTA DE DEBITO')) group by c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id order by c.fac_nombre,c.fac_identificacion,c.fac_numero");
        }
    }

    function suma_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(cta_monto) from erp_ctasxcobrar where com_id=$id and cta_forma_pago<>'NOTA DE DEBITO') as monto,
                                    (select sum(pag_cant) from erp_pagos_factura where com_id='$id') as pago,
                                    (select sum(cta_monto) from erp_ctasxcobrar where com_id=$id and cta_forma_pago='NOTA DE DEBITO') as debito");
        }
    }

    function suma_pagos1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(cta_monto) from erp_ctasxcobrar where com_id=$id and cta_forma_pago<>'NOTA DE DEBITO') as monto,
                                    (select fac_total_valor from erp_factura where fac_id='$id') as pago,
                                    (select sum(cta_monto) from erp_ctasxcobrar where com_id=$id and cta_forma_pago='NOTA DE DEBITO') as debito");
        }
    }

    function lista_pagos($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id' order by pag_id $txt");
        }
    }

    function lista_ultimo_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id' order by pag_fecha_v desc limit 1");
        }
    }

    function listar_una_cta_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxcobrar where pag_id=$id");
        }
    }

    function lista_documentos_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura WHERE fac_id=$id");
        }
    }

    function lista_cliente_ced($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE (cli_ced_ruc='$id' or cli_codigo='$id') and (cli_tipo = '0' or cli_tipo = '2')");
        }
    }

    function listar_una_cta_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxcobrar where com_id=$id");
        }
    }

    function listar_una_cuenta_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_id=$id");
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

    function listar_un_asiento($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_codigo='$cod'");
        }
    }

    function buscar_un_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE p.reg_id=$id and not exists(SELECT * FROM erp_ctasxpagar c where c.pag_id=p.pag_id)");
        }
    }

    function insert_ctasxpagar($data, $pln, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxpagar(reg_id, ctp_fecha, ctp_monto, ctp_forma_pago, ctp_banco,pln_id,ctp_fecha_pago,pag_id,num_documento,ctp_concepto,asiento)
        VALUES ($data[9],'$data[1]','$data[2]','$data[3]','$data[4]',$pln,'$data[6]',$pag,'$data[10]','$data[11]','$data[12]')");
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
            return pg_query("insert into erp_asientos_contables(con_asiento, con_concepto, con_documento, con_fecha_emision,con_concepto_debe, con_concepto_haber, con_valor_debe, con_valor_haber,con_estado)
        VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]')");
        }
    }

    function lista_un_cheque($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques where chq_id=$id and chq_estado<>2");
        }
    }

    function updte_cheque($id, $mto) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques set chq_cobro='$mto' where chq_id=$id");
        }
    }

    function lista_docs($id, $doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques h, erp_i_cliente c where h.cli_id=c.cli_id and c.cli_ced_ruc='$id' and h.chq_tipo_doc $doc and h.chq_estado<>2");
        }
    }

    function suma_pag_doc_nombre($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("select (SELECT sum(c.fac_total_valor) FROM erp_factura c where c.fac_identificacion='$id' and(c.emi_id=1 or c.emi_id=10)and exists (select * from erp_pagos_factura p where p.com_id=cast(c.fac_id as varchar)) and not exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id)) as pago, 
//(SELECT sum(cta_monto)FROM erp_factura c, erp_ctasxcobrar cta where c.fac_id=cta.com_id and c.fac_identificacion='$id' and(c.emi_id=1 or c.emi_id=10) and not exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id)) as monto");
            return pg_query("select (SELECT sum(c.fac_total_valor) FROM erp_factura c where c.fac_identificacion='$id' and exists (select * from erp_pagos_factura p where p.com_id=cast(c.fac_id as varchar)) and not exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id)) as pago, 
(SELECT sum(cta_monto)FROM erp_factura c, erp_ctasxcobrar cta where c.fac_id=cta.com_id and c.fac_identificacion='$id' and not exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id)) as monto");
        }
    }

    function suma_pag_doc_nombre1($id, $cod) {
        if ($this->con->Conectar() == true) {
//            return pg_query("select (SELECT sum(c.fac_total_valor) FROM erp_factura c where c.fac_identificacion='$id' and(emi_id=1 or emi_id=10)and exists (select * from erp_pagos_factura p where p.com_id=cast(c.fac_id as varchar)) and exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id and cta.pln_id='$cod')) as pago, 
//(SELECT sum(cta_monto)FROM erp_factura c, erp_ctasxcobrar cta where c.fac_id=cta.com_id and cta.pln_id='$cod' and c.fac_identificacion='$id' and(c.emi_id=1 or c.emi_id=10)) as monto");
            return pg_query("select (SELECT sum(c.fac_total_valor) FROM erp_factura c where c.fac_identificacion='$id' and exists (select * from erp_pagos_factura p where p.com_id=cast(c.fac_id as varchar)) and exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id and cta.pln_id='$cod')) as pago, 
(SELECT sum(cta_monto)FROM erp_factura c, erp_ctasxcobrar cta where c.fac_id=cta.com_id and cta.pln_id='$cod' and c.fac_identificacion='$id') as monto");
        }
    }

    function suma_documentos_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (SELECT sum(c.fac_total_valor) FROM erp_factura c where c.fac_identificacion='$id' and exists (select * from erp_pagos_factura p where p.com_id=c.fac_id) and not exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id and cta.cta_estado='0')) as val_factura, 
                                     (SELECT sum(c.fac_total_valor) FROM erp_factura c where c.fac_identificacion='$id' and exists (select * from erp_pagos_factura p where p.com_id=c.fac_id ) and exists(select * from erp_ctasxcobrar cta where cta.com_id=c.fac_id and cta.cta_estado='0')) as val_fact_ctas, 
                                     (SELECT sum(cta_monto)FROM erp_factura c, erp_ctasxcobrar cta where c.fac_id=cta.com_id and c.fac_identificacion='$id' and cta.cta_estado='0' and cta_forma_pago<> 'NOTA DE DEBITO') as ctas_credito,
                                     (SELECT sum(cta_monto)FROM erp_factura c, erp_ctasxcobrar cta where c.fac_id=cta.com_id and c.fac_identificacion='$id' and cta.cta_estado='0' and cta_forma_pago= 'NOTA DE DEBITO') as ctas_debito");
        }
    }

    function lista_estado_cuenta_cliente($txt, $f1, $f2) {
        if ($this->con->Conectar() == true) {
            return pg_query("select '0' as cta_id, c.fac_id,c.fac_fecha_emision, c.fac_numero,('FACTURACION VENTA') as concepto,('') as forma,c.fac_total_valor as total_valor,('0') as haber from erp_factura c where c.fac_identificacion='$txt' and exists(select * from erp_pagos_factura p where p.com_id=c.fac_id) and c.fac_fecha_emision between '$f1' and '$f2' 
                            union 
                            select cta.cta_id,c.fac_id,cta.cta_fecha_pago, c.fac_numero,cta.cta_concepto,cta_forma_pago,('0') as total_valor ,cta.cta_monto from erp_ctasxcobrar cta,erp_factura c where c.fac_id=cta.com_id  and c.fac_identificacion='$txt' and cta.cta_fecha_pago between '$f1' and '$f2' order by fac_numero, fac_fecha_emision,cta_id
                     ");
        }
    }

    function saldo_anterior($txt, $fec1) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(select sum(c.fac_total_valor) from erp_factura c where (c.emi_id=1 or c.emi_id=10) and c.fac_identificacion='$txt' and exists(select * from erp_pagos_factura p where p.com_id= cast(c.fac_id as varchar)) and c.fac_fecha_emision <'$fec1') as debito, 
                                    (select sum (cta.cta_monto) from erp_ctasxcobrar cta,erp_factura c  where  cta.com_id=c.fac_id and c.fac_identificacion='$txt' and cta.cta_fecha_pago <'$fec1' and cta.cta_forma_pago<>'NOTA DE DEBITO') as credito,
                                    (select sum (cta.cta_monto) from erp_ctasxcobrar cta,erp_factura c  where  cta.com_id=c.fac_id and c.fac_identificacion='$txt' and cta.cta_fecha_pago <'$fec1' and cta.cta_forma_pago='NOTA DE DEBITO') as debito1");
        }
    }

    function buscar_documentos_vencidos_xvencer($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM  erp_factura c, erp_pagos_factura p where c.fac_id =p.com_id  and p.pag_fecha_v > '$act' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and c.fac_fecha_emision between '$fec1' and '$fec2' and (c.fac_total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id ) or not exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id ))group by c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id) 
                              union
                             SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM  erp_factura c, erp_pagos_factura p where c.fac_id=p.com_id  and p.pag_fecha_v < '$act' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and c.fac_fecha_emision between '$fec1' and '$fec2' $txt and (c.fac_total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_forma_pago <>'NOTA DE DEBITO') or not exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id ) or exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_forma_pago ='NOTA DE DEBITO')) group by c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id order by fac_nombre,fac_identificacion,fac_numero");
        }
    }

    function buscar_documentos_pagados_vencidos($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM erp_factura c WHERE fac_fecha_emision between '$fec1' and '$fec2' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and fac_total_valor+(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago='NOTA DE DEBITO')=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO') or fac_total_valor=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO'))
                              union
                             (SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM  erp_factura c, erp_pagos_factura p where c.fac_id=p.com_id  and p.pag_fecha_v < '$act'  and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull) and c.fac_fecha_emision between '$fec1' and '$fec2' and (c.fac_total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and ct.cta_forma_pago <>'NOTA DE DEBITO') or not exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id )) group by c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id) order by fac_nombre,fac_identificacion, fac_numero");
        }
    }

    function buscar_documentos_pagados_xvencer($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM erp_factura c WHERE fac_fecha_emision between '$fec1' and '$fec2' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and fac_total_valor+(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago='NOTA DE DEBITO')=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO') or fac_total_valor=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO'))
                              union
                             (SELECT c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero,c.cli_id FROM  erp_factura c, erp_pagos_factura p where c.fac_id=p.com_id  and p.pag_fecha_v > '$act' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull) and c.fac_fecha_emision between '$fec1' and '$fec2' and (c.fac_total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id ) or not exists(select * from erp_ctasxcobrar ct where c.fac_id=ct.com_id ))group by c.fac_id,c.fac_nombre,c.fac_identificacion,c.fac_fecha_emision, c.fac_total_valor,c.fac_numero) order by fac_nombre,fac_identificacion, fac_numero,c.cli_id");
        }
    }

    function lista_codigo_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_factura f, erp_ctasxcobrar c where c.com_id=f.fac_id  and fac_identificacion='$id'");
        }
    }
    
    function lista_pagos_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM pagos WHERE cli_id=$id ORDER BY pag_fecha_v DESC");
        }
    }
    
    function lista_pagos_vencidos($id, $fini, $ffin) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from pagos where cli_id=$id and pag_fecha_v between '$fini' and '$ffin'");
        }
    }
    
    function lista_pagos_vencidos_m120($id, $fec){
        if ($this->con->Conectar() == true) {
            return pg_query("select * from pagos where cli_id=$id and pag_fecha_v<'$fec'");
        }
    }
    
    function lista_cliente_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_codigo, cli_raz_social, cli_ced_ruc, cli_calle_prin, cli_telefono FROM erp_i_cliente WHERE cli_id=$id");
        }
    }
    
    function lista_pagos_vencidost($id, $fini, $ffin) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id and pag_fecha_v between '$fini' and '$ffin'");
        }
    }
    
    function lista_pagos_vencidos_m120t($id, $fec){
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id and pag_fecha_v<'$fec'");
        }
    }
    
    function lista_pag_porvencert($id, $fec){
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id and pag_fecha_v>'$fec'");
        }
    }
    
    function lista_pag_porvencer($id, $fec){
        if ($this->con->Conectar() == true) {
            return pg_query("select * from pagos where cli_id=$id and pag_fecha_v>'$fec' ORDER BY pag_fecha_v ASC");
        }
    }
    
    function lista_cheques_cliente($id){
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE cli_id=$id and chq_tipo_doc>=1 and chq_tipo_doc<=2 and chq_estado<2 ORDER BY chq_fecha ASC");
        }
    }
    
    function lista_totales_pagos($id){
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(cta_monto) as total FROM  erp_ctasxcobrar WHERE com_id=$id");
        }
    }
    
    function lista_cliente_cedula($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id FROM erp_i_cliente WHERE cli_ced_ruc='$id'");
        }
    }
    
    function lista_pagos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM pagos WHERE cli_id=$id");
        }
    }
    
    function lista_pagos_factu($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM pagos WHERE fac_id=$id");
        }
    }
    
}

?>
