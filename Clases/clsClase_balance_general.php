<?php

include_once 'Conn.php';

class Clase_balance_general {

    var $con;

    function Clase_balance_general() {
        $this->con = new Conn();
    }
    
            
    function lista_balance_general1($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_debe) and  pln_operacion=0 and con_concepto_debe like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_haber) and  pln_operacion=0 and con_concepto_haber like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a where not exists (select * from erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_debe)) and con_concepto_debe like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe2,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a where not exists (select * from erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_haber)) and con_concepto_haber like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber2,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_debe) and  pln_operacion=1 and con_concepto_debe like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe3,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_haber) and  pln_operacion=1 and con_concepto_haber like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber3");

        }
    }

    function lista_balance_general($cod,$fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,2)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,2)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber1,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,5)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe2,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,5)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber2,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,8)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe3,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,8)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber3,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,11)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe4,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,11)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber4,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,14)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe5,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,14)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber5");
        }
    }

    function listar_asiento_agrupado($n, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_concepto_debe FROM  erp_asientos_contables where substr(con_concepto_debe,1,2)='$n' and con_fecha_emision between '$fec1' and '$fec2' union SELECT con_concepto_haber FROM erp_asientos_contables where substr(con_concepto_haber,1,2)='$n' and con_fecha_emision between '$fec1' and '$fec2' order by con_concepto_debe");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where identificacion='$id'");
        }
    }

    function listar_descripcion_asiento($id,$idp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where (pln_codigo='$id' or pln_codigo='$idp')");
        }
    }

    function suma_cuentas($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(con_valor_debe) as debe FROM erp_asientos_contables where con_concepto_debe='$cod' and con_fecha_emision between '$fec1' and '$fec2') as debe,
                                   (SELECT sum(con_valor_haber) as debe FROM erp_asientos_contables where con_concepto_haber='$cod' and con_fecha_emision between '$fec1' and '$fec2') as haber");
        }
    }
    
    function suma_pasivo_patrimonio($fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(con_valor_debe) as debe FROM erp_asientos_contables where (substr(con_concepto_debe,1,2)='2.' or substr(con_concepto_debe,1,2)='3.') and con_fecha_emision between '$fec1' and '$fec2') as debe,
                                   (SELECT sum(con_valor_haber) as debe FROM erp_asientos_contables where (substr(con_concepto_haber,1,2)='2.' or substr(con_concepto_haber,1,2)='3.') and con_fecha_emision between '$fec1' and '$fec2') as haber");
        }
    }

    function lista_un_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor where emi_id='$id'");
        }
    }
}

?>
