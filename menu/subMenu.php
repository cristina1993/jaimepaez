
<?php
session_start();
include_once '../Clases/clsUsers.php';
$User=new User();
if(!empty ($_REQUEST['ol']))
{
    $ol=$_REQUEST['ol'];
    $_SESSION['ol']=$ol;
}else{
    $ol=$_SESSION['ol'];
}    
//echo $ol;
?>
    <script>
    function lista(id,ol,dir)
    {
        window.location='../'+dir+'.php?ol='+ol+'&sub_id='+id;
        
        //alert('../'+dir+'.php?ol='+ol+'&sub_id='+id);
        
    }
    </script>
    <style>
        .subMenu{
   			font-size: 10px;
			background: white;
			padding: 5px 10px 5px;
			color: #333;
			font-weight:bold;
			cursor: pointer;
                        border-radius:5px 5px 0 0;
                        text-transform:uppercase;    
                        text-decoration:none; 
                        display:inline-block; 
        }
        .subMenu:hover{
                                background:#87ceff;
                                color:white;  
        }
        
        .selected{
			background: #11658d;
			padding: 5px 10px 5px;
			color: #fff;
                        font-size: 11px;
			border-top:1px solid #ccc;
                        border-left:1px solid #ccc;
                        border-right:1px solid #ccc;
			line-height: 1.2em;
			cursor: pointer;
                        border-radius:5px 5px 0 0;
                        text-transform:uppercase;    
                        text-decoration:underline; 
                        display:inline-block; 
        }
        .selected:hover{
            font-weight:bolder;
            text-decoration:none;  
        }
    </style>    
      <div class="submenus">
<?php
        $cnsSubMenu=$User->list_primer_opl($ol,$_SESSION[usuid],'');
        $data='';
        $n=0;
        while($rstSubMenu=pg_fetch_array($cnsSubMenu)){
            $n++;
            ?>
                 <span class="subMenu" id="<?php echo 'id_'.$rstSubMenu[opl_id]?>" onclick="lista(this.id,<?php echo $ol?>,'<?php echo $rstSubMenu[opl_direccion]?>')" ><?php echo $rstSubMenu[opl_modulo]?></span>
            <?php
        }
?>     
    </div>
    <script>
    spn=document.getElementById('<?php echo $_REQUEST[sub_id]?>'); 
    spn.className='selected';
    </script>
