<?php
  /**
  * @see https://bootstrapmade.com/php-email-form/
  * TODO sicurezza
  */
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use PHPMailer\PHPMailer\SMTP;

  require_once('../config.php');
  require_once('../assets/vendor/php-email-form/PHPMailer/src/PHPMailer.php');
  require_once('../assets/vendor/php-email-form/PHPMailer/src/Exception.php');
  require_once('../assets/vendor/php-email-form/PHPMailer/src/SMTP.php');

  $contact = new PHPMailer(true);
  $contact->setLanguage('it');
  $contact->isSMTP();
  $contact->Host = $INFO['host'];
  $contact->Port = $INFO['port'];
  $contact->SMTPAuth = true;
  $contact->Username = $INFO['username']; 
  $contact->Password = $INFO['password'];
  $contact->SMTPSecure = $INFO['type'];
  $contact->SMTPKeepAlive = true; // add it to keep SMTP connection open after each email sent
  $contact->SMTPDebug = 0;

  $contact->setFrom($INFO['to_mail'], $INFO['to_name']);
  $contact->Subject = $_POST['subject'];

  /* mail admin */
  $contact->addAddress($INFO['to_mail'], $INFO['to_name']);
  $contact->AddReplyTo($_POST['email'], $_POST['name']);
  $contact->isHTML(true);

  $contact->Body = "<h2>DA: {$_POST['name']}</h2> <p>Richiesta \"{$_POST['subject']}\"</p><br><br><p>{$_POST['message']}</p>";
  $contact->AltBody = "DA {$_POST['name']} \n Richiesta \"{$_POST['subject']}\" \n\n {$_POST['message']}";
  
  try {
      $contact->send();
      $response = "OK";
  } catch (Exception $e) {
      $response = "Mailer Error ({$INFO['to_mail']}) {$contact->ErrorInfo}\n";
  }

  /* OPTIONAL BCC SENDER */
  if(isset($_POST['bcc'])) {
    $contact->clearAddresses();
    $contact->clearReplyTos();
    $contact->addAddress($_POST['email'], $_POST['name']);
    $contact->AddReplyTo($INFO['to_mail'], $INFO['to_name']);

    $contact->Body = "<h2>Gentile {$_POST['name']}</h2> <p>Ho ricevuto la sua richiesta \"{$_POST['subject']}\"</p><br><br><p>{$_POST['message']}</p>";
    $contact->AltBody = "Gentile, {$_POST['name']} \n Ho ricevuto la sua richiesta \"{$_POST['subject']}\" \n\n {$_POST['message']}";
    
    try {
      $contact->send();
    } catch (Exception $e) {
      $response .= "Mailer Error ({$_POST['email']}) {$contact->ErrorInfo}\n";
    }
  }
  $contact->smtpClose();
  
  echo $response;
?>
