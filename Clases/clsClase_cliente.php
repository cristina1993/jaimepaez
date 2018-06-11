<?php

include_once 'Conn.php';

class Clase_cliente {

    var $con;

    function Clase_cliente() {
        $this->con = new Conn();
    }

    function lista_cliente() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente ORDER BY cli_codigo DESC");
        }
    }

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente WHERE cli_id=$id");
        }
    }

    function lista_buscador_cliente($txt, $tipo, $categoria, $estado) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente 
$txt
$tipo
$categoria    
$estado order by cli_codigo  ");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente(
            cli_fecha,
            cli_tipo,
            cli_categoria,
            cli_codigo,
            cli_estado,
            cli_apellidos,
            cli_nombres,
            cli_ced_ruc,
            cli_raz_social,
            cli_nom_comercial,
            cli_retencion,
            cli_credito,
            cli_cup_maximo,
            cli_cat_cliente, 
            cli_nacionalidad,
            cli_lugar_nac,
            cli_fecha_nac,
            cli_estado_civil,
            cli_con_cedula,
            cli_con_apellido_paterno,
            cli_con_apellido_materno,
            cli_con_nombres,
            cli_tipo_vivienda,
            cli_valor_arriendo,
            cli_pais, 
            cli_provincia,
            cli_canton,
            cli_parroquia,
            cli_calle_prin,
            cli_numeracion,
            cli_calle_sec,
            cli_tiempo_residencia,
            cli_telefono, 
            cli_email,
            cli_referencia,
            cli_tipo_actividad,
            cli_empresa,
            cli_actividad,
            cli_propia, 
            cli_cargo,
            cli_tiempo_trab,
            cli_actividad_telefono,
            cli_actividad_celular,
            cli_direccion_trabajo,
            cli_sueldo,
            cli_ingresos, 
            cli_total_gastos,
            cli_con_sueldo,
            cli_con_ingresos,
            cli_con_total_gastos,
            cli_ref_apellidos1, 
            cli_ref_nombres1,
            cli_ref_parentesco1,
            cli_ref_telefono1,
            cli_ref_apellidos2,
            cli_ref_nombres2,
            cli_ref_parentesco2,
            cli_ref_telefono2,
            cli_rep_apellidos,
            cli_rep_nombres,
            cli_rep_telefono,
            cli_rep_celular,
            cli_rep_email,
            cli_cont_apellidos,
            cli_cont_nombres,
            cli_cont_telefono,
            cli_cont_celular, 
            cli_cont_email,
            cli_refc_empresa1,
            cli_refc_credito1,
            cli_refc_telefono1,
            cli_refc_empresa2,
            cli_refc_credito2,
            cli_refc_telefono2,
            cli_refb_institucion1,
            cli_refb_cuenta1,
            cli_refb_tip_cuenta1,
            cli_refb_institucion2, 
            cli_refb_cuenta2,
            cli_refb_tip_cuenta2,
            cli_tipo_cliente)
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
                    $data[11],
                    '$data[12]',
                    '$data[13]',
                    '$data[14]',
                    '$data[15]',
                    '$data[16]',
                    '$data[17]',
                    '$data[18]',
                    '$data[19]',
                    '$data[20]',
                    '$data[21]',
                    '$data[22]',
                    '$data[23]',
                    '$data[24]',
                    '$data[25]',
                    '$data[26]',
                    '$data[27]',
                    '$data[28]',
                    '$data[29]',
                    '$data[30]',
                    '$data[31]',
                    '$data[32]',
                    '$data[33]',
                    '$data[34]',
                    '$data[35]',
                    '$data[36]',
                    '$data[37]',
                    '$data[38]',
                    '$data[39]',
                    '$data[40]',
                    '$data[41]',
                    '$data[42]',
                    '$data[43]',
                    '$data[44]',
                    '$data[45]',
                    '$data[46]',
                    '$data[47]',
                    '$data[48]',
                    '$data[49]',
                    '$data[50]',
                    '$data[51]',
                    '$data[52]',
                    '$data[53]',
                    '$data[54]',
                    '$data[55]',
                    '$data[56]',
                    '$data[57]',
                    '$data[58]',
                    '$data[59]',
                    '$data[60]',
                    '$data[61]',
                    '$data[62]',
                    '$data[63]',
                    '$data[64]',
                    '$data[65]',
                    '$data[66]',
                    '$data[67]',
                    '$data[68]',
                    '$data[69]',
                    '$data[70]',
                    '$data[71]',
                    '$data[72]',
                    '$data[73]',
                    '$data[74]',
                    '$data[75]',
                    '$data[76]',
                    '$data[77]',
                    '$data[78]',
                    '$data[79]',
                    '$data[80]')");
        }
    }

    function upd_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_fecha='$data[0]',
            cli_tipo='$data[1]',
            cli_categoria='$data[2]',
            cli_codigo='$data[3]',
            cli_estado='$data[4]',
            cli_apellidos='$data[5]',
            cli_nombres='$data[6]',
            cli_ced_ruc='$data[7]',
            cli_raz_social='$data[8]',
            cli_nom_comercial='$data[9]',
            cli_retencion='$data[10]',
            cli_credito=$data[11],
            cli_cat_cliente='$data[13]', 
            cli_nacionalidad='$data[14]',
            cli_lugar_nac='$data[15]',
            cli_fecha_nac='$data[16]',
            cli_estado_civil='$data[17]',
            cli_con_cedula='$data[18]',
            cli_con_apellido_paterno='$data[19]',
            cli_con_apellido_materno='$data[20]',
            cli_con_nombres='$data[21]',
            cli_tipo_vivienda='$data[22]',
            cli_valor_arriendo='$data[23]',
            cli_pais='$data[24]', 
            cli_provincia='$data[25]',
            cli_canton='$data[26]',
            cli_parroquia='$data[27]',
            cli_calle_prin='$data[28]',
            cli_numeracion='$data[29]',
            cli_calle_sec='$data[30]',
            cli_tiempo_residencia='$data[31]',
            cli_telefono='$data[32]', 
            cli_email='$data[33]',
            cli_referencia='$data[34]',
            cli_tipo_actividad='$data[35]',
            cli_empresa='$data[36]',
            cli_actividad='$data[37]',
            cli_propia='$data[38]', 
            cli_cargo='$data[39]',
            cli_tiempo_trab='$data[40]',
            cli_actividad_telefono='$data[41]',
            cli_actividad_celular='$data[42]',
            cli_direccion_trabajo='$data[43]',
            cli_sueldo='$data[44]',
            cli_ingresos='$data[45]', 
            cli_total_gastos='$data[46]',
            cli_con_sueldo='$data[47]',
            cli_con_ingresos='$data[48]',
            cli_con_total_gastos='$data[49]',
            cli_ref_apellidos1='$data[50]', 
            cli_ref_nombres1='$data[51]',
            cli_ref_parentesco1='$data[52]',
            cli_ref_telefono1='$data[53]',
            cli_ref_apellidos2='$data[54]',
            cli_ref_nombres2='$data[55]',
            cli_ref_parentesco2='$data[56]',
            cli_ref_telefono2='$data[57]',
            cli_rep_apellidos='$data[58]',
            cli_rep_nombres='$data[59]',
            cli_rep_telefono='$data[60]',
            cli_rep_celular='$data[61]',
            cli_rep_email='$data[62]',
            cli_cont_apellidos='$data[63]',
            cli_cont_nombres='$data[64]',
            cli_cont_telefono='$data[65]',
            cli_cont_celular='$data[66]', 
            cli_cont_email='$data[67]',
            cli_refc_empresa1='$data[68]',
            cli_refc_credito1='$data[69]',
            cli_refc_telefono1='$data[70]',
            cli_refc_empresa2='$data[71]',
            cli_refc_credito2='$data[72]',
            cli_refc_telefono2='$data[73]',
            cli_refb_institucion1='$data[74]',
            cli_refb_cuenta1='$data[75]',
            cli_refb_tip_cuenta1='$data[76]',
            cli_refb_institucion2='$data[77]', 
            cli_refb_cuenta2='$data[78]',
            cli_refb_tip_cuenta2='$data[79]', 
            cli_tipo_cliente='$data[80]' 
            WHERE cli_id=$id");
        }
    }

    function insert_dir_entrega($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cli_direccion_entrega(
            cli_id,
            cde_local,
            cde_apellido,
            cde_nombre,
            cde_telefono, 
            cde_pais,
            cde_provincia,
            cde_canton,
            cde_parroquia,
            cde_calle_prin, 
            cde_numero,
            cde_calle_sec,
            cde_referencia)
    VALUES ($data[0],'$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]')");
        }
    }

    function insert_aprobacion($cod, $sts, $cambio, $txt) {
        if ($this->con->Conectar() == true) {
            $fecha = date('Y-m-d');
            return pg_query("INSERT INTO erp_i_aprobacion(
                cli_codigo,
                apb_sts, 
                apb_cambio, 
                abp_campo, 
                apb_fecha_reg, 
                apb_solicita,
                apb_autoriza)
    VALUES ('$cod',$sts,'$cambio','$txt','$fecha',$_SESSION[usuid],0)");
        }
    }

    function delete_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cliente WHERE cli_id=$id");
        }
    }

    function delete_direccion_entrega($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cli_direccion_entrega WHERE cli_id=$id");
        }
    }

    function delete_aprobacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_aprobacion WHERE cli_codigo='$id'");
        }
    }

    function lista_secuencial_cliente($sig) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_codigo like '$sig%' ORDER BY cli_codigo DESC LIMIT 1");
        }
    }

    function lista_ultimo_cliente() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente order by cli_id desc limit 1");
        }
    }

    function lista_direccion_cliid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cli_direccion_entrega d, erp_i_cliente c where d.cli_id=c.cli_id and c.cli_id=$id");
        }
    }

    function upd_raz_social($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_i_cliente set cli_raz_social='$dat' where cli_id=$id  ");
        }
    }

    function insert_varios_clientes($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente(
            cli_fecha,
            cli_tipo,
            cli_categoria,
            cli_codigo,
            cli_estado,
            cli_ced_ruc,
            cli_raz_social,
            cli_pais, 
            cli_provincia,
            cli_canton,
            cli_parroquia,
            cli_calle_prin,
            cli_telefono, 
            cli_email,
            cli_sueldo,
            cli_ingresos,
            cli_total_gastos,
            cli_con_sueldo,
            cli_con_ingresos,
            cli_con_total_gastos,
            cli_refc_credito1,
            cli_refc_credito2,
            cli_tipo_cliente
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
                    '$data[17]',
                    '$data[18]',
                    '$data[19]',
                    '$data[20]',
                    '$data[21]',
                    '$data[22]'
                    )");
        }
    }

       function lista_cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where cli_id=$id");
        }
    }
    
    
///////////////////////////////////////////////////////////////////////////////////////         
    
    function lista_una_ced_ruc($ruc){
        if($this->con->Conectar() == true){
            return pg_query("SELECT cli_ced_ruc FROM erp_i_cliente WHERE cli_ced_ruc='$ruc'");
        }
    }
    
}

?>
