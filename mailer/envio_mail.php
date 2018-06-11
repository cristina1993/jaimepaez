<?php
      require "../mailer/class.phpmailer.php";
      $mail = new phpmailer();
      $mail->PluginDir = "../mailer/";
      $mail->Mailer = "smtp";
      $mail->Port =587;
      $mail->SMTPAuth = true;
      $mail->Host = "mail.scpgkv.com";
      $mail->Username = "info@scpgkv.com";
      $mail->Password = "SuremandaS492";
      $mail->From = "info@scpgkv.com";
      $mail->FromName = "SCP";
      $mail->Timeout=5;
      $mail->AddAddress("holgerhcdj@hotmail.com", "Holger Caiza");
      $mail->Subject = "Prueba de envio de mail servidor";
      $mensaje="Prueba de envio de mail xxx";
      $mail->Body = $mensaje;
      $mail->AltBody = $mensaje;
      $exito = $mail->Send();
      if($exito){
          echo 'enviado';
      }else{
          echo $mail->ErrorInfo;
      }
?>

     