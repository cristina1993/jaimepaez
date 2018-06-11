<?php

set_time_limit(0);

class Costos_unitarios {

    function Conectar() {
        return pg_connect('host=localhost'
                . ' port=5432 '
                . ' dbname=erp2.5_server'
                . ' user=postgres'
                . ' password=SuremandaS495');
    }

    function lista_costos_mov($id, $txt) {
        if ($this->Conectar() == true) {
            return pg_query("select (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as ingreso,
                                    (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as egreso,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as icnt,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as ecnt");
        }
    }

    function lista_movimiento_productos($id) {
        if ($this->Conectar() == true) {
            return pg_query("select pro_id,bod_id from erp_i_mov_inv_pt where pro_id=$id order by bod_id limit 1");
        }
    }

    function lista_productos() {
        if ($this->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_i='0'");
        }
    }

    function update_costos($id, $c) {
        if ($this->Conectar() == true) {
            return pg_query("update erp_mp set mp_p='$c' where id=$id");
        }
    }

}

$Cost = new Costos_unitarios();

$cns = $Cost->lista_productos();
$n = 0;
while ($rst = pg_fetch_array($cns)) {
    $n++;
    $rst_m = pg_fetch_array($Cost->lista_movimiento_productos($rst[id]));
    $txt = "and m.bod_id=$rst_m[bod_id]";
    $rst2 = pg_fetch_array($Cost->lista_costos_mov($rst[id], $txt));
    if (!empty($rst_m)) {
        $costo_unit = (($rst2[ingreso] - $rst2[egreso]) / ($rst2[icnt] - $rst2[ecnt]));
        if (empty($costo_unit)) {
            $costo_unit = 0;
        }
        if ($Cost->update_costos($rst[id], round($costo_unit, 4)) == false) {
            $sms = pg_last_error();
        }
        echo $n . ' ' . $rst[mp_c] . ' = ' .  round($costo_unit,4) . '  ' . $sms . '<br>';
    }
}
?>
