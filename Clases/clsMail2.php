<?php
require "../mailer/class.phpmailer.php";

class Mail extends PHPMailer {

    function envia_correo($mail, $name, $file, $ndoc, $tp) {
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
        $this->SMTPSecure = "";
        $this->Port = 587;
        $this->Host = "mail.gruponoperti.com";
//       $this->Username = "proveedores@gruponoperti.com";
//       $this->Password = "@2015noperti70";
        $this->Username = "facturaelectronica@gruponoperti.com";
        $this->Password = "@2015noperti17";

        $this->From = "facturaelectronica@gruponoperti.com";
//*************************************************************************************
        $mls = explode(';', $mail);
        $x = 0;
        while ($x < count($mls)) {
            $this->AddAddress($mls[$x], $name);
            $x++;
        }

        $this->FromName = "Tivka";
        $this->AddBCC('crisrosxx@gmail.com', 'CRIS');
        $this->Subject = $doc . " Cliente : " . $name . " No: " . $ndoc;
        $this->AddEmbeddedImage('../img/tikva_logo.jpg', 'logo_tivka');
        $mensaje = "<html>
            <body>
            <table>
            <tr><td>Estimado cliente</td></tr>
            <tr><td>" . utf8_decode('Estimado cliente
Le informamos que ha sido generado y autorizado por el SRI un comprobante electrónico, que se encuentra disponible para descargarlo a través de nuestro portal www.tivkasystem.com
'.$doc.' : ' . $ndoc . '
Gracias por preferirnos
') . "</td></tr>
            <tr><td><br><br>Derechos Reservados  <a href='https://www.tivkasystem.com'>TIKVASYSTEMS</a></td></tr>
            </table>
            </body>
            </html>";

        $this->MsgHTML($mensaje);
        $this->AltBody = utf8_decode("Estimado cliente,
                          Es un gusto recordarle que esta ORGANIZACION, por disposición del SRI,  está emitiendo  todas sus facturas de manera electrónica  adjuntas en este correo, por lo que usted puede ingresar a nuestro portal web www.gruponoperti.com para descargar su factura.
                          Gracias por confiar en nosotros.

                          Este mail no debe ser respondido, para cualquier información llame al 022449696 con la señora Raquel Cedeño  ó escribanos al email  rcedeno@gruponoperti.com  con copia a  jsanchez@gruponoperti.com");
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
