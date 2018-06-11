<?php

include_once 'Conn.php';

class Empresas {

    var $con;

    function Empresas() {
        $this->con = new Conn();
    }

    function lista_empresas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_emisor order by emi_nombre,emi_nombre_comercial");
        }
    }

    function lista_una_empresa_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_emisor where emi_id=$id");
        }
    }

    function lista_credenciales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_configuraciones where con_id=13 or con_id=14");
        }
    }

    function insert($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_emisor(
            emi_identificacion,
            emi_nombre,
            emi_nombre_comercial, 
            emi_dir_establecimiento_matriz,
            emi_obligado_llevar_contabilidad, 
            emi_dir_establecimiento_emisor,
            emi_cod_establecimiento_emisor, 
            emi_cod_punto_emision,
            emi_contribuyente_especial,
            emi_credencial,
            emi_sec_factura, 
            emi_sec_notcred,
            emi_sec_notdeb,
            emi_sec_guia_remision,
            emi_sec_retencion,
            emi_telefono,
            emi_ciudad,
            emi_pais
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
            '$data[11]',
            '$data[12]',
            '$data[13]',
            '$data[14]',
            '$data[15]',
            '$data[16]',
            '$data[17]'
               ) ");
        }
    }

    function update($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_emisor 
            set emi_identificacion='$data[0]',
            emi_nombre='$data[1]',
            emi_nombre_comercial='$data[2]',
            emi_dir_establecimiento_matriz='$data[3]',
            emi_obligado_llevar_contabilidad='$data[4]',
            emi_dir_establecimiento_emisor='$data[5]',
            emi_cod_establecimiento_emisor='$data[6]',
            emi_cod_punto_emision='$data[7]',
            emi_contribuyente_especial='$data[8]',
            emi_credencial='$data[9]',
             emi_sec_factura='$data[10]', 
            emi_sec_notcred='$data[11]',
            emi_sec_notdeb='$data[12]',
            emi_sec_guia_remision='$data[13]',
            emi_sec_retencion='$data[14]',
            emi_telefono='$data[15]',
            emi_ciudad='$data[16]',
            emi_pais='$data[17]'
             where  emi_id=$id    ");
        }
    }

    function lista_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas ORDER BY pln_codigo");
        }
    }

    function lista_cuentas_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id='$id'");
        }
    }

}

?>
