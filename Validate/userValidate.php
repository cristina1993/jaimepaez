<?PHP
try{
session_start();
include_once("../Clases/clsUsers.php");
include_once("../Clases/clsAuditoria.php");
if(isset ($_POST)){
$user=$_POST['user'];
$pass=md5($_POST['pass']);
$objUser=new User();
$Audit=new Auditoria();
$consulta=$objUser->listUser($pass,$user);
$registro=pg_fetch_array($consulta);
if($registro['usu_login']==$user and $registro['usu_pass']==$pass and $registro['usu_status']=='t'){
    $_SESSION['User']=$registro['usu_login'];    
    $_SESSION['usuid']=$registro['usu_id'];
    $_SESSION['usuario']=$registro['usu_person'];
    if($Audit->insert(array('Ingreso al Sistema','Login',''))==false)
    {
        echo pg_last_error();
    }else{
       header("location:../menu/main.php");     
    }      
    
}else{
    include_once 'closeSession.php';
}
}
    
} catch (Exception $e) {
   echo 'Error:',$e->getMessage(); 
}
?>
