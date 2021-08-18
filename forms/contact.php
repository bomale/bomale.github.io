<?php
  /**
  * @see https://bootstrapmade.com/php-email-form/
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
  $contact->CharSet = PHPMailer::CHARSET_UTF8;
  $contact->SMTPAuth = true;
  $contact->Username = $INFO['username']; 
  $contact->Password = $INFO['password'];
  $contact->SMTPSecure = $INFO['type'];
  $contact->SMTPKeepAlive = true; // add it to keep SMTP connection open after each email sent
  $contact->SMTPDebug = 0;

  $contact->setFrom($INFO['to_mail'], $INFO['to_name']);
  $name = substr(strip_tags($_POST['name']), 0, 255);
  $subject = substr(strip_tags($_POST['subject']), 0, 255);
  $email = PHPMailer::validateAddress($_POST['email']) ? $_POST['email'] : '';
  $plainMessage = htmlspecialchars( $_POST['message'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );
  $message = nl2br( $message );
  $contact->Subject = $subject;

  /* mail admin */
  $contact->addAddress($INFO['to_mail'], $INFO['to_name']);
  $contact->AddReplyTo($email, $name);
  $contact->isHTML(true);

  $contact->Body = "<h2>DA: {$name}</h2> <p>Richiesta \"{$subject}\"</p><br><br><p>{$message}</p>";
  $contact->AltBody = "DA {$name} \n Richiesta \"{$subject}\" \n\n {$plainMessage}";
  $contact->AddCustomHeader( "X-Confirm-Reading-To: $email" );
  $contact->AddCustomHeader( "Return-Receipt-To: $email" );
  $contact->AddCustomHeader( "Disposition-Notification-To: $email" );

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
    $contact->addAddress($email, $name);
    $contact->AddReplyTo($INFO['to_mail'], $INFO['to_name']);

    $contact->Body = "<h2>Gentile {$name}</h2> <p>Ho ricevuto la sua richiesta \"{$subject}\"</p><br><br><p>{$message}</p>";
    $contact->AltBody = "Gentile, {$name} \n Ho ricevuto la sua richiesta \"{$subject}\" \n\n {$plainMessage}";
    
    try {
      $contact->send();
    } catch (Exception $e) {
      $response .= "Mailer Error ({$email}) {$contact->ErrorInfo}\n";
    }
  }
  $contact->smtpClose();
  
  echo $response;
?>
