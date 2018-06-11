<?php

include_once 'Conn.php';

class Clase_asientos {

    var $con;

    function Clase_asientos() {
        $this->con = new Conn();
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
                con_tipo
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[9]')");
        }
    }

    function upd_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_asientos_contables SET 
                con_asiento='$data[0]',
                con_concepto='$data[1]',
                con_documento='$data[2]',
                con_fecha_emision='$data[3]',
                con_concepto_debe='$data[4]',
                con_concepto_haber='$data[5]',
                con_valor_debe='$data[6]',
                con_valor_haber='$data[7]',
                con_tipo='$data[9]'
                WHERE  con_id=$data[8]");
        }
    }

    function delete_asientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_asiento='$id'");
        }
    }

/////////////////////OMAR    
    function lista_asientos_contables() {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_plan_cuentas where character_length (pln_codigo) = 14");
            return pg_query("SELECT * FROM erp_plan_cuentas ORDER BY pln_codigo ASC");
        }
    }

    function lista_asientos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables ORDER BY con_asiento DESC");
        }
    }

    function lista_asientos_fecha($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_asiento FROM erp_asientos_contables WHERE con_fecha_emision BETWEEN '$desde' AND '$hasta'");
        }
    }

    function lista_un_asiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where con_id=$id");
        }
    }

    function lista_ingreso_asiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where con_asiento='$id'  order by con_id   ");
        }
    }

    function lista_buscador_asientos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables $txt ORDER BY con_asiento DESC,con_id asc");
        }
    }

    function lista_total_asientos_fecha($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_asiento FROM  erp_asientos_contables $txt group BY con_asiento order by con_asiento ");
        }
    }

    function lista_cuentas_asientos($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where  con_asiento='$as' order by con_id");
        }
    }

    function lista_secuencial_asientos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where con_asiento like 'AM%'  ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function update_asientos($sts, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_asientos_contables SET con_estado=$sts where con_id=$id");
        }
    }

    function elimina_asietos($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_asientos_contables where con_asiento='$num'");
        }
    }

    function listar_un_asiento($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_codigo='$cod'");
        }
    }

    function lista_un_plan_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_codigo='$id'");
        }
    }

    function listar_descripcion_asiento($id,$id1) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where trim(pln_codigo)=trim('$id') or trim(pln_codigo)=trim('$id1') ");
        }
    }

    function listar_debe_haber_asiento_cuenta($as, $cuenta) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(con_valor_debe) from erp_asientos_contables where con_concepto_debe='$cuenta' and con_asiento='$as'  ) as debe, 
(select sum(con_valor_haber) from erp_asientos_contables where con_concepto_haber='$cuenta' and con_asiento='$as'  ) as haber,
(select con_fecha_emision from erp_asientos_contables where (con_concepto_debe='$cuenta' or con_concepto_haber='$cuenta') and con_asiento='$as' group by con_fecha_emision  ) as fecha,
(select con_documento from erp_asientos_contables where (con_concepto_debe='$cuenta' or con_concepto_haber='$cuenta') and con_asiento='$as' group by con_documento  ) as documento,
(select con_concepto from erp_asientos_contables where (con_concepto_debe='$cuenta' or con_concepto_haber='$cuenta') and con_asiento='$as' group by con_concepto  ) as concepto,
(select con_estado from erp_asientos_contables where (con_concepto_debe='$cuenta' or con_concepto_haber='$cuenta') and con_asiento='$as' group by con_estado  ) as estado");
        }
    }

    function listar_debe_haber_asiento_cuenta1($as, $cuenta, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(con_valor_debe) from erp_asientos_contables where con_concepto_debe='$cuenta' and con_asiento='$as' and con_id=$id  ) as debe, 
(select sum(con_valor_haber) from erp_asientos_contables where con_concepto_haber='$cuenta' and con_asiento='$as' and con_id=$id ) as haber,
(select con_fecha_emision from erp_asientos_contables where con_id=$id ) as fecha,
(select con_documento from erp_asientos_contables where con_id=$id  ) as documento,
(select con_concepto from erp_asientos_contables where con_id=$id  ) as concepto,
(select con_estado from erp_asientos_contables where con_id=$id  ) as estado");
        }
    }

    function listar_debe_haber_asiento_cuenta_fac($as, $cuenta, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(con_valor_debe) from erp_asientos_contables where con_concepto_debe='$cuenta' and con_asiento='$as' ) as debe, 
(select sum(con_valor_haber) from erp_asientos_contables where con_concepto_haber='$cuenta' and con_asiento='$as') as haber,
(select con_fecha_emision from erp_asientos_contables where con_id=$id ) as fecha,
(select con_documento from erp_asientos_contables where con_id=$id  ) as documento,
(select con_concepto from erp_asientos_contables where con_id=$id  ) as concepto,
(select con_estado from erp_asientos_contables where con_id=$id  ) as estado");
        }
    }

    function listar_asiento_numero($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_asientos_contables where con_asiento='$num' ORDER BY con_asiento DESC,con_concepto_haber asc");
        }
    }

    function listar_asiento_agrupado($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_asiento FROM  erp_asientos_contables where con_asiento='$num' GROUP BY con_asiento");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function suma_totales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(con_valor_debe) as debe, sum(con_valor_haber) as haber FROM erp_asientos_contables where con_asiento='$id'");
        }
    }

    function suma_totales_dh($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(con_valor_debe) as debe FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and ac.con_concepto_debe='$cuenta'),
                                   (SELECT sum(con_valor_haber) as haber FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and ac.con_concepto_haber='$cuenta')");
        }
    }

////***************REPORTE MAYORIZACION************************

    function lista_cuentas_fecha($desde, $hasta, $txt1, $txt2) {
        if ($this->con->Conectar() == true) {
            return pg_query("(SELECT trim(con_concepto_debe) as con_concepto_debe FROM erp_asientos_contables WHERE con_fecha_emision BETWEEN '$desde' AND '$hasta' and con_concepto_debe!='' $txt1 group by con_concepto_debe order by con_concepto_debe)
union 
(SELECT trim(con_concepto_haber) as con_concepto_haber FROM erp_asientos_contables WHERE con_fecha_emision BETWEEN '$desde' AND '$hasta' and con_concepto_haber!='' $txt2 group by con_concepto_haber  order by con_concepto_haber) order by con_concepto_debe
");
        }
    }

    ///aumenta la consulta para obtener el nombre del cliente
    function lista_asientos_cuenta_fecha($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 'Debe' as tipo, ac.*, c.cli_raz_social FROM erp_asientos_contables ac, erp_i_cliente c WHERE ac.cli_id=c.cli_id and ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and ac.con_concepto_debe='$cuenta'
                                 union
                             SELECT 'Haber' as tipo, ac.*,c.cli_raz_social FROM erp_asientos_contables ac, erp_i_cliente c  WHERE ac.cli_id=c.cli_id and ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and ac.con_concepto_haber='$cuenta'
                            union
                            SELECT 'Debe' as tipo, ac.*, '' as cli_raz_social  FROM erp_asientos_contables ac WHERE ac.cli_id=0 and ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and ac.con_concepto_debe='$cuenta'
                                 union
                             SELECT 'Haber' as tipo, ac.*, '' as cli_raz_social FROM erp_asientos_contables ac WHERE ac.cli_id=0 and ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and ac.con_concepto_haber='$cuenta' order by con_fecha_emision");
        }
    }

    function suma_totales_mayorizacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(con_valor_debe) as debe, sum(con_valor_haber) as haber FROM erp_asientos_contables where con_asiento='$id'");
        }
    }

/////////*******************REPORTE BALANCE COMPROBACION **************

    function lista_suma_cuentas($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select  sum(ac.con_valor_debe) FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and trim(ac.con_concepto_debe)=trim('$cuenta')) as debe, 
                                    (select  sum(ac.con_valor_haber) FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and trim(ac.con_concepto_haber)=trim('$cuenta')) as haber");
        }
    }

    function lista_balance_general($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,2)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as debe1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,2)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as haber1,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,5)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as debe2,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,5)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as haber2,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,8)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as debe3,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,8)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as haber3,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,11)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as debe4,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,11)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as haber4,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,14)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as debe5,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,14)='$cuenta' and con_fecha_emision between '$desde' and '$hasta')as haber5");
        }
    }
    
    function lista_balance_general1($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables where trim(con_concepto_debe) like trim('$cuenta%') and con_fecha_emision between '$desde' and '$hasta')as debe,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where trim(con_concepto_haber) like trim('$cuenta%') and con_fecha_emision between '$desde' and '$hasta')as haber");
        }
    }

    function listar_factura_asiento($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_reg_documentos d, erp_i_cliente c where d.cli_id=c.cli_id and d.con_asiento='$txt'");
        }
    }

    function listar_retencion_regid($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_retencion where reg_id='$id' and ret_estado_aut<>'ANULADO'");
            return pg_query("SELECT *  FROM  erp_retencion where reg_id='$id' and (ret_estado_aut is null or ret_estado_aut<>'ANULADO')");
        }
    }

    function lista_cuentas_asientos_fac($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_debe as concepto, con_valor_debe as valor,con_estado,con_tipo,doc_id , '0'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_debe))<>0 and con_asiento='$as'
                             union 
                             SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_haber as concepto, con_valor_haber as valor,con_estado,con_tipo,doc_id , '1'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_haber))<>0 and con_asiento='$as'
                             order by con_id");
        }
    }

    function listar_debe_haber_asiento_cuenta_fac1($as, $cuenta) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(con_valor_debe) from erp_asientos_contables where con_concepto_debe='$cuenta' and con_asiento='$as' ) as debe, 
                                    (select sum(con_valor_haber) from erp_asientos_contables where con_concepto_haber='$cuenta' and con_asiento='$as') as haber");
        }
    }

    function listar_factura_asiento_anulado($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_reg_documentos d, erp_i_cliente c where d.cli_id=c.cli_id and d.reg_asi_anulacion='$txt'");
        }
    }

    function listar_ultima_retencion_regid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT *  FROM  erp_retencion where reg_id='$id' order by ret_numero desc limit 1");
        }
    }

    function lista_cuentas_asientos_anulado($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_debe as concepto, con_valor_debe as valor,con_estado,con_tipo,doc_id , '0'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_debe))<>0 and con_asiento='$as'
                             union 
                             SELECT con_id, con_asiento,con_documento,con_concepto,con_fecha_emision,con_concepto_haber as concepto, con_valor_haber as valor,con_estado,con_tipo,doc_id , '1'  as tipo FROM  erp_asientos_contables where  char_length(trim(con_concepto_haber))<>0 and con_asiento='$as'
                             order by concepto,tipo");
        }
    }

    function listar_retencion_asiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT *  FROM  erp_registro_retencion where con_asiento='$id'");
        }
    }

    function lista_plan_cuentas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas order by pln_codigo");
        }
    }

    function lista_un_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor where emi_id='$id'");
        }
    }
     function listar_asientos_debe($as, $cuenta, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where con_concepto_debe='$cuenta' and con_asiento='$as' and con_id='$id'");
        }
    }

    function listar_asientos_haber($as, $cuenta, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where con_concepto_haber='$cuenta' and con_asiento='$as' and con_id='$id'");
        }
    }
    ///funcion para calcular saldo inicial
    function lista_suma_cuentas_ant($cuenta, $desde) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select  sum(ac.con_valor_debe) FROM erp_asientos_contables ac WHERE ac.con_fecha_emision <'$desde' and ac.con_concepto_debe='$cuenta') as debe, 
                                    (select  sum(ac.con_valor_haber) FROM erp_asientos_contables ac WHERE ac.con_fecha_emision < '$desde' and ac.con_concepto_haber='$cuenta') as haber");
        }
    }
    
   

}

?>
