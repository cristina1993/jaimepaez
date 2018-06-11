<?php
//session_start();
if (isset($_POST['do'])) {

    if (!empty($_FILES['archivo']['name'])) {
        $name = $_FILES['archivo']['name'];
        $tmp = $_FILES['archivo']['tmp_name'];
        $file = pathinfo($name);
        echo $url = $_POST[ruta].$name;
    }
    
        switch ($_POST[act]) {
            case 1:
                copy($tmp, $url);
                break;
            case 2:
                break;
            case 3:
                //copy($tmp,$url);
                break;
        }
    
    
}
?>
<form action="index.php" onsubmit=""  autocomplete="off" enctype="multipart/form-data"  method="POST"  >                            
    <table>
        <tr>
            <td>Archivo:</td>
            <td><input type="file" name="archivo" /></td>
        </tr>
        <tr>
            <td>Ruta:</td>
            <td><input type="text" size="100" name="ruta" /></td>
        </tr>
        <tr>
            <td>Accion:</td>
            <td><input type="text" name="act" /></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="do" /></td>
        </tr>
    </table>
</form>    
