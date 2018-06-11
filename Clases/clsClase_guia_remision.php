<?php

include_once 'Conn.php';

class Clase_guia_remision {

    var $con;

    function Clase_guia_remision() {
        $this->con = new Conn();
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='5'");
        }
    }

    function lista_configuraciones_dec() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='2'");
        }
    }

    function lista_una_factura_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_numero='$id'");
        }
    }

    function lista_detalle_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_factura where fac_id='$id'");
        }
    }

    function lista_pro_factura($pro, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_factura where pro_id='$pro' and fac_id='$id'");
        }
    }

    function lista_detalle_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_guia where gui_id='$id'");
        }
    }

    function delete_det_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_guia WHERE gui_id='$id'"
            );
        }
    }

    function delete_guia_remision($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_guia_remision WHERE gui_id='$id'"
            );
        }
    }

    function lista_facturas($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial");
        }
    }

    function lista_facturas_fec($desde, $hasta, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where fecha_emision >='$desde' and fecha_emision <='$hasta' and tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_una_factura($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes where num_secuencial='$id' and cod_punto_emision='$cod' and  tipo_comprobante=1");
        }
    }

    function lista_guias_factura($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM guia_remision where num_comprobante_venta='$id' order by num_comprobante desc");
            return pg_query("SELECT num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion,guia_estado_correo FROM guia_remision where num_comprobante_venta='$id' group by num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion,guia_estado_correo");
        }
    }

    function lista_guias_fec($desde, $hasta, $emi) {///////////
        if ($this->con->Conectar() == true) {
            if ($emi == 10) {
                $bd = '010';
            } else {
                $bd = '00' . $emi;
            }
            return pg_query("SELECT num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion FROM guia_remision where fecha_emision >='$desde' and fecha_emision <='$hasta' and substr(num_comprobante,1,3)='$bd' group by num_comprobante, num_comprobante_venta,fecha_emision,fecha_inicio_transporte,fecha_fin_transporte,motivo_traslado,destino,identificacion_destinario,nombre_destinatario, punto_partida, destino,com_observacion,com_estado,com_autorizacion");
        }
    }

    function lista_una_guia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision g, erp_transportista t where g.tra_id=t.tra_id and g.gui_numero='$id'");
        }
    }

    function lista_una_guia_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision g, erp_transportista t, erp_i_cliente c where g.cli_id=c.cli_id and g.tra_id=t.tra_id and g.gui_id='$id'");
        }
    }

    function lista_un_transportista($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transportista where tra_identificacion='$id'");
        }
    }

    function lista_buscador_guias($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision g, erp_transportista t, erp_vendedor v where g.tra_id=t.tra_id and g.vnd_id=v.vnd_id $txt ORDER BY g.gui_numero");
        }
    }

    function lista_transportista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM transportista");
        }
    }

//     function lista_un_transportista($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM transportista where identificacion='$id'");
//        }
//    }

    function insert_guia_remision($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_guia_remision(
            vnd_id, 
            emi_id, 
            cli_id, 
            gui_numero, 
            gui_fecha_emision, 
            gui_fecha_inicio, 
            gui_fecha_fin,
            gui_motivo_traslado, 
            gui_punto_partida, 
            gui_destino, 
            gui_identificacion, 
            gui_nombre, 
            gui_identificacion_transp, 
            gui_doc_aduanero, 
            gui_cod_establecimiento, 
            gui_num_comprobante,
            gui_observacion,
            fac_id,
            tra_id,
            gui_denominacion_comp,
            gui_aut_comp,
            gui_fecha_comp
                           )
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]','$data[20]','$data[21]')");
        }
    }

    function insert_det_guia_remision($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_guia(
            gui_id, 
            dtg_cantidad, 
            dtg_codigo, 
            dtg_cod_aux, 
            dtg_descripcion,
            pro_id)
            VALUES ($id,'$data[0]','$data[1]','$data[2]','$data[3]','$data[4]')");
        }
    }

    function update_guia_remision($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_guia_remision SET
            vnd_id='$data[0]', 
            emi_id='$data[1]', 
            cli_id='$data[2]', 
            gui_numero='$data[3]', 
            gui_fecha_emision='$data[4]', 
            gui_fecha_inicio='$data[5]', 
            gui_fecha_fin='$data[6]',
            gui_motivo_traslado='$data[7]', 
            gui_punto_partida='$data[8]', 
            gui_destino='$data[9]', 
            gui_identificacion='$data[10]', 
            gui_nombre='$data[11]', 
            gui_identificacion_transp='$data[12]', 
            gui_doc_aduanero='$data[13]', 
            gui_cod_establecimiento='$data[14]', 
            gui_num_comprobante='$data[15]',
            gui_observacion='$data[16]',
            fac_id='$data[17]',
            tra_id='$data[18]',
            gui_denominacion_comp='$data[19]',
            gui_aut_comp='$data[20]',
            gui_fecha_comp='$data[21]'
                           where gui_id=$id");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedor where vnd_nombre='$txt'");
        }
    }

    function suma_cantidad_entregado($cod, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(d.dtg_cantidad) as suma FROM erp_det_guia d, erp_guia_remision g where d.gui_id=g.gui_id and d.pro_id='$cod' and g.fac_id='$id'");
        }
    }

    function lista_cantidad($cod, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM guia_remision where cod_producto='$cod' and num_comprobante='$id' ");
        }
    }

    function lista_secuencial_documento($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_guia_remision where emi_id=$bod order by gui_numero desc limit 1");
        }
    }

    function lista_buscar_transportistas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_transportista where 
                (tra_identificacion like '%$txt%' 
                    or tra_razon_social like '%$txt%'  
                        or tra_placa like '%$txt%' 
                          ) 
                                                        Order by tra_razon_social");
        }
    }

    function insert_transportista($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO transportista(
                        identificacion,
                        razon_social,
                        placa
                        )
            VALUES ('$data[0]','$data[1]','$data[2]')");
        }
    }

    function lista_guias() {///////////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision order by gui_numero");
        }
    }

    function lista_guias_no_autorizadas() {///////////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_guia_remision where gui_autorizacion is null or gui_autorizacion='' or gui_autorizacion='nullnull' order by gui_numero");
        }
    }

    function upd_guia_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_guia_remision 
                set gui_clave_acceso='$clave'  where gui_id=$id ");
        }
    }

    function upd_guia_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_guia_remision 
                set gui_estado_aut='RECIBIDA AUTORIZADO', gui_fec_hora_aut='$fh' , gui_autorizacion='$na'  where gui_clave_acceso='$id' ");
        }
    }

    function lista_clientes_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%' 
                            
                            Order by cli_raz_social");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

    function lista_producto_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp p where p.mp_a='0' and mp_i='0'");
        }
    }

    function lista_un_producto_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where id=$id and mp_a='0' and mp_i='0'");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
