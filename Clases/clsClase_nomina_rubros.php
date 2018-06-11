<?php

include_once 'Conn.php';

class Clase_nomina_rubros {

    var $con;

    function Clase_nomina_rubros() {
        $this->con = new Conn();
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

    function insert_nomina_rubros($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_rubros_nomina(
            rub_codigo, 
            rub_grupo,
            rub_descripcion, 
            rub_valor, 
            rub_tipo,
            rub_cuenta_contable,
            rub_nomina,
            rub_iess,
            rub_combo,
            rub_estado,
            rub_tipo_valor,
            rub_operacion
            )
    VALUES ('$data[0]','$data[1]','$data[2]',$data[3],$data[4],$data[5],$data[6],$data[7],$data[8],1,$data[9],'$data[10]')");
        }
    }

    function update_nomina_rubros($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_rubros_nomina SET rub_codigo='$data[0]',    
                                                           rub_grupo='$data[1]',
                                                           rub_descripcion='$data[2]',    
                                                           rub_valor=$data[3],   
                                                           rub_tipo=$data[4],
                                                           rub_cuenta_contable=$data[5],
                                                           rub_nomina=$data[6],
                                                           rub_iess=$data[7],
                                                           rub_combo=$data[8],
                                                           rub_tipo_valor=$data[9],
                                                           rub_operacion='$data[10]' 
                                                       WHERE rub_id=$id");
        }
    }

    function lista_buscardor_nomina_rubros() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros_nomina ORDER BY rub_id DESC");
        }
    }

    function upd_estado_nomina_rubros($std, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_rubros_nomina SET rub_estado=$std WHERE rub_id=$id");
        }
    }

    function lista_una_nomina_rubros($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros_nomina WHERE rub_id=$id");
        }
    }

    function lista_buscardor_un_reg_nomina_rubros($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros_nomina $txt ORDER BY rub_id DESC");
        }
    }

    function lista_val_operacion_fijo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT rub_operacion FROM erp_rubros_nomina WHERE rub_operacion='DT*VD'");
        }
    }

    ///////////////////////////////// ROLES DE PAGO 

    function lista_fecha_pago_sueldo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones WHERE con_id=16");
        }
    }

    function lista_fecha_pago_he() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones WHERE con_id=17");
        }
    }

    function lista_rubros_nomina_si() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros_nomina WHERE rub_nomina=1 and rub_estado=1 ORDER BY rub_codigo='SLD001' DESC, rub_grupo='HORAS EXTRAS' DESC, rub_grupo='IESS' DESC, rub_grupo='ADICIONALES' DESC, rub_codigo ASC");
        }
    }

    function lista_rubros_nomina_si_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros_nomina WHERE rub_id=$id and rub_nomina=1 ORDER BY rub_codigo='SLD001' DESC, rub_codigo ASC");
        }
    }

    function lista_rubro_sueldo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_rubros_nomina WHERE rub_codigo='SLD001'");
        }
    }

    function lista_empleados_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  par_empleados where 
                (emp_documento like '%$txt%' 
                    or emp_apellido_paterno like '%$txt%'  
                        or emp_apellido_materno like '%$txt%' 
                            or emp_nombres like '%$txt%') 
                                and emp_estado=0
                            Order by emp_apellido_paterno");
        }
    }

    function lista_empleados_cedula($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_empleados where emp_documento='$id' ");
        }
    }

    function lista_secuencial_nomina() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT nom_secuencial FROM erp_nomina ORDER BY nom_secuencial DESC LIMIT 1");
        }
    }

    function insert_nomina($data) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_nomina(
  nom_fec_registro,
  nom_forma_pago,
  nom_periodo,
  nom_fp_desde,
  nom_fp_hasta,
  nom_horas_extras,
  nom_fh_desde,
  nom_fh_hasta,
  nom_empleado,
  nom_sueldo_base,
  nom_dias_trabajados,
  nom_tipo_desembolso,
  nom_numdoc_desembolso,
  nom_usuario,
  nom_secuencial,
  nom_anio
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',$data[8],'$data[9]','$data[10]',0,'0','$data[13]','$data[14]','$data[15]')");
        }
    }

    function lista_una_nomina($sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nomina WHERE nom_secuencial='$sec'");
        }
    }

    function insert_det_nomina($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_nomina(
            nom_id,
            dnm_rubro,
            dnm_cantidad,
            dnm_valor,
            dnm_tipo,
            dnm_formula
            )
    VALUES ($id,$data[0],'$data[1]','$data[2]',$data[3],'$data[4]')");
        }
    }

    function lista_buscardor_nomina_pagos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as id,
       split_part(prod,'&',2) as ape_pat,
       split_part(prod,'&',3) as ape_mat,
       split_part(prod,'&',4) as nombre,
       split_part(prod,'&',5) as f_pago,
       split_part(prod,'&',6) as periodo,
       split_part(prod,'&',7) as fp_desde,
       split_part(prod,'&',8) as fp_hasta,
       split_part(prod,'&',9) as sueldo_base,
       split_part(prod,'&',10) as dias_trabj,
       split_part(prod,'&',11) as nom_id,
       split_part(prod,'&',12) as anio,
       split_part(prod,'&',13) as seccion,
       split_part(prod,'&',14) as cedula,
       split_part(prod,'&',15) as codigo,
       loc1,
       loc2,
       loc3,
       loc4,
       loc5,
       loc6,
       loc7,
       loc8,
       loc9,
       loc10,
       loc11,
       loc12,
       loc13,
       loc14
from generar_rol where prod is not null
$txt
");
        }
    }

    function lista_empleados_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_empleados WHERE emp_id=$id");
        }
    }

    function lista_emp_seccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_secciones WHERE sec_id=$id");
        }
    }

    function lista_un_det_nomina($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_nomina WHERE nom_id=$id");
        }
    }

    function lista_tot_ingresos_egresos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(select sum(dnm_valor) from erp_det_nomina WHERE nom_id=$id and dnm_tipo=1) as ingreso,
                                   (select sum(dnm_valor) from erp_det_nomina WHERE nom_id=$id and dnm_tipo=2) as egreso");
        }
    }

    function lista_anio_pagos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT nom_anio FROM erp_nomina WHERE nom_anio<>'2015' GROUP BY nom_anio ORDER BY nom_anio DESC");
        }
    }

    function lista_periodo_pago() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT nom_periodo FROM erp_nomina GROUP BY nom_periodo ORDER BY nom_periodo ASC");
        }
    }

    function upd_nomina($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nomina SET nom_dias_trabajados='$data[10]'
                                               WHERE nom_id=$id");
        }
    }

    function upd_nomina_det($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_det_nomina SET dnm_rubro=$data[0],
                                                       dnm_cantidad=$data[1],
                                                       dnm_valor='$data[2]',
                                                       dnm_tipo='$data[3]',
                                                       dnm_formula='$data[4]'
                                               WHERE nom_id=$id and dnm_rubro=$data[0]");
        }
    }
    
    function lista_empleados_activos(){
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT emp_id, emp_sueldo_inicial FROM par_empleados WHERE emp_estado=0");
        }
    }
    
    function lista_secciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_descricpion");
        }
    }

    /////////////////////////////////////// GENERAR PAGOS A ROLES

    function lista_pagos_roles() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nomina ORDER BY nom_id DESC");
        }
    }

    function lista_cuentas_bancos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pln_id, pln_codigo, pln_descripcion FROM erp_bancos_y_cajas b, erp_plan_cuentas p WHERE b.byc_id_cuenta=p.pln_id ORDER BY pln_descripcion");
        }
    }

    function upd_encabezado_nomina($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nomina SET nom_forma_pago_rol=$data[0],
                                                   nom_concepto='$data[1]', 
                                                   nom_num_documento='$data[2]',
                                                   nom_cta_contable='$data[3]', 
                                                   nom_fec_pago='$data[4]',
                                                   nom_estado=$data[5]
                                               WHERE nom_id=$id");
        }
    }

    function lista_buscardor_pagos_a_roles($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nomina $txt ORDER BY nom_id DESC");
        }
    }

    function lista_id_cta($cta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pln_id, pln_codigo, pln_descripcion FROM erp_bancos_y_cajas b, erp_plan_cuentas p WHERE b.byc_id_cuenta=p.pln_id and pln_codigo='$cta' ORDER BY pln_descripcion");
        }
    }

    function lista_nomina_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nomina WHERE nom_id=$id");
        }
    }

    function upd_chq_tran_imp() {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nomina SET nom_chq_trans_impr=0");
        }
    }

    function upd_chq_tran_imp_id($val, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nomina SET nom_chq_trans_impr=$val WHERE nom_id=$id");
        }
    }

    function lista_chqtrnas_dif0() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nomina WHERE nom_chq_trans_impr<>0");
        }
    }

    function upd_estado_nomina($std, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nomina SET nom_estado=$std WHERE nom_id=$id");
        }
    }
    
    function lista_nomina_mes_anio($perd, $anio, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nomina WHERE nom_periodo='$perd' and nom_anio='$anio' and nom_empleado=$id");
        }
    }

}
