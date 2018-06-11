<?php

include_once 'Conn.php';

class CuentasPagar {

    var $con;

    function CuentasPagar() {
        $this->con = new Conn();
    }

    function lista_documentos_buscador($nm) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos c, erp_i_cliente cl where c.cli_id=cl.cli_id $nm order by reg_ruc_cliente,reg_num_documento");
        }
    }

    function lista_cliente_ruc($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_ced_ruc='$ruc' ");
        }
    }

    function listar_una_ctapagar_ctpid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where ctp_id=$id");
        }
    }

    function listar_una_ctapagar_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where pag_id=$id");
        }
    }

    function lista_documentos_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos d, erp_i_cliente c WHERE c.cli_id=d.cli_id and d.reg_id=$id");
        }
    }

    function lista_cliente_ced($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_i_cliente WHERE (cli_ced_ruc='$id' or cli_codigo='$id') and (cli_tipo = '1' or cli_tipo = '2')");
            return pg_query("SELECT * FROM erp_i_cliente WHERE (cli_ced_ruc='$id' or cli_codigo='$id')");
        }
    }

    function lista_asientos_contables() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas order by pln_codigo");
//            return pg_query("SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14 and pln_id between 1054 and 1060
//                    union SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14 and pln_id between 1172 and 1193 order by pln_codigo");
        }
    }

    function lista_cuentas_bancos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas order by pln_codigo");
//                SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14 and pln_id between 836 and 869 
//                union 
//                select * FROM erp_plan_cuentas where pln_id between 1104 and 1105 
//                union 
//                select * FROM erp_plan_cuentas where pln_id=1899 
//                union 
//                select * FROM erp_plan_cuentas where pln_id between 992 and 993 order by pln_codigo");
        }
    }

    function listar_un_asiento($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where trim(pln_codigo)=trim('$cod')");
        }
    }

    function listar_una_cuenta_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_id=$id");
        }
    }

    function lista_secuencial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where ctp_forma_pago!='RETENCION' and ctp_forma_pago!='NOTA DE CREDITO' and char_length(ctp_secuencial)>0 order by cast(replace(ctp_secuencial,'E','') as integer) desc limit 1");
        }
    }

    function insert_ctasxpagar($data, $pln) {
        if ($this->con->Conectar() == true) {
            $rst_sec = pg_fetch_array($this->lista_secuencial());
            if (empty($rst_sec)) {
                $sec = 1;
            } else {
                $sec = str_replace('E', '', $rst_sec[ctp_secuencial]) + 1;
            }
            if ($sec >= 0 && $sec < 10) {
                $tx = '000000000';
            } else if ($sec >= 10 && $sec < 100) {
                $tx = '00000000';
            } else if ($sec >= 100 && $sec < 1000) {
                $tx = '0000000';
            } else if ($sec >= 1000 && $sec < 10000) {
                $tx = '000000';
            } else if ($sec >= 10000 && $sec < 100000) {
                $tx = '00000';
            } else if ($sec >= 100000 && $sec < 1000000) {
                $tx = '0000';
            } else if ($sec >= 1000000 && $sec < 10000000) {
                $tx = '000';
            } else if ($sec >= 10000000 && $sec < 100000000) {
                $tx = '00';
            } else if ($sec >= 100000000 && $sec < 1000000000) {
                $tx = '0';
            } else if ($sec >= 1000000000 && $sec < 10000000000) {
                $tx = '';
            }
            $secuencial = 'E' . $tx . $sec;
            
            return pg_query("
                INSERT INTO erp_ctasxpagar(
                reg_id, 
                ctp_fecha, 
                ctp_monto, 
                ctp_forma_pago, 
                ctp_banco,
                pln_id,
                ctp_fecha_pago,
                pag_id,
                num_documento,
                ctp_concepto,
                asiento,
                ctp_secuencial)
        VALUES ( $data[0],
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',
                 $pln,
                '$data[6]',
                 $data[7],
                '$data[8]',
                '$data[11]',
                '$data[12]',
                '$secuencial')");
        }
    }

    function insert_ctasxcobrar($data, $pln, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar(com_id, cta_fecha, cta_monto, cta_forma_pago, cta_banco,pln_id,cta_fecha_pago,pag_id,num_documento,cta_concepto,asiento)
        VALUES ($data[10],'$data[1]','$data[2]','$data[3]','$data[4]',$pln,'$data[6]',$pag,'$data[9]','$data[11]','$data[12]')");
        }
    }

    function suma_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id) as monto,
                                    (select sum(pag_valor) from erp_pagos_documentos where reg_id=$id) as pago");
        }
    }

    function suma_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(ctp_monto) as cred FROM  erp_ctasxpagar where pag_id=$id");
        }
    }

    function buscar_un_documento($id, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura WHERE fac_numero='$id' and fac_identificacion='$ruc'");
        }
    }

    function buscar_documentos_vencidos($act, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento FROM  comprobantes c, erp_pagos_factura p where c.num_documento=p.com_id  and c.tipo_comprobante=1 and(cod_punto_emision=1 or cod_punto_emision=10) and p.pag_fecha_v < '$act' and c.fecha_emision between $fec1 and $fec2 and (c.total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.com_id=ct.com_id ) or not exists(select * from erp_ctasxcobrar ct where c.com_id=ct.com_id )) group by c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento order by c.num_documento");
        }
    }

    function buscar_documentos_vencer($act, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento FROM  comprobantes c, erp_pagos_factura p where c.num_documento=p.com_id  and c.tipo_comprobante=1 and(cod_punto_emision=1 or cod_punto_emision=10) and p.pag_fecha_v > '$act' and c.fecha_emision between $fec1 and $fec2 and (c.total_valor>(select sum(ct.cta_monto) from erp_ctasxcobrar ct where c.com_id=ct.com_id ) or not exists(select * from erp_ctasxcobrar ct where c.com_id=ct.com_id ))group by c.com_id,c.nombre,c.identificacion,c.fecha_emision, c.total_valor,c.num_documento order by c.num_documento ");
        }
    }

    function lista_pago_vencer($id, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_DOCUMENTOS WHERE pag_id=$id and reg_id=$num");
        }
    }

    function buscar_un_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura p WHERE p.com_id='$id' and not exists(SELECT * FROM erp_ctasxcobrar c where c.pag_id=p.pag_id)");
        }
    }

    function insert_asientosp($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into 
                erp_asientos_contables
                (con_asiento, 
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
                '$data[11]')");
        }
    }

    function ultimo_asientop() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function siguiente_asientop() {
        if ($this->con->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asientop());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = 'AS' . substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function buscar_un_pago_doc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE reg_id='$id' and not exists(SELECT * FROM erp_ctasxpagar c where c.pag_id=p.pag_id) order by p.pag_id");
        }
    }

    function lista_estado_cuenta_cliente($txt, $f1, $f2) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                            select '0' as ctp_id, c.reg_id,c.reg_femision, c.reg_num_documento,('FACTURACION COMPRA') as concepto,('') as forma,c.reg_total as total_valor,('0') as haber from erp_reg_documentos c where c.reg_ruc_cliente='$txt' and exists(select * from erp_pagos_documentos p where p.reg_id=c.reg_id) and c.reg_femision between '$f1' and '$f2' 
                            union 
                            select cta.ctp_id,c.reg_id,cta.ctp_fecha_pago, c.reg_num_documento,cta.ctp_concepto,ctp_forma_pago,('0') as total_valor ,cta.ctp_monto from erp_ctasxpagar cta,erp_reg_documentos c where c.reg_id=cta.reg_id  and c.reg_ruc_cliente='$txt' and cta.ctp_fecha_pago between '$f1' and '$f2' order by reg_num_documento, reg_femision,ctp_id");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where identificacion='$id'");
        }
    }

    function saldo_anterior($txt, $fec1) {
        if ($this->con->Conectar() == true) {
            $f1 = str_replace('-', '', $fec1);
            return pg_query("select(select sum(c.reg_total) from erp_reg_documentos c where c.reg_ruc_cliente='$txt' and exists(select * from erp_pagos_documentos p where p.reg_id= c.reg_id) and c.reg_femision <'$fec1') as credito, 
                                    (select sum (ctp.ctp_monto) from erp_ctasxpagar ctp, erp_reg_documentos c where ctp.reg_id=c.reg_id and c.reg_ruc_cliente='$txt'  and ctp.ctp_fecha_pago <'$fec1') as debito");
        }
    }

    function listar_una_ctapagar_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where reg_id=$id and ctp_estado=0");
        }
    }

    function lista_pagos_regfac($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos WHERE reg_id='$id' order by pag_id $txt");
        }
    }

//////////********PAGO PROVEEDORES***********/////////

    function lista_pagos_por_vencer($today, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos rd,
                             erp_pagos_documentos pd
                             where pd.reg_id=rd.reg_id
                             and pd.pag_fecha_v >='$today' 
                             and rd.reg_ruc_cliente='$ruc' ");
        }
    }

/////// cambios para notas de debito//////
    function lista_pagos_documento($reg_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(ctp_monto) from erp_ctasxpagar where reg_id=$reg_id and ctp_forma_pago<>'NOTA DE DEBITO' and ctp_estado=0");
        }
    }

    function lista_pagos_ndebito($reg_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(ctp_monto) as debito from erp_ctasxpagar where reg_id=$reg_id and ctp_forma_pago='NOTA DE DEBITO' and ctp_estado=0");
        }
    }

    function lista_pagos_vencidos($today, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos rd,
                             erp_pagos_documentos pd
                             where pd.reg_id=rd.reg_id
                             and pd.pag_fecha_v <'$today'
                             and rd.reg_ruc_cliente='$ruc'    ");
        }
    }

    function lista_proveedores() {
        if ($this->con->Conectar() == true) {
            return pg_query("select cl.cli_ced_ruc,cl.cli_raz_social from 
erp_reg_documentos doc,
erp_i_cliente cl
where cl.cli_ced_ruc=doc.reg_ruc_cliente
group by cl.cli_ced_ruc,cl.cli_raz_social order by cl.cli_raz_social ");
        }
    }

    function lista_secuencial_obligaciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_obligacion_pago order by obl_codigo desc limit 1");
        }
    }

    function lista_obligaciones_reg_id($reg_id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_asientos_contables ac,
erp_plan_cuentas pc
where pd.pag_id=op.pag_id
and ac.doc_id=pd.pag_id
and pc.pln_codigo=op.obl_cuenta
and op.obl_codigo='$cod'
and pd.reg_id=$reg_id");
        }
    }

    function inser_pago_obligaciones($dat) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_obligacion_pago(
            pag_id,
            obl_codigo,
            obl_cantidad,
            obl_estado
            )
    VALUES ($dat[0],
           '$dat[1]',
            $dat[2],
            $dat[3] ) ");
        }
    }

//    function lista_obligaciones_pago($ruc) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM 
//                                erp_obligacion_pago op,
//                                erp_pagos_documentos pd,
//                                erp_reg_documentos rg
//                                where pd.pag_id=op.pag_id
//                                and rg.reg_id=pd.reg_id
//                                and op.obl_estado_obligacion<>3
//                                and rg.reg_ruc_cliente like '%$ruc%'
//                                    order by op.obl_codigo
//                                ");
//        }
//    }

    function lista_obligaciones_pago($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT op.*,rg.reg_ruc_cliente,rg.reg_num_documento,rg.reg_concepto,rg.reg_femision,rg.reg_id,pd.pag_valor,pd.pag_fecha_v FROM 
                                erp_obligacion_pago op,
                                erp_pagos_documentos pd,
                                erp_reg_documentos rg
                                where pd.pag_id=op.pag_id
                                and rg.reg_id=pd.reg_id
                                and op.obl_estado_obligacion<>3
                                and rg.reg_ruc_cliente like '%$ruc%'
                                union 
                            SELECT op.*, c.cli_ced_ruc as reg_ruc_cliente, op.con_asiento as reg_num_documento, op.obl_concepto as reg_concepto, op.obl_fecha_pago as reg_femision,'0' as reg_id,obl_cantidad as pag_valor,op.obl_fecha_pago as pag_fecha_v FROM 
                                erp_obligacion_pago op,erp_i_cliente c
                                where op.cli_id=c.cli_id
                                and op.obl_estado_obligacion<>3
                                and c.cli_ced_ruc like '%$ruc%'
                                    order by obl_codigo
                                ");
        }
    }

//    function lista_pagos_aprobados() {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select op.obl_codigo,
//rd.reg_ruc_cliente,
//op.obl_estado_obligacion,
//op.obl_fecha_pago,
//op.obl_forma_pago,
//op.obl_concepto,
//op.obl_doc,
//op.obl_cuenta,
//sum(obl_cantidad) 
//from erp_obligacion_pago op,
//erp_pagos_documentos pd,
//erp_reg_documentos rd   
//where op.pag_id=pd.pag_id
//and pd.reg_id=rd.reg_id
//and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
//group by 
//op.obl_estado_obligacion,
//op.obl_fecha_pago,
//op.obl_codigo,
//rd.reg_ruc_cliente,
//op.obl_forma_pago,
//op.obl_concepto,
//op.obl_doc,
//op.obl_cuenta order by op.obl_codigo desc");
//        }
//    }

    function lista_pagos_aprobados() {
        if ($this->con->Conectar() == true) {
            return pg_query("select op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad),
op.obl_tipo,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd   
where op.pag_id=pd.pag_id
and pd.reg_id=rd.reg_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_tipo,
op.obl_num_egreso
UNION 
select op.obl_codigo,
c.cli_ced_ruc as reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad),
op.obl_tipo,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_i_cliente c
where op.cli_id = c.cli_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
c.cli_ced_ruc,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_tipo,
op.obl_num_egreso
order by obl_codigo desc ");
        }
    }

    function cambia_estado_obligaciones($sts, $id, $cnt, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_obligacion_pago SET obl_estado_obligacion=$sts ,obl_cantidad=$cnt,obl_num_egreso='$egr'  where obl_id=$id");
        }
    }

    function lista_estado_obligacion_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_obligacion_pago WHERE pag_id=$id and obl_estado_obligacion<>2"); ///Si esta registrado y no esta rechazado
        }
    }

    function lista_obl_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 
op.obl_codigo,
op.obl_estado,
op.obl_estado_obligacion,
pg.reg_id,
pg.pag_id,
pg.pag_fecha_v,
op.obl_cantidad,
rd.reg_num_documento 
FROM  
erp_obligacion_pago op,
erp_pagos_documentos pg,
erp_reg_documentos rd
where op.pag_id=pg.pag_id 
and rd.reg_id=pg.reg_id
and op.obl_codigo='$cod'
and op.obl_estado_obligacion='1'
"); ///Si esta registrado y no esta rechazado
        }
    }

    function cambia_estado_obligacion_pago($sts, $cod, $fecha_pag, $forma_pago, $conepto, $documento, $cuenta, $n_egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_obligacion_pago 
                SET obl_estado_obligacion=$sts,
                    obl_fecha_pago='$fecha_pag',
                    obl_forma_pago='$forma_pago',
                    obl_concepto='$conepto',
                    obl_doc='$documento',
                    obl_cuenta='$cuenta',
                    obl_num_egreso='$n_egr'    
                    where obl_codigo='$cod' and obl_estado_obligacion=1 ");
        }
    }

    function lista_obligacion_pago($cod, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago op,
                            erp_pagos_documentos pd
                            where pd.pag_id=op.pag_id
                            and op.obl_codigo='$cod'
                            and op.obl_fecha_pago='$fecha' ");
        }
    }

    function lista_asiento_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select *
from erp_asientos_contables ac,
erp_plan_cuentas pc
where ac.con_concepto_haber=pc.pln_codigo
and ac.con_asiento='$cod'
and ac.con_concepto_haber<>''  ");
        }
    }

    function lista_obligacion_pago_datos($cod, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(obl_cantidad),obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc from erp_obligacion_pago
                            where pag_id=pag_id
                            and obl_codigo=trim('$cod')
                            and obl_fecha_pago='$fecha'
                            and obl_estado_obligacion=3
                            group by obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc");
        }
    }

    function lista_debe_factura($reg_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(op.obl_cantidad) from erp_obligacion_pago op,
erp_pagos_documentos pd
where op.pag_id=pd.pag_id
and pd.reg_id=$reg_id");
        }
    }

//    function lista_asientos_obligacion_pago($cod) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select ac.con_concepto,
//ac.con_documento,
//ac.con_fecha_emision,
//ac.con_asiento,
//op.obl_codigo,
//rd.reg_ruc_cliente,
//ac.con_concepto_debe,
//ac.con_concepto_haber,
//ac.con_valor_debe,
//ac.con_valor_haber,
//pc.pln_descripcion,
//rd.con_asiento
//from 
//erp_obligacion_pago op,
//erp_asientos_contables ac,
//erp_plan_cuentas pc,
//erp_pagos_documentos pg,
//erp_reg_documentos rd
//where op.pag_id=ac.doc_id
//and trim(ac.con_concepto_debe)=trim(pc.pln_codigo)
//and op.obl_codigo='$cod'
//and op.obl_estado_obligacion=3
//and pg.pag_id=op.pag_id
//and rd.reg_id=pg.reg_id
//order by ac.con_fecha_emision,
//ac.con_documento
//");
//        }
//    }
    function lista_asientos_obligacion_pago($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
FROM erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd,
erp_asientos_contables ac
where pd.pag_id=op.pag_id
and rd.reg_id=pd.reg_id
and op.obl_codigo='$cod'
and op.obl_estado_obligacion=3
group by pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
");
        }
    }

    function insert_asientosp2($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into 
                erp_asientos_contables
                (con_asiento, 
                con_concepto, 
                con_documento, 
                con_fecha_emision,
                con_concepto_debe, 
                con_concepto_haber, 
                con_valor_debe, 
                con_valor_haber,
                con_estado,
                doc_id,
                cli_id
                )
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
                '$data[10]')");
        }
    }

    function lista_ultimo_num_egreso() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago where obl_num_egreso is not null order by obl_num_egreso desc limit 1");
        }
    }

    function lista_obligacion_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago where obl_codigo='$id'");
        }
    }

    function lista_un_asiento($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_debe as concepto, con_valor_debe as valor,con_estado,con_tipo,doc_id , '0'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_debe))<>0 and con_asiento='$as'
                             union 
                             SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_haber as concepto, con_valor_haber as valor,con_estado,con_tipo,doc_id , '1'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_haber))<>0 and con_asiento='$as'
                             order by con_id");
        }
    }

    function lista_un_plan_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_plan_cuentas where pln_codigo='$id' ");
        }
    }

    function delete_asientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_asiento='$id'");
        }
    }

    function delete_asientos_pagid($id, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_asiento='$id' and doc_id='$pag'");
        }
    }

    function delete_obligacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_obligacion_pago WHERE obl_codigo='$id'");
        }
    }

    function delete_obligacion_pagid($id, $pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_obligacion_pago WHERE obl_codigo='$id' and pag_id=$pag");
        }
    }

    function lista_un_asiento_pag_id($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables WHERE doc_id='$id' and con_concepto= 'CUENTAS X PAGAR' and con_fecha_emision='$fec'");
        }
    }

    function delete_ctasxpagar($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_ctasxpagar WHERE pag_id='$id' and ctp_fecha_pago='$fec'");
        }
    }

    function lista_una_obligacion_cod($id, $doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_obligacion_pago where obl_codigo='$id' and obl_doc='$doc' ");
        }
    }

    function lista_obligacion_pago_datos_egr($cod, $fecha, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(obl_cantidad),obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc from erp_obligacion_pago
                            where pag_id=pag_id
                            and obl_codigo=trim('$cod')
                            and obl_fecha_pago='$fecha'
                            and obl_num_egreso='$egr'
                            and obl_estado_obligacion=3
                            group by obl_codigo,obl_fecha_pago,obl_forma_pago,obl_concepto,obl_doc");
        }
    }

    function lista_asientos_obligacion_pago_egr($cod, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
FROM erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd,
erp_asientos_contables ac
where pd.pag_id=op.pag_id
and rd.reg_id=pd.reg_id
and op.obl_codigo='$cod' and op.obl_num_egreso='$egr' 
and op.obl_estado_obligacion=3
group by pd.reg_id,
rd.reg_num_documento,
rd.con_asiento,
rd.reg_ruc_cliente
");
        }
    }

    function lista_obligaciones_reg_egr($cod, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("select op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
sum(obl_cantidad) ,
op.obl_num_egreso
from erp_obligacion_pago op,
erp_pagos_documentos pd,
erp_reg_documentos rd   
where op.pag_id=pd.pag_id
and pd.reg_id=rd.reg_id
and (op.obl_estado_obligacion=1 or op.obl_estado_obligacion=3)
and op.obl_codigo='$cod' and op.obl_num_egreso='$egr'
group by 
op.obl_estado_obligacion,
op.obl_fecha_pago,
op.obl_codigo,
rd.reg_ruc_cliente,
op.obl_forma_pago,
op.obl_concepto,
op.obl_doc,
op.obl_cuenta,
op.obl_num_egreso
order by op.obl_codigo desc ");
        }
    }

    function lista_debe_factura_egr($reg_id, $cod, $egr) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(op.obl_cantidad) from erp_obligacion_pago op,
erp_pagos_documentos pd
where op.pag_id=pd.pag_id
and pd.reg_id=$reg_id
and op.obl_codigo='$cod'
and op.obl_num_egreso='$egr'
                    ");
        }
    }

    function lista_una_cuenta_bancos($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_bancos_y_cajas cb,
                            erp_plan_cuentas pc
                            where pc.pln_codigo=cb.byc_cuenta_contable
                            and cb.byc_cuenta_contable='$cod'");
        }
    }

    function lista_un_cliente_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_id='$id' ");
        }
    }

    function suma_pagos1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id and ctp_forma_pago<>'NOTA DE DEBITO' and ctp_estado=0) as monto,
                                    (select reg_total from erp_reg_documentos where reg_id='$id') as pago,
                                    (select sum(ctp_monto) from erp_ctasxpagar where reg_id=$id and ctp_forma_pago='NOTA DE DEBITO'  and ctp_estado=0) as debito");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id = c.pln_id and a.emi_id=1 and a.cas_orden_emi=$id and c.pln_estado=0");
        }
    }

    function buscar_documentos_vencer_cp($act, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM  erp_reg_documentos c, erp_pagos_documentos p, erp_i_cliente cl where cl.cli_id=c.cli_id and c.reg_id=p.reg_id  and p.pag_fecha_v > '$act' and c.reg_estado<3 and c.reg_femision between '$fec1' and '$fec2' and (c.reg_total>(select sum(ct.ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_estado='0') or not exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_estado='0'))group by c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id order by cl.cli_raz_social,c.reg_ruc_cliente,c.reg_num_documento");
        }
    }

    function buscar_documentos_vencidos_cp($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id  FROM  erp_reg_documentos c, erp_pagos_documentos p, erp_i_cliente cl where cl.cli_id=c.cli_id and c.reg_id=p.reg_id  and p.pag_fecha_v < '$act' and c.reg_estado<3 and c.reg_femision between '$fec1' and '$fec2' $txt and (c.reg_total>(select sum(ct.ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_forma_pago <>'NOTA DE DEBITO') or not exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id ) or exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_forma_pago ='NOTA DE DEBITO')) group by c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id order by cl.cli_raz_social,c.reg_ruc_cliente,c.reg_num_documento");
        }
    }

    function buscar_documentos_vencidos_xvencer($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM  erp_reg_documentos c, erp_pagos_documentos p, erp_i_cliente cl where cl.cli_id=c.cli_id and c.reg_id =p.reg_id  and p.pag_fecha_v > '$act' and c.reg_estado<3 and c.reg_femision between '$fec1' and '$fec2' and (c.reg_total>(select sum(ct.ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id ) or not exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id ))group by c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id 
                              union
                             SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM  erp_reg_documentos c, erp_pagos_documentos p , erp_i_cliente cl where cl.cli_id=c.cli_id and c.reg_id=p.reg_id  and p.pag_fecha_v < '$act' and c.reg_estado<3 and c.reg_femision between '$fec1' and '$fec2' $txt and (c.reg_total>(select sum(ct.ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_forma_pago <>'NOTA DE DEBITO') or not exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id ) or exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_forma_pago ='NOTA DE DEBITO')) group by c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id order by cli_raz_social,reg_ruc_cliente,reg_num_documento");
        }
    }

    function buscar_documentos_pagados_vencidos($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM erp_reg_documentos c, erp_i_cliente cl WHERE cl.cli_id=c.cli_id and reg_femision between '$fec1' and '$fec2' and reg_estado<3 and reg_total+(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago='NOTA DE DEBITO')=(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago<>'NOTA DE DEBITO') or reg_total=(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago<>'NOTA DE DEBITO'))
                              union
                             (SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM  erp_reg_documentos c, erp_pagos_documentos p, erp_i_cliente cl WHERE cl.cli_id=c.cli_id and c.reg_id=p.reg_id  and p.pag_fecha_v < '$act' and reg_estado<3 and c.reg_femision between '$fec1' and '$fec2' and (c.reg_total>(select sum(ct.ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ct.ctp_forma_pago <>'NOTA DE DEBITO') or not exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id )) group by c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id) order by cli_raz_social,reg_ruc_cliente, reg_num_documento");
        }
    }

    function buscar_documentos_pagados_xvencer($act, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM erp_reg_documentos c, erp_i_cliente cl WHERE cl.cli_id=c.cli_id and reg_femision between '$fec1' and '$fec2' and reg_estado<3 and reg_total+(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago='NOTA DE DEBITO')=(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago<>'NOTA DE DEBITO') or reg_total=(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago<>'NOTA DE DEBITO'))
                              union
                             (SELECT c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento,c.cli_id FROM  erp_reg_documentos c, erp_pagos_documentos p , erp_i_cliente cl WHERE cl.cli_id=c.cli_id and c.reg_id=p.reg_id  and p.pag_fecha_v > '$act' and reg_estado<3 and c.reg_femision between '$fec1' and '$fec2' and (c.reg_total>(select sum(ct.ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id ) or not exists(select * from erp_ctasxpagar ct where c.reg_id=ct.reg_id ))group by c.reg_id,cl.cli_raz_social,c.reg_ruc_cliente,c.reg_femision, c.reg_total,c.reg_num_documento) order by cli_raz_social,reg_ruc_cliente, reg_num_documento,cli_id");
        }
    }

    /////reportes////
    function lista_emisor_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_cliente_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_codigo, cli_raz_social, cli_ced_ruc, cli_calle_prin, cli_telefono FROM erp_i_cliente WHERE cli_id=$id");
        }
    }

    function lista_pagos_vencidos_rep($id, $fini, $ffin) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from pagos_documentos where cli_id=$id and pag_fecha_v between '$fini' and '$ffin' ORDER BY pag_id");
        }
    }

    function lista_pag_porvencer($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from pagos_documentos where cli_id=$id and pag_fecha_v>'$fec' ORDER BY pag_fecha_v ASC");
        }
    }

    function lista_pagos_vencidost($id, $fini, $ffin) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos_documentos where cli_id=$id and pag_fecha_v between '$fini' and '$ffin'");
        }
    }

    function lista_pag_porvencert($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos_documentos where cli_id=$id and pag_fecha_v>'$fec'");
        }
    }

    function lista_pagos_vencidos_m120($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from pagos_documentos where cli_id=$id and pag_fecha_v<'$fec'");
        }
    }

    function lista_pagos_vencidos_m120t($id, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos_documentos where cli_id=$id and pag_fecha_v<'$fec'");
        }
    }

    function listar_una_cta_comid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxpagar where reg_id=$id");
        }
    }

    function lista_ultimo_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos WHERE reg_id='$id' order by pag_fecha_v desc limit 1");
        }
    }

    function lista_pagos_factu($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM pagos_documentos WHERE reg_id=$id");
        }
    }

    function lista_totales_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(ctp_monto) as total FROM  erp_ctasxpagar WHERE reg_id=$id");
        }
    }

    function lista_pagos($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos WHERE reg_id='$id' order by pag_id $txt");
        }
    }

    function lista_documentos_ctas($nm) {
        if ($this->con->Conectar() == true) {
            return pg_query("select f.reg_ruc_cliente, cl.cli_raz_social, c.pln_id from erp_reg_documentos f, erp_ctasxpagar c, erp_i_cliente cl where f.cli_id=cl.cli_id and c.reg_id=f.reg_id  $nm group by cl.cli_raz_social,f.reg_ruc_cliente,c.pln_id
                     union
                     select f.reg_ruc_cliente, cl.cli_raz_social,'0' as pln_id from erp_reg_documentos f , erp_i_cliente cl where f.cli_id=cl.cli_id  and not exists(select * from erp_ctasxpagar c where c.reg_id=f.reg_id) $nm group by cl.cli_raz_social,f.reg_ruc_cliente order by cli_raz_social,pln_id desc ");
        }
    }

    function lista_pagos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM pagos_documentos WHERE cli_id=$id");
        }
    }

    function lista_codigo_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos f, erp_ctasxpagar c where c.reg_id=f.reg_id  and reg_ruc_cliente='$id'");
        }
    }

}

?>
