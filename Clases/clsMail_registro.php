<?php

require "../mailer/class.phpmailer.php";

class Mail extends PHPMailer {

    function envia_correo_reg($mail, $name, $empresa,$comentarios) {
        $sms = 0;
        $this->PluginDir = "../mailer/";
        $this->Mailer = "smtp";
        $this->Port = 587;
        $this->SMTPAuth = true;
        $this->Host = "mail.scpgkv.com";
        $this->Username = "info@scpgkv.com";
        $this->Password = "SuremandaS492";
        $this->From = "info@scpgkv.com";
        $this->FromName = "SCP";
        $this->Timeout = 5;
//        $this->AddCC("facturacion@gruponoperti.com");
        $this->AddBCC("info@scpgkv.com");
        $this->AddAddress($mail, $name);
        $this->Subject = "Registro de solicitud de modulos: " . $name . " de la Empresa: " . $empresa;
        $mensaje = "<html>
            <body>
            <table>
            <tr><td><img src='../img/logo_tivka.png' /></td></tr>
            <tr><td>Registro de solicitud de modulos:</td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td> Nombre: ". utf8_decode($name)."</td></tr>
            <tr><td> Empresa: ". utf8_decode($empresa)."</td></tr>
            <tr><td> Email: ". utf8_decode($mail)."</td></tr>
            <tr><td> Comentarios: ". utf8_decode($comentarios)."</td></tr>
            <tr><td><br><br><br><br><br>
            <br>Derechos Reservados TIKVASYSTEMS www.scpgkv.com</td></tr>
            </table>
            </body>
            </html>";

        $this->MsgHTML($mensaje);
        $this->AltBody = utf8_decode("Este mail no debe ser respondido");
        $n = 0;
        if (!$this->Send()) {
            $sms = $this->ErrorInfo;
        }
        return $sms;
    }

}

?>
