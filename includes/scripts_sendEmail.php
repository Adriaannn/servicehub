<?php
define('BASEPATH',dirname(__DIR__));
require BASEPATH.'/vendor/phpmailer/phpmailer/src/Exception.php';
require BASEPATH.'/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require BASEPATH.'/vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private $mail;

    public function __construct() {
        $this->setupEmail();
    }

    private function setupEmail() {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'official.easyserve@gmail.com';
        $this->mail->Password = 'uxlsnitswehqkhdp'; // Remember to use your real app password here
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port = 465;
        $this->mail->setFrom('official.easyserve@gmail.com', 'EasyServe');
        $this->mail->addReplyTo('official.easyserve@gmail.com');
        // Disable SSL verification - only for testing, remove this for production
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $this->mail->SMTPDebug = 0; // Set to 0 for production
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }

    public function sendEmail($sendTo, $message) {
        try {
            $this->mail->addAddress($sendTo);
            $this->mail->Subject = 'EasyServe Notification';
            $this->mail->Body = $message;
            $this->mail->send();
        } catch (Exception $e) {
        	// Re-throw the exception to be caught by the caller
        	throw new Exception('Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo);
        }
    }
}
