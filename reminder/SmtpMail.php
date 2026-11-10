<?php

require(__DIR__ . '/../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;

class SmtpMail {

    private $Data;
    private $Document;
    private $headers;

    function __construct($Data) {

        $this->Data = $Data;

        $this->mergeDataToDocument();

        $subject = $this->Data['Subject'];

        if ($this->Data['Test_Ind'] == true) {
            $this->printToScreen();
        }
    }

    public static function createByData($Data) {

        return new SmtpMail($Data);
    }

    public function send() {
        $to = substr($this->headers[2], 4);
        $name = "";
        $pattern = "/</";
        $arr = preg_split($pattern, $to);
        if (count($arr) == 2) {
            $name = $arr[0];
            $to = substr($arr[1], 0, strlen($arr[1]) - 1);
        }
        unset($this->headers[2]);
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->addAddress($to, $name);
        $mail->Timeout = 3000;
        $mail->Host = 'secure.123-hosting-server.biz';
        $mail->Username = 'info@pedicureinkampen.nl';
        $mail->Password = 'Journaal77';  // Journaal77
        $mail->setFrom("herinneringen@pedicureinkampen.nl");
        $mail->addReplyTo("info@pedicureinkampen.nl");
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Port = 465;
// $mail->Host = 'sandbox.smtp.mailtrap.io';
// $mail->Username = 'f02b04425a2d3e';
// $mail->Password = 'd381a8f3e86375';
// $mail->addAddress('info@mailtrap.io', 'Mailtrap');
// $mail->setFrom('info@mailtrap.io', 'Mailtrap');
// $mail->addReplyTo('info@mailtrap.io', 'Mailtrap');

        $mail->Subject = $this->Data['Subject'];
        $mail->isHTML(true);
        $mail->Body = $this->Document;
//$mail->SMTPDebug = 2;
        $result = $mail->send();
        if ($result == false) {
            "<h1>Fout: Email is niet verzonden</h1>";
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return;
        } else {
            if ($this->Data['Test_Ind'] == true) {
                echo "<h1>Succes: Email is verzonden.</h1>";
            }
            return;
        }
    }

    public function printToScreen() {

        echo "<H1>Test Email</H1>";

        echo "<H2>Subject</H2>";
        echo $this->Data['Subject'];

        echo "<H2>Headers</H2>";
        foreach ($this->headers as $header) {
            echo '<br>';
        }

        echo "<H2>Body</H2>";
        echo $this->Document;
    }

    private function mergeDataToDocument() {

        $data = [];
        ob_start();
        $data['appointment_hash'] = $this->Data['Content']['AppointmentHash'];
        $data['company_name'] = $this->Data['Sender']['Name'];
        $data['company_link'] = $this->Data['Sender']['Url'];
        $data['email_title'] = $this->Data['Content']['Title'];
        $data['email_message'] = $this->Data['Content']['Message'];
        $data['service_private'] = $this->Data['Content']['ServicePrivate'];
        $data['appointment_provider'] = $this->Data['Content']['AppointmentProvider'];
        $data['appointment_service'] = $this->Data['Content']['AppointmentService'];
        $data['appointment_start_date'] = $this->Data['Content']['AppointmentStartDate'];
     //   $data['appointment_end_date'] = $this->Data['Content']['AppointmentEndDate'];
        $data['customer_name'] = $this->Data['Content']['CustomerName'];
        $data['customer_email'] = $this->Data['Content']['CustomerEmail'];
        $data['customer_phone'] = $this->Data['Content']['CustomerPhone'];
        extract($data);
        
        include('appointment_details.html');
        $this->Document = ob_get_clean();

        // To send HTML mail, the Content-type header must be set
        $this->headers[] = 'MIME-Version: 1.0';
        $this->headers[] = 'Content-Type: text/html; charset=iso-8859-1';
        //Add receivers
        $this->addReceivers('To');
        $this->addReceivers('Cc');
        $this->addReceivers('Bcc');

        //Add sender
        $this->headers[] = 'From: ' . $this->Data['Sender']['Name'] . ' <' . $this->Data['Sender']['Email'] . '>';
    }

    //ToType = To, Cc, Bcc
    function addReceivers($ToType) {

        $ReceiversText = $ToType . ': ';

        $ReceiverCounter = 0;

        foreach ($this->Data['Receivers'][$ToType] as $Receiver) {

            $ReceiverCounter = $ReceiverCounter + 1;

            if ($ReceiverCounter > 1) {
                $ReceiversText = $ReceiversText . ', ';
            }
            $ReceiversText = $ReceiversText . $Receiver['Name'] . ' <' . $Receiver["Email"] . '>';
        }
        if ($ReceiverCounter > 0) {
            $this->headers[] = $ReceiversText;
            //	var_dump($this->headers);die;
        }
    }

}
