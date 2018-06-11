<?php
include_once '../Clases/clsUsers.php';
class Permisos {
    var $con;
    var $edition=1;
    var $delete=1;
    var $add=1;
    var $view=1;
    var $report=1;
    var $show=1;
    var $special=1;
    
 function Permisos(){
     $this->con=new Conn();
 }	
 
function Permit($user,$mod)
{
    $User= new User();
    $permisos=pg_fetch_array($User->lista_permiso_por_modulo($user,$mod));    
    if($permisos['asg_opl_level_0']=='t' || $permisos['asg_opl_level_6']=='t')
        {
            $this->edition=0;
            $this->delete=0;
            $this->add=0;
            $this->view=0;
            $this->report=0;
            $this->show=0; 
            $this->special=0; 
        }else
            {
                if($permisos['asg_opl_level_1']=='t')            
                {
                $this->edition=0;
                $this->show=0; 
                }
                if($permisos['asg_opl_level_2']=='t')            
                {
                $this->delete=0;
                $this->show=0; 
                }
                if($permisos['asg_opl_level_3']=='t')            
                {
                $this->add=0;  
                $this->show=0; 
                }
                if($permisos['asg_opl_level_4']=='t')            
                {
                $this->view=0; 
                $this->show=0; 
                }
                if($permisos['asg_opl_level_5']=='t')            
                {
                $this->report=0;    
                $this->show=0; 
                }
            }
            return $this->edition;
            return $this->delete;
            return $this->add;
            return $this->view;
            return $this->report;
            return $this->show;
            return $this->special; 
}

       
}

?>
    
    
