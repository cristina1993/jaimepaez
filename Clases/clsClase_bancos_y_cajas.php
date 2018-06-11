<?php

include_once 'Conn.php';

class Clase_bancos_y_cajas {

    var $con;

    function Clase_bancos_y_cajas() {
        $this->con = new Conn();
    }
    
    function insert_bancos_cajas($data){
        if($this->con->Conectar() == true){
            return pg_query("INSERT INTO erp_bancos_y_cajas(
            byc_tipo, 
            byc_referencia,
            byc_num_cuenta, 
            byc_tipo_cuenta, 
            byc_documento,
            byc_saldo,
            byc_cuenta_contable,
            byc_id_cuenta
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]',$data[7])");
        }
    }
    
    function update_bancos_cajas($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_bancos_y_cajas SET byc_tipo='$data[0]',    
                                                           byc_referencia='$data[1]',
                                                           byc_num_cuenta='$data[2]',    
                                                           byc_tipo_cuenta='$data[3]',   
                                                           byc_documento='$data[4]',
                                                           byc_saldo='$data[5]',
                                                           byc_cuenta_contable='$data[6]',
                                                           byc_id_cuenta=$data[7]
                                                       WHERE byc_id=$id");
        }
    }
    
    function lista_buscardor_bancos_cajas(){
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM erp_bancos_y_cajas");
        }
    }
    
    function lista_plan_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas ORDER BY pln_codigo");
        }
    }
    
    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id=$id");
        }
    }
    
    function lista_un_reg_banco_caja($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_bancos_y_cajas WHERE byc_id=$id");
        }
    }
    
    function lista_suma_ctsxcobrar($cta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(cta_monto) as suma_ctasxcobrar FROM erp_ctasxcobrar WHERE cta_banco='$cta'");
        }
    }
    
    function lista_suma_ctsxpagar($cta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(ctp_monto) as suma_ctasxpagar FROM erp_ctasxpagar WHERE ctp_banco='$cta'");
        }
    }
    
    function lista_buscardor_un_reg_bancos_cajas($txt){
        if($this->con->Conectar() == true){
            return pg_query("SELECT * FROM erp_bancos_y_cajas $txt");
        }
    }
    
    function upd_estado_plan_cuentas($std, $id){
        if($this->con->Conectar() == true){
            return pg_query("UPDATE erp_plan_cuentas SET pln_estado=$std WHERE pln_id=$id");
        }
    }

}

?>
