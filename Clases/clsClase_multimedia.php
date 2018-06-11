<?php

include_once 'Conn.php';

class Multimedia {

    var $con;

    function Multimedia() {
        $this->con = new Conn();
    }

    function lista_multimedia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_multimedia ORDER BY mlt_fecha ");
        }
    }
    function lista_multimedia_fecha($desde,$hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_multimedia where mlt_fecha between '$desde' and '$hasta' ");
        }
    }
    function lista_multimedia_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_multimedia where upper(mlt_archivo) like '%$txt%' ");
        }
    }
    
    function lista_un_multimedia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_multimedia WHERE mlt_id=$id");
        }
    }
    
    function lista_sec_codigo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_multimedia ORDER BY mlt_cod desc limit 1 ");
        }
    }
    function delete_mult($id){
        if ($this->con->Conectar() == true){
            return pg_query("delete FROM  erp_multimedia WHERE mlt_id=$id  ");
        }
    }
    function delete_sms_mult(){
        if ($this->con->Conectar() == true){
            return pg_query("delete FROM  erp_sms");
        }
    }
    function lista_sms_mult(){
        if ($this->con->Conectar() == true){
            return pg_query("select * FROM  erp_sms");
        }
    }
    
    function insert_sms_multimedia($data) {
        if ($this->con->Conectar() == true) {
            $f=date('Y-m-d');
            return pg_query("INSERT INTO erp_sms(
            sms_sms,
            sms_user,
            sms_fecha)
    VALUES ('$data',
            '$_SESSION[usuario]',
            '$f') ");
        }
    }
    
    function insert_multimedia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_multimedia(
            mlt_archivo,
            mlt_cod,
            mlt_fecha,
            mlt_user,
            mlt_informacion)
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',
            '$data[3]',
            '$data[4]') ");
        }
    }
    
    function update_multimedia($data,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_multimedia SET
            mlt_archivo='$data[0]',
            mlt_cod='$data[1]',
            mlt_fecha='$data[2]',
            mlt_user='$data[3]',
            mlt_informacion='$data[4]'
            WHERE mlt_id=$id ");
        }
    }

    
}

?>
