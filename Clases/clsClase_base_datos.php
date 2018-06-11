<?php

include_once 'Conn.php';

class Clase_base_datos {

    var $con;

    function Clase_base_datos() {
        $this->con = new Conn();
    }

    function delete_det_factura() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_factura");
        }
    }

    function delete_pagos_factura() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_pagos_factura");
        }
    }

    function delete_factura() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_factura");
        }
    }

    function delete_det_guia() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_guia");
        }
    }

    function delete_guia() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_guia_remision");
        }
    }

    function delete_det_nota_credito() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_nota_credito");
        }
    }

    function delete_nota_credito() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_nota_credito");
        }
    }

    function delete_det_nota_debito() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_nota_debito");
        }
    }

    function delete_nota_debito() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_nota_debito");
        }
    }

    function delete_det_retencion() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_retencion");
        }
    }

    function delete_retencion() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_retencion");
        }
    }
    
     function delete_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_mp");
        }
    }
    
     function insert_productos() {
        if ($this->con->Conectar() == true) {
            $fec = date(Y - m - d);
            return pg_query("INSERT INTO erp_mp(
                 ids,
                mp_a,
                mp_c,
                mp_d,
                mp_q,
                mp_e,
                mp_f,
                mp_g,
                mp_h,
                mp_i,
                mp_j,
                mp_k,
                mp_l,
                mp_m
                )values(
                26,
                '0',
                'PRU001',
                'PRUEBA1',
                'KG',
                '1.00',
                '0.00',
                '0.00',
                '0',
                '0',
                '0',
                '0',
                '',
                '');
                INSERT INTO erp_mp(
                ids,
                mp_a,
                mp_c,
                mp_d,
                mp_q,
                mp_e,
                mp_f,
                mp_g,
                mp_h,
                mp_i,
                mp_j,
                mp_k,
                mp_l,
                mp_m
                )values(
                26,
                '0',
                'PRU002',
                'PRUEBA2',
                'KG',
                '1.00',
                '0.00',
                '0.00',
                '0',
                '0',
                '0',
                '0',
                '',
                '')
                    ");
        }
    }

    function delete_transportista() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_transportista");
        }
    }

    
    function insert_transportita() {
        if ($this->con->Conectar() == true) {
            $fec = date(Y - m - d);
            return pg_query("INSERT INTO erp_transportista(
                tra_id,
                tra_razon_social,
                tra_email,
                tra_placa,
                tra_telefono,
                tra_direccion,
                tra_identificacion
                )values(
                1,
                'PRUEBA',
                'prueba@prueba.com',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA'
                )
                    ");
        }
    }

    function delete_clientes() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cliente");
        }
    }

    function insert_cliente() {
        if ($this->con->Conectar() == true) {
            $fec = date('Y-m-d');
            return pg_query("INSERT INTO erp_i_cliente (
                cli_id,
                cli_fecha,
                cli_tipo,
                cli_categoria,
                cli_codigo,
                cli_estado,
                cli_ced_ruc,
                cli_raz_social,
                cli_nom_comercial,
                cli_pais,
                cli_provincia,
                cli_canton,
                cli_parroquia,
                cli_calle_prin,
                cli_telefono,
                cli_email,
                cli_tipo_cliente,
                cli_fecha_nac
                )values(
                1,
                '$fec',
                '2',
                '2',
                'CJ00001',
                '0',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'PRUEBA',
                'prueba@prueba.com',
                '0',
                '$fec'
                )
                    ");
        }
    }

    function delete_vendedor() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_vendedor where vnd_id<>1");
        }
    }

    function delete_emisor() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_emisor where emi_id<>1");
        }
    }

    function update_emsior() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_emisor SET 
                            emi_identificacion='0000000000000',
                            emi_nombre='NOMBRE DE LA EMPRESA',
                            emi_nombre_comercial='NOMBRE DE LA EMPRESA',
                            emi_dir_establecimiento_matriz='DIRECCION MATRIZ DE LA EMPRESA',
                            emi_dir_establecimiento_emisor='DIRECCION DE LA EMPRESA', 
                            emi_cod_establecimiento_emisor='1', 
                            emi_cod_punto_emision='1', 
                            emi_contribuyente_especial='0', 
                            emi_establecimiento='NOMBRE DE LA EMPRESA', 
                            emi_obligado_llevar_contabilidad='NO',
                            emi_cod_cli='1', 
                            emi_cod_orden='1', 
                            emi_cod='1', 
                            emi_credencial='0', 
                            emi_sec_factura='1', 
                            emi_sec_notcred='1', 
                            emi_sec_notdeb='1', 
                            emi_sec_guia_remision='1', 
                            emi_sec_retencion='1', 
                            emi_logo='', 
                            emi_asi_fac_cliente_nac='',
                            emi_asi_fac_cliente_ext='', 
                            emi_asi_fac_descuento='', 
                            emi_asi_fac_iva='', 
                            emi_asi_fac_ice='', 
                            emi_asi_fac_irbprn='', 
                            emi_asi_fac_ventas='', 
                            emi_asi_fac_propina='', 
                            emi_asi_nc_cliente_nac='', 
                            emi_asi_nc_descuento='', 
                            emi_asi_nc_iva='', 
                            emi_asi_nc_ice='', 
                            emi_asi_nc_irbprn='', 
                            emi_asi_nc_ventas='', 
                            emi_asi_nc_propina=''
                    where emi_id=1");
        }
    }

    function delete_asi_users() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asg_option_list where usu_id<>1");
        }
    }
    
    function delete_users() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_users where usu_id<>1");
        }
    }

    function delete_auditoria() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_auditoria");
        }
    }
    
    function delete_credenciales() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_configuraciones set con_valor2='' where con_id=13 or con_id=14");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
