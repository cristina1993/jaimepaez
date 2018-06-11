<?php
$archivo = $_GET[archivo];
$file = $archivo;
$arch = explode('.', $archivo);
switch (strtolower($arch[1])) {
    case'txt':
        $ext = "rtf";
        break;
    case'xlsx':
        $ext = "vnd.ms-excel";
        break;
    case'csv':
        $ext = "vnd.ms-excel";
        break;
    case'docx':
        $ext = "msword";
        break;
    case'pdf':
        $ext = "pdf";
        break;
    case'psd':
        $ext = "imagen";
        break;
    case'png':
        $ext = "imagen";
        break;
    case'jpg':
        $ext = "imagen";
        break;
    case'jpeg':
        $ext = "imagen";
        break;
    case'gif':
        $ext = "imagen";
        break;
    case'gif':
        $ext = "imagen";
        break;
    case'zip':
        $ext = "zip";
        break;
    case'rar':
        $ext = "zip";
        break;
    
    default :
        $ext = "postscript";
        break;
}
header("Content-type: application/".$ext);
header("Content-length:" . filesize($file));
header("Content-Disposition: attachment; filename=$archivo");
readfile($file);

?>