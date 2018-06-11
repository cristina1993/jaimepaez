<?php

class Conn {

    var $con = 0;

    function Conectar() {
        $this->con = pg_connect('host=localhost  port=5432  dbname=plasfabri  user=postgres  password=SuremandaS495');
        return $this->con;
    }

    function lista_comprobantes() {
        if ($this->Conectar() == true) {
            return pg_query("select *
                             from comprobantes order by nombre");
        }
    }
    
    function lista_comp($nm) {
        if ($this->Conectar() == true) {
            return pg_query("select *
                             from erp_i_cliente where upper(cli_raz_social)='$nm'  ");
        }
    }
    
    function update_comprobantes($ci,$id) {
        if ($this->Conectar() == true) {
            return pg_query("update comprobantes set identificacion='$ci' where com_id=$id  ");
        }
    }
    
    
}


$Obj= new Conn();
$cns=$Obj->lista_comprobantes();

while($rst=  pg_fetch_array($cns)){
    
    $rst_cli=  pg_fetch_array($Obj->lista_comp(strtoupper($rst[nombre])));
    if(empty($rst_cli)){
        $nm='No existe';
        echo $rst[nombre].'   '.$nm.'</br>';    
    }else{
        $nm=trim($rst_cli[cli_ced_ruc]);
        if(!$Obj->update_comprobantes($nm, $rst[com_id])){
            $nm=  pg_last_error();
        }else{
            $nm='ok';
        }
    }
    
    
    
}

?>
