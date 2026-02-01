<?php
/**
 * PHP Email Form
 * https://bootstrapmade.com/php-email-form/
 */

class PHP_Email_Form {

  public $to = '';
  public $from_name = '';
  public $from_email = '';
  public $subject = '';
  public $smtp = array();
  public $ajax = false;
  private $messages = array();

  public function add_message($message, $label = '', $priority = 0) {
    $this->messages[] = array(
      'text' => $message,
      'label' => $label,
      'priority' => $priority
    );
  }

  public function send() {
    if (empty($this->to)) {
      return $this->json_response('Please set the email address to send to.', 0);
    }

    if (empty($this->from_email)) {
      return $this->json_response('Please set the sender email address.', 0);
    }

    if (empty($this->subject)) {
      return $this->json_response('Please set the email subject.', 0);
    }

    if (empty($this->messages)) {
      return $this->json_response('Message is empty.', 0);
    }

    // Prepare headers
    $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
    $headers .= "Reply-To: {$this->from_email}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Prepare body
    $body = $this->prepare_email_body();

    // Send email
    if (!empty($this->smtp)) {
      $result = $this->send_via_smtp($body, $headers);
    } else {
      $result = mail($this->to, $this->subject, $body, $headers);
    }

    if ($result) {
      return $this->json_response('OK', 1);
    } else {
      return $this->json_response('Email sending failed.', 0);
    }
  }

  private function prepare_email_body() {
    $body = '';
    foreach ($this->messages as $message) {
      if (!empty($message['label'])) {
        $body .= "<strong>{$message['label']}:</strong> {$message['text']}<br><br>";
      } else {
        $body .= "{$message['text']}<br><br>";
      }
    }
    return $body;
  }

  private function send_via_smtp($body, $headers) {
    if (empty($this->smtp['host']) || empty($this->smtp['username']) || empty($this->smtp['password'])) {
      return false;
    }

    $smtp_host = $this->smtp['host'];
    $smtp_username = $this->smtp['username'];
    $smtp_password = $this->smtp['password'];
    $smtp_port = !empty($this->smtp['port']) ? $this->smtp['port'] : 587;

    // Note: PHPMailer or similar library would be better for production use
    // This is a simplified version. For production, use PHPMailer
    return false; // SMTP not fully implemented without external library
  }

  private function json_response($message, $status) {
    if ($this->ajax) {
      if (is_array($message)) {
        echo json_encode($message);
      } else {
        if ($status) {
          echo 'OK';
        } else {
          echo $message;
        }
      }
    } else {
      if ($status) {
        echo 'OK';
      } else {
        echo $message;
      }
    }
    exit;
  }
}
?>
