<?php

include_once 'Conn.php';

class Clase_impuestos {

    var $con;

    function Clase_impuestos() {
        $this->con = new Conn();
    }

    function insert_impuestos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO porcentages_retencion(
                        por_codigo,
                        por_porcentage,
                        por_descripcion,
                        por_siglas,
                        por_cod_ats,
                        cta_id   )
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]')");
        }
    }

    function upd_impuestos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE porcentages_retencion 
                SET por_codigo='$data[0]',
                    por_porcentage='$data[1]',
                        por_descripcion='$data[2]',
                            por_siglas='$data[3]', 
                             por_cod_ats='$data[4]',  
                             cta_id='$data[5]' 
                                WHERE por_id='$id'");
        }
    }

    function delete_impuestos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM porcentages_retencion WHERE por_id= '$id'"
            );
        }
    }

    function lista_un_impuesto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM porcentages_retencion where por_id='$id'");
        }
    }

    function lista_buscardor_impuestos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM porcentages_retencion $txt order by por_codigo");
        }
    }
        function lista_transportista() {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT * FROM erp_transportista order by tra_razon_social");
            }
        }
    
///incremento de campos        13/11/2015

        function lista_cuentas_contables() {
            if ($this->con->Conectar() == true) {
                return pg_query("select * from erp_plan_cuentas order by pln_codigo");
            }
        }
        function lista_una_cuenta_id($id) {
            if ($this->con->Conectar() == true) {
                return pg_query("select * from erp_plan_cuentas where pln_id=$id");
            }
        }
        function lista_una_cuenta_codigo($cod) {
            if ($this->con->Conectar() == true) {
                return pg_query("select * from erp_plan_cuentas where pln_codigo='$cod'");
            }
        }


///////////////////////////////////////////////////////////////////////////////////////         
    }

?>
