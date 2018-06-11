<?php //
class Conn {
var $con = 0;
function Conectar(){
$this->con = pg_connect('host=localhost'
        . ' port=5432 '
        . ' dbname=jaimepaez'
        . ' user=postgres'
        . ' password=1234' );
return $this->con;
}
}

//$Obj=new Conn();
//$link=$Obj->Conectar();
//if($link){
//echo "ok";
//}else{
//echo "no";
//}

?>