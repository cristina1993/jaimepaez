<?php

include_once 'Conn.php';

class Clase_registro_costos {

    var $con;

    function Clase_registro_costos() {
        $this->con = new Conn();
    }

  

    function lista_importacion() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT reg_importe FROM erp_reg_documentos where reg_importe  is not null and char_length(trim(reg_importe))<>0 and  reg_estado <> '3' group by reg_importe");
        }
    }
    
      function lista_productos_documentos($txt) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_reg_documentos f, erp_reg_det_documentos d, erp_mp p, erp_tipos t where d.reg_id=f.reg_id and d.pro_id=p.id and cast(p.mp_a as integer)=t.tps_id and t.tps_tipo<>'0&0&1' and t.tps_tipo<>'0&0&0' $txt");
            return pg_query("SELECT * FROM erp_reg_documentos f, erp_reg_det_documentos d, erp_mp p where d.reg_id=f.reg_id and d.pro_id=p.id and f.reg_importe='$txt'  and f.reg_estado <> '3'  order by ids,reg_num_documento");
        }
    }
    
     function lista_productos_consventa($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT d.pro_id,d.det_descripcion,d.det_codigo_empresa,sum(d.det_cantidad) as cant,(sum(d.det_total)/ sum(d.det_cantidad)) as vunit, sum(d.det_total) as total
                             FROM erp_reg_documentos f, erp_reg_det_documentos d, erp_mp p, erp_tipos t
                             where d.reg_id=f.reg_id and d.pro_id=p.id and cast(p.mp_a as integer)=t.tps_id and t.tps_tipo<>'0&0&1' and t.tps_tipo<>'0&0&0' and p.ids!=79 and p.ids!=80 and f.reg_importe='$txt'  and f.reg_estado <> '3' 
                             group by d.pro_id,d.det_descripcion,d.det_codigo_empresa
                             order by det_descripcion");
        }
    }
    
    
    function suma_total_importacion($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(d.det_total) as total
                             FROM erp_reg_documentos f, erp_reg_det_documentos d, erp_mp p, erp_tipos t
                             where d.reg_id=f.reg_id and d.pro_id=p.id and cast(p.mp_a as integer)=t.tps_id and t.tps_tipo<>'0&0&1' and t.tps_tipo<>'0&0&0' and p.ids!=79 and p.ids!=80 and f.reg_importe='$txt'  and reg_estado <> '3' ");
        }
    }
    
    function lista_factura($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos f, erp_i_cliente c where f.cli_id=c.cli_id and f.reg_importe='$txt' and reg_estado <> '3'  order by reg_num_documento ");
        }
    }
    
    
       function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mp p, erp_tipos t where cast(p.mp_a as integer)=t.tps_id and t.tps_tipo<>'0&0&1' and t.tps_tipo<>'0&0&0' and p.ids!=79 and p.ids!=80 and p.id=$id");
        }
    }
    
     function lista_una_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_documentos f, erp_i_cliente c where f.cli_id=c.cli_id and f.reg_id='$id' and reg_estado <> '3' ");
        }
    }
    
    function lista_suma_factura_imp($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(reg_sbt) FROM erp_reg_documentos f, erp_i_cliente c where f.cli_id=c.cli_id and f.reg_importe='$txt'  and reg_estado <> '3' ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
