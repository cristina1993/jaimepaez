<?php

include_once 'Conn.php';

class Clase_resumen_ctasxpagar {

    var $con;

    function Clase_resumen_ctasxpagar() {
        $this->con = new Conn();
    }

    
    function lista_ctasxpagar($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_ctasxpagar c, erp_reg_documentos r, erp_i_cliente cl where c.reg_id=r.reg_id and r.cli_id=cl.cli_id and ctp_forma_pago!='RETENCION' and ctp_forma_pago!='NOTA DE CREDITO' $txt  order by ctp_fecha_pago");
        }
    }

        
    function lista_ctasxpagar_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_ctasxpagar c, erp_reg_documentos r, erp_i_cliente cl where c.reg_id=r.reg_id and r.cli_id=cl.cli_id and ctp_id=$id ");
        }
    }
    
    function lista_una_cuenta($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_plan_cuentas where trim(pln_codigo)='$cod'");
        }
    }
    function lista_una_cuentaid($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_plan_cuentas where pln_id='$cod'");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
