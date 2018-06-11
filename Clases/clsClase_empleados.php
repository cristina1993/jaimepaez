<?php

include_once 'Conn.php';

class Clase_empleados {

    var $con;

    function Clase_empleados() {
        $this->con = new Conn();
    }

    function lista_buscador_empleados($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_empleados $txt order by emp_apellido_paterno,emp_apellido_materno,emp_nombres");
        }
    }

    function lista_un_empleado($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_empleados e where e.emp_id=$id");
        }
    }

    function insert_empleado($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_empleados(
            emp_fregistro, 
            emp_foto, 
            emp_documento, 
            emp_tipo_documento, 
            emp_sexo, 
            emp_apellido_paterno, 
            emp_apellido_materno, 
            emp_nombres, 
            emp_fnacimiento, 
            emp_provincia, 
            emp_canton, 
            emp_parroquia, 
            emp_direccion, 
            emp_telefono, 
            emp_celular, 
            emp_estado_civil, 
            emp_cta_bancaria, 
            emp_cta_banco, 
            emp_licencia_tipo, 
            emp_nivel_instruccion, 
            emp_titulo, 
            emp_estudia, 
            emp_estado, 
            emp_codigo, 
            emp_nacionalidad, 
            emp_d_provincia, 
            emp_d_canton, 
            emp_d_parroquia, 
            emp_d_sector, 
            emp_conyugue, 
            emp_hijo1_sexo, 
            emp_hijo1_nombres, 
            emp_hijo1_fnac, 
            emp_hijo2_sexo, 
            emp_hijo2_nombres, 
            emp_hijo2_fnac, 
            emp_hijo3_sexo, 
            emp_hijo3_nombres, 
            emp_hijo3_fnac, 
            emp_hijo4_sexo, 
            emp_hijo4_nombres, 
            emp_hijo4_fnac, 
            emp_hijo5_sexo, 
            emp_hijo5_nombres, 
            emp_hijo5_fnac, 
            emp_disc, 
            emp_d_disc, 
            emp_empresa1, 
            emp_cargo1, 
            emp_t_trabajo1, 
            emp_t_telefono1, 
            emp_empresa2, 
            emp_cargo2, 
            emp_t_trabajo2, 
            emp_t_telefono2, 
            emp_empresa3, 
            emp_cargo3, 
            emp_t_trabajo3, 
            emp_t_telefono3, 
            emp_sueldo_inicial, 
            emp_tipo_sangre, 
            emp_restriccion, 
            emp_epp, 
            emp_obs, 
            emp_act_fecha, 
            emp_act, 
            grp_id, 
            sec_id, 
            emp_cargo, 
            rev, 
            emp_fret, 
            emp_sub_sec, 
            emp_hor_from, 
            emp_hor_until, 
            emp_timbrar, 
            emp_rf1_nombre, 
            emp_rf2_nombre, 
            emp_rf3_nombre, 
            emp_rf1_parentezco, 
            emp_rf2_parentezco, 
            emp_rf3_parentezco, 
            emp_rf1_telefono, 
            emp_rf2_telefono, 
            emp_rf3_telefono, 
            emp_motivo_retiro, 
            emp_codigo_encriptado,
            emp_email,
            emp_sueldo_basico
            )
    VALUES (
            '$data[0]',
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
            '$data[80]',
            '$data[81]',
            '$data[82]',
            '$data[83]',
            '$data[84]',
            '$data[85]',
            '$data[86]',
             $data[87]
    );
");
        }
    }

    function update_empleado($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update par_empleados set 
            emp_fregistro='$data[0]', 
            emp_foto='$data[1]', 
            emp_documento='$data[2]', 
            emp_tipo_documento='$data[3]', 
            emp_sexo='$data[4]', 
            emp_apellido_paterno='$data[5]', 
            emp_apellido_materno='$data[6]', 
            emp_nombres='$data[7]', 
            emp_fnacimiento='$data[8]', 
            emp_provincia='$data[9]', 
            emp_canton='$data[10]', 
            emp_parroquia='$data[11]', 
            emp_direccion='$data[12]', 
            emp_telefono='$data[13]', 
            emp_celular='$data[14]', 
            emp_estado_civil='$data[15]', 
            emp_cta_bancaria='$data[16]', 
            emp_cta_banco='$data[17]', 
            emp_licencia_tipo='$data[18]', 
            emp_nivel_instruccion='$data[19]', 
            emp_titulo='$data[20]', 
            emp_estudia='$data[21]', 
            emp_estado='$data[22]', 
            emp_codigo='$data[23]', 
            emp_nacionalidad='$data[24]', 
            emp_d_provincia='$data[25]', 
            emp_d_canton='$data[26]', 
            emp_d_parroquia='$data[27]', 
            emp_d_sector='$data[28]', 
            emp_conyugue='$data[29]', 
            emp_hijo1_sexo='$data[30]', 
            emp_hijo1_nombres='$data[31]', 
            emp_hijo1_fnac='$data[32]', 
            emp_hijo2_sexo='$data[33]', 
            emp_hijo2_nombres='$data[34]', 
            emp_hijo2_fnac='$data[35]', 
            emp_hijo3_sexo='$data[36]', 
            emp_hijo3_nombres='$data[37]', 
            emp_hijo3_fnac='$data[38]', 
            emp_hijo4_sexo='$data[39]', 
            emp_hijo4_nombres='$data[40]', 
            emp_hijo4_fnac='$data[41]', 
            emp_hijo5_sexo='$data[42]', 
            emp_hijo5_nombres='$data[43]', 
            emp_hijo5_fnac='$data[44]', 
            emp_disc='$data[45]', 
            emp_d_disc='$data[46]', 
            emp_empresa1='$data[47]', 
            emp_cargo1='$data[48]', 
            emp_t_trabajo1='$data[49]', 
            emp_t_telefono1='$data[50]', 
            emp_empresa2='$data[51]', 
            emp_cargo2='$data[52]', 
            emp_t_trabajo2='$data[53]', 
            emp_t_telefono2='$data[54]', 
            emp_empresa3='$data[55]', 
            emp_cargo3='$data[56]', 
            emp_t_trabajo3='$data[57]', 
            emp_t_telefono3='$data[58]', 
            emp_sueldo_inicial='$data[59]', 
            emp_tipo_sangre='$data[60]', 
            emp_restriccion='$data[61]', 
            emp_epp='$data[62]', 
            emp_obs='$data[63]', 
            emp_act_fecha='$data[64]', 
            emp_act='$data[65]', 
            grp_id='$data[66]', 
            sec_id='$data[67]', 
            emp_cargo='$data[68]', 
            rev='$data[69]', 
            emp_fret='$data[70]', 
            emp_sub_sec='$data[71]', 
            emp_hor_from='$data[72]', 
            emp_hor_until='$data[73]', 
            emp_timbrar='$data[74]', 
            emp_rf1_nombre='$data[75]', 
            emp_rf2_nombre='$data[76]', 
            emp_rf3_nombre='$data[77]', 
            emp_rf1_parentezco='$data[78]', 
            emp_rf2_parentezco='$data[79]', 
            emp_rf3_parentezco='$data[80]', 
            emp_rf1_telefono='$data[81]', 
            emp_rf2_telefono='$data[82]', 
            emp_rf3_telefono='$data[83]', 
            emp_motivo_retiro='$data[84]', 
            emp_codigo_encriptado='$data[85]',     
            emp_email='$data[86]',    
                emp_sueldo_basico=$data[87]
                            where emp_id=$id;
                            ");
        }
    }

    function lista_secciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_descricpion");
        }
    }

    function lista_una_seccion_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_id=$id");
        }
    }

    function lista_subsecciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_subseccion order by sbs_descripcion");
        }
    }

    function lista_una_subseccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_subseccion where sbs_id=$id");
        }
    }

    function lista_grupo_horarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_grupos_horarios order by gru_horarios");
        }
    }

    function lista_un_horario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_grupos_horarios where gru_id=$id");
        }
    }

    function lista_una_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_division where div_id=$id");
        }
    }

    function lista_un_empleado_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_empleados e where e.emp_codigo='$id'");
        }
    }

    //////horas trabajadas/////
    function lista_empleado_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("  select * from par_empleados em,
                                            par_secciones sc,
                                            par_grupo_horarios gh
                                            where em.sec_id=sc.sec_id
                                            and gh.grp_id=em.grp_id
                                            and em.emp_codigo='$cod' ");
        }
    }

    function lista_todos_empleados_activos($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_empleados em,
                                            par_secciones sc,
                                            erp_grupos_horarios gh
                                            WHERE em.sec_id=sc.sec_id
                                            AND gh.gru_id=em.grp_id
                                            AND sc.sec_gerencia='$ger' 
                                            AND em.emp_estado=0 ORDER BY em.emp_codigo ");
        }
    }

    function lista_emp_ger_activos($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM 
                                            par_empleados em,
                                            par_secciones sec 
                                            WHERE em.sec_id=sec.sec_id
                                            AND em.emp_estado=0 
                                            AND sec.sec_gerencia='$ger'
                                            ORDER BY emp_codigo ASC");
        }
    }

    function lista_emp_ger_div_activos($ger, $div) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM 
                                            par_empleados em,
                                            par_secciones sec,
                                            erp_grupos_horarios gh
                                            WHERE em.sec_id=sec.sec_id
                                            AND em.emp_estado=0 
                                            AND em.grp_id=gh.gru_id 
                                            AND sec.sec_gerencia='$ger'
                                            AND sec.sec_area='$div'
                                            ORDER BY emp_codigo ASC");
        }
    }

    function lista_emp_sec_activos($sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM 
                                            par_empleados em,
                                            par_secciones sec, 
                                            par_grupo_horarios gh
                                            WHERE em.sec_id=sec.sec_id
                                            AND em.grp_id=gh.grp_id 
                                            AND em.emp_estado=0 
                                            AND sec.sec_id=$sec
                                            ORDER BY emp_codigo ASC");
        }
    }

    function lista_horarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM 
                                            par_grupo_horarios grp,
                                            par_asg_horarios ash,
                                            par_horario hr
                                            WHERE ash.grp_id=grp.grp_id
                                            AND   ash.hor_id=hr.hor_id
                                            ORDER BY grp.grp_codigo ASC, hr.hor_inicio ASC ");
        }
    }

    function lista_horarios_emp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT gh.grp_var,
                                                hr.hor_inicio,
                                                hr.hor_alms,
                                                hr.hor_alme,
                                                hr.hor_fin,
                                                hr.hor_horas,
                                                hr.hor_alm
                                                FROM 
                                                par_empleados em,
                                                par_asg_horarios ah,
                                                par_grupo_horarios gh,
                                                par_horario hr
                                                where ah.grp_id=gh.grp_id
                                                and   ah.hor_id=hr.hor_id
                                                and   em.grp_id=gh.grp_id
                                                and   em.emp_codigo='$emp' ORDER BY hr.hor_inicio     ");
        }
    }

    function lista_divisiones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_division where ger_id=$id order by div_descripcion");
        }
    }

    function lista_secciones_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_area='$id' order by sec_descricpion");
        }
    }

    function lista_sueldo_basico() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=18");
        }
    }

}
