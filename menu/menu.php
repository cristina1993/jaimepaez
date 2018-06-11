<?php
session_start();
include_once '../Clases/clsUsers.php';
$usuario = $_SESSION['usuid'];
$User = new User();
$cnsProc = $User->list_proceos($usuario);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <title>Menú</title>
        <script type="text/javascript" src="../js/jquery.js"></script>
        <script>
            function submenu(mn)
            {
                frame = parent.document.getElementById('mainFrame')
                frame.src = '../' + mn
            }
            function miFerfil()
            {
                mn = '../Scripts/erp_perfil.php';
                frame = parent.document.getElementById('mainFrame');
                frame.src = mn;
            }
            function ayuda()
            {
                mn = '../Scripts/Lista_multimedia.php';
                frame = parent.document.getElementById('mainFrame');
                frame.src = mn;
            }

            function resize() {
                doc = parent.document.getElementById('contenedor');
                li = document.getElementsByTagName('li');
                frame = parent.document.getElementById('leftFrame');
                if (btnclose.lang === '0')
                {
                    btnclose.innerHTML = '>>';
                    btnclose.lang = '1';
                    doc.cols = '20,*';
                    for (var i = 1; i < li.length; i++)
                    {
                        li[i].style.visibility = 'hidden';
                    }

                    frame.style.transition = 'all 0.5s ease-out';
                    frame.style.background = '#015b85';
                    frame.style.opacity = 0.4;
                } else {
                    btnclose.innerHTML = '<<';
                    btnclose.lang = '0';
                    doc.cols = '125,*';
                    for (var i = 1; i < li.length; i++)
                    {
                        li[i].style.visibility = 'visible';
                    }
                    frame.style.transition = 'all 0.5s ease-out';
                    frame.style.background = 'none';
                    frame.style.opacity = 1;
                }
            }

        </script>        
        <style>
            @charset "UTF-8";
            body{
                font-family:'Times New Roman'; 
                margin:0px !important; 
                font-size: 11px;
            }
            #accordion {
                position:absolute;
                top: -15px;
                padding: 0 0 0 0;
                cursor:pointer; 
            }
            #accordion > li{
                background-color: #616975;
                filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,StartColorStr='#727a86', EndColorStr='#505864');
                border-bottom: 0.05em solid #33373d;
                border-right:0.001em solid #33373d;
                height: 2em;
                line-height: 2em;
                text-indent: 0.75em;
                display: block;
                position: relative;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: #fff;
                text-shadow: 0px 1px 0px rgba(0,0,0,.5);
                width:120px; 
            }
            #accordion > li:hover{
                font-weight:bold; 
                background: -moz-linear-gradient(top, rgb(41,127,170) 0%, rgb(44,160,202) 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(41,17,170)), color-stop(100%,rgb(44,160,202)));
                background: -webkit-linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
                background: -o-linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
                background: -ms-linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
                background: linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
            }
            #accordion ul {
                list-style: none;
                padding: 0 0 0 0;
                display: none;
            }
            #accordion ul li{
                background: #fff url(bg_form.jpg) repeat-x top left;		
                margin-left:5px;  
                color:#616975;
                border-bottom: 1px solid #33373d;
                height: 1.8em;
                line-height: 1.8em;
                text-indent: 0.75em;
                display: block;
                position: relative;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                border-radius:5px 0px 5px 0px; 
            }
            #accordion .mnu {
                font-weight:normal; 
            }
            #accordion .mnu:hover{ 
                color:#fff;
                background: -moz-linear-gradient(top, rgb(41,127,170) 0%, rgb(44,160,202) 100%);
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(41,17,170)), color-stop(100%,rgb(44,160,202)));
                background: -webkit-linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
                background: -o-linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
                background: -ms-linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
                background: linear-gradient(top, rgb(41,127,170) 0%,rgb(44,160,202) 100%);
            }


            #lock_menu{
                position: absolute;
                background:#f8f8f8;
                opacity: 0.4;
                top:0;
                left:0; 
                width:100%;
                height:100%; 
                cursor:no-drop; 
                z-index:999999; 
                visibility:hidden; 
            }
        </style>
    </head>
    <body >
        <div id="lock_menu" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>  

        <ul id="accordion">
            <li id="btnclose" style="height:25px;" lang="0" onClick="resize()"> << </li>        
            <?php
            $n = 0;
            while ($rstProc = pg_fetch_array($cnsProc)) {
                $n++;
                ?>    
                <li>
                    <?php echo $rstProc[proc_descripcion] ?>
                </li>
                <ul>
                    <?php
                    $cnsMod = $User->list_modulos($rstProc[proc_id], $usuario);
                    while ($rstMod = pg_fetch_array($cnsMod)) {
                        $rstOl = pg_fetch_array($User->list_primer_opl($rstMod[mod_id], $usuario, ' limit 1'));

                        $dir = $rstOl[opl_direccion] . '.php?ol=' . $rstOl[opl_id].'&mod='.$rstOl[mod_id];
                        ?>
                    <li class="mnu" onclick="submenu('<?php echo $dir ?>');" ><?php echo ucwords(strtolower($rstMod[mod_descripcion])) ?></li>                
                        <?php
                    }
                    ?>
                </ul>                                                
                <?php
            }
            ?>
            <li id="perfil" onclick="miFerfil()" > MI PERFIL
                <img src="../img/doc.png" style="position:absolute;right:5px;top:25%" width="15px" height="15px" />
            </li>
            <li id="salir" onclick="frmClose.submit()">
                SALIR
                <form id="frmClose" action="../Validate/closeSession.php" method="POST" target="_parent">                    
                    <img src="../img/b_delete.png" style="position:absolute;right:5px;top:25%" width="15px" height="15px" />
                </form>                    
            </li>
        </ul>    
    </body>
    <script>
        $("#accordion > li").click(function () {

            if (false === $(this).next().is(':visible') && $(this).attr('id') !== 'btnclose' && $(this).attr('id') !== 'perfil' && $(this).attr('id') !== 'salir') {
                $('#accordion > ul').slideUp(100);
            }
            if ($(this).attr('id') !== 'btnclose' && $(this).attr('id') !== 'perfil' && $(this).attr('id') !== 'salir') {
                $(this).next().slideToggle(100);
            }
        });
    </script>

</html>