<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//        return pg_connect('host=localhost port=5432 dbname=dpvworld user=postgres password=1234');
    }

    function lista_costos() {
        if ($this->con() == true) {
            return pg_query("select * from erp_mp where mp_p='0'");
        }
    }

    function lista_ultimo_ingreso($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_i_mov_inv_pt i, erp_transacciones t where i.trs_id=t.trs_id and i.pro_id=$id and t.trs_operacion=0 order by mov_id desc limit 1");
        }
    }

    function update_costo($id, $c) {
        if ($this->con() == true) {
            return pg_query("update erp_mp set mp_p='$c' where id='$id'");
        }
    }

}

$Obj = new Objeto();
$sms = 0;

$cns = $Obj->lista_costos();
while ($rst = pg_fetch_array($cns)) {
    $rst_mov = pg_fetch_array($Obj->lista_ultimo_ingreso($rst[id]));
    if (!empty($rst_mov) && $rst_mov[mov_val_unit]>0) {

        if ($Obj->update_costo($rst[id],round($rst_mov[mov_val_unit]),2) == false) {
            $sms = pg_last_error();
            print_r($sms);
            echo "<br>";
        }
    }
}
echo $sms;
?>
