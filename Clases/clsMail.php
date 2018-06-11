<?php

require "../mailer/class.phpmailer.php";

//echo "$dat[0]---$dat[1]---$dat[2]---$dat[3]---$dat[4]---$dat[5]---$dat[6]---$dat[7]";
class Mail extends PHPMailer {

    function envia_correo($mail, $name, $file, $ndoc, $tp) {
        include_once '../Clases/clsUsers.php';
        $User = new User();
        $rst = pg_fetch_array($User->lista_configuraciones('8'));
        $dat = explode('&', $rst[con_valor2]);
        switch ($tp) {
            case 1:$doc = 'Factura';
                break;
            case 4:$doc = 'Nota de Credito';
                break;
            case 5:$doc = 'Nota de Debito';
                break;
            case 6:$doc = 'Guia de Remision';
                break;
            case 7:$doc = 'Retencion';
                break;
        }
        $sms = 0;
        $this->PluginDir = "../mailer/";
        $this->Mailer = "smtp";
        $this->SMTPAuth = true;
        $this->IsHTML(true);
        $this->IsSMTP();
//NOPERTI ******************************************************************************
//        $this->SMTPSecure = "$dat[0]";
        $this->Port = "$dat[1]";
        $this->Host = "$dat[2]";
        $this->Username = "$dat[3]";
        $this->Password = "$dat[4]";
        $this->From = "$dat[3]";
//*************************************************************************************
        $mls = explode(';', $mail);
        $x = 0;
        while ($x < count($mls)) {
            $this->AddAddress($mls[$x], $name);
            $x++;
        }

        $this->FromName = "$dat[5]";
//        $this->AddBCC('crisrosxx@gmail.com', 'CRIS');
        $this->AddBCC('gabokatz@hotmail.com', 'gabo');
        $this->Subject = $doc . " Cliente : " . $name . " No: " . $ndoc;
        $this->Subject = "$dat[6]";
//        $this->AddEmbeddedImage('../img/tikva_logo.jpg', 'logo_tivka');
//        $mensaje = "<html>
//            <body>
//            <table>
//            <tr><td>Estimado cliente</td></tr>
//            <tr><td>" . utf8_decode('Estimado cliente
//Le informamos que ha sido generado y autorizado por el SRI un comprobante electrónico, que se encuentra disponible para descargarlo a través de nuestro portal www.tivkasystem.com
//' . $doc . ' : ' . $ndoc . '
//Gracias por preferirnos
//') . "</td></tr>
//            <tr><td><br><br>Derechos Reservados  <a href='https://www.tivkasystem.com'>TIKVASYSTEMS</a></td></tr>
//            </table>
//            </body>
//            </html>";

        $mensaje = "$dat[7]";
        $this->MsgHTML(utf8_decode($mensaje));
        $this->AltBody = utf8_decode("Estimado cliente,
                          Es un gusto recordarle que esta ORGANIZACION, por disposición del SRI,  está emitiendo  todas sus facturas de manera electrónica  adjuntas en este correo, por lo que usted puede ingresar a nuestro portal web www.gruponoperti.com para descargar su factura.
                          Gracias por confiar en nosotros.
                          
                          Este mail no debe ser respondido");
        $n = 0;
        while ($n < count($file)) {
            $this->AddAttachment($file[$n]);
            $n++;
        }

        if (!$this->Send()) {
            $sms = $this->ErrorInfo;
        }
        return $sms;
    }

}

?>
