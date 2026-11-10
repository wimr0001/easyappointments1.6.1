<?php

class Email{

	private $Data;
	private $Document;
	private $headers;

   function __construct($Data) {
		$this->Data = $Data;

		$this->mergeDataToDocument();

		$subject = $this->Data['Subject'];

		if($this->Data['Test_Ind'] == true){
			$this->printToScreen();
		}
   }

	public static function createByData($Data){

		return new Email($Data);;

	}

	public function send(){
		$to = $this->headers[2];
		unset($this->headers[2]);

		$result = mail($to, $this->Data['Subject'], $this->Document, implode("\r\n", $this->headers));
		if($result == false){
			"<h1>Fout: Email is niet verzonden</h1>";
		}
		else{
			if($this->Data['Test_Ind'] == true){
				echo "<h1>Succes: Email is verzonden.</h1>";
			}
		}
	}

	public function printToScreen(){

		echo "<H1>Test Email</H1>";

		echo "<H2>Subject</H2>";
		echo $this->Data['Subject'];

		echo "<H2>Headers</H2>";
		foreach($this->headers as $header){
	//		var_dump($header);
			echo '<br>';
		}

		echo "<H2>Body</H2>";
		echo $this->Document;


	}

	private function mergeDataToDocument(){

		$this->Document = file_get_contents('appointment_details.php');

		$this->Document = str_replace('Appointment Details'     , 'Afspraak Details', $this->Document);
		$this->Document = str_replace('Service'                 , 'Behandeling', $this->Document);
		$this->Document = str_replace('Provider'                , 'Pedicure', $this->Document);
		$this->Document = str_replace('Start'                   , 'Begin', $this->Document);
		$this->Document = str_replace('End'                     , 'Einde', $this->Document);

		$this->Document = str_replace('Customer Details'        , 'Klantgegevens', $this->Document);
		$this->Document = str_replace('Name'                    , 'Naam', $this->Document);
		$this->Document = str_replace('Email'                   , 'E-mail', $this->Document);
		$this->Document = str_replace('Phone'                   , 'Telefoon', $this->Document);
		$this->Document = str_replace('Address'                 , 'Adres', $this->Document);

		$this->Document = str_replace('Appointment Link'        , 'Wijzig hier uw afspraak', $this->Document);


		$this->Document = str_replace('$company_name'           , $this->Data['Sender']['Name'], $this->Document);
		$this->Document = str_replace('$company_link'           , $this->Data['Sender']['Url'], $this->Document);
		$this->Document = str_replace('$email_title'            , $this->Data['Content']['Title'], $this->Document);
		$this->Document = str_replace('$email_message'          , $this->Data['Content']['Message'], $this->Document);
		$this->Document = str_replace('$appointment_service'    , $this->Data['Content']['AppointmentService'], $this->Document);
		$this->Document = str_replace('$appointment_provider'   , $this->Data['Content']['AppointmentProvider'], $this->Document);
		$this->Document = str_replace('$appointment_start_date' , $this->Data['Content']['AppointmentStartDate'], $this->Document);
		$this->Document = str_replace('$appointment_end_date'   , $this->Data['Content']['AppointmentEndDate'], $this->Document);
		$this->Document = str_replace('$customer_name'          , $this->Data['Content']['CustomerName'], $this->Document);
		$this->Document = str_replace('$customer_email'         , $this->Data['Content']['CustomerEmail'], $this->Document);
		$this->Document = str_replace('$customer_phone'         , $this->Data['Content']['CustomerPhone'], $this->Document);
		$this->Document = str_replace('$customer_address'       , $this->Data['Content']['CustomerAddress'], $this->Document);
		$this->Document = str_replace('$appointment_link'       , $this->Data['Content']['AppointmentLink'], $this->Document);
		$this->Document = str_replace("<?= ", "", $this->Document);
		$this->Document = str_replace(" ?>", "", $this->Document);
		// To send HTML mail, the Content-type header must be set
		$this->headers[] = 'MIME-Version: 1.0';
		$this->headers[] = 'Content-type: text/html; charset=iso-8859-1';

		//Add receivers
		$this->addReceivers('To');
		$this->addReceivers('Cc');
		$this->addReceivers('Bcc');

		//Add sender
		$this->headers[] = 'From: ' . $this->Data['Sender']['Name'] . ' <' . $this->Data['Sender']['Email'] . '>';

		return $this->Document;
	}

	//ToType = To, Cc, Bcc
	function addReceivers($ToType){

		$ReceiversText = $ToType.': ';
		$ReceiverCounter = 0;

		foreach( $this->Data['Receivers'][$ToType] as $Receiver ){

			$ReceiverCounter = $ReceiverCounter + 1;

			if($ReceiverCounter > 1){
				$ReceiversText = $ReceiversText . ', ';
			}
			$ReceiversText = $ReceiversText . $Receiver['Name'] . ' <' . $Receiver["Email"] . '>';

		}
		if($ReceiverCounter > 0 ){
			$this->headers[] = $ReceiversText;
		}

	}

}

?>
