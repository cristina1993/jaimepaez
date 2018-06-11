<?php
session_start();
include_once '../Clases/clsUsers.php';
$User=new User();
$mn=$_REQUEST[mn];
$op=$_REQUEST[op];

switch ($op)
{
    case 0:
        $cns=$User->listaMenuGrupos($_SESSION[usuid],$mn);
        $data='';
        $n=0;
        while($rst=pg_fetch_array($cns)){
            $n++;
            
            if($n==(pg_num_rows($cns)))
            {
                $fin='';
            }else{
                $fin='*';
            }    
            
            $rstSbmn=pg_fetch_array($User->listaUnSubMenus($_SESSION[usuid],$rst[mod_id]));
            
            $data=$data.$rstSbmn[opl_direccion].'&'.$rst[mod_id].'&'.$rst[mod_nombre].'&'.$rstSbmn[opl_id].$fin;
        }
        echo $data;
        
    break;    
    case 1:
        $cns=$User->listaSubMenus($_SESSION[usuid],$mn);
        $data='';
        $n=0;
        while($rst=pg_fetch_array($cns)){
            $n++;
            
            if($n==(pg_num_rows($cns)))
            {
                $fin='';
            }else{
                $fin='*';
            }    
            $data=$data.$rst[opl_modulo].'&'.$rst[opl_direccion].$fin;
        }
        echo $data;
        
    break;    
    
}



?>
