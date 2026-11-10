<?php

class ReminderSender{

	public $Data;
	public $Rounded_Reference_DateTimeObject;
	public $ReminderInternvalData;
	public $List;

   function __construct($Data) {
		//parent::__construct();
		$this->Data = $Data;
   }

	/*
	$Data = [
		'Reference_DateTimeObject' => DateTimeObject::createByText('1-7-2017 12:30:15')
	];
	*/
	public static function create($Data){
		return new ReminderSender($Data);
	}
	
	public function execute(){
		
		$this->logWriteParameters();
		
		$this->Rounded_Reference_DateTimeObject = $this->Data['Reference_DateTimeObject']->getRoundedToNearestMinute_DateTimeObject();
		
		
		/*
		'StartDateTime'
		'EndDateTime'
		'ExcludeInterval_DateTime'
		*/
		$Setting = new ReminderTimeSetting();
		$this->ReminderInternvalData = 
			$Setting->getReminderInterval($this->Rounded_Reference_DateTimeObject);
		$this->logWriteReminderInternvalData();
		
		$Parameters = [
			"Test_Ind" => $this->Data['Test_Ind'] ,
			"Check_Has_Already_Email"   => $this->Data['Check_Has_Already_Email'],
			"Start_From_DateTimeObject" => $this->ReminderInternvalData['StartDateTime'],
			"Start_Till_DateTimeObject" => $this->ReminderInternvalData['EndDateTime']
		];
		$this->List = ReminderAppointmentList::create($Parameters);
		$this->List->execute($Parameters);
		
		$this->logWriteListCount();
		
		$Appointment = $this->List->getFirst();
		
		while($Appointment != null){
			
			$Send_Ind = true;
			
			$DateTime = DateTimeObject::createByText('5-5-2005 15:05:05');
			$Appointment->setReminderDateTime( $this->Data['Reference_DateTimeObject'] );
			
			$Book_DateTimeObject = DateTimeObject::createBySqlDateTime($Appointment->Data['appointment_book_datetime']);
			
			//Als boekdatum na de Exluderen datum/tijd
			if($this->Data['Check_ExcludeInterval_DateTime_Ind'] == true){
				
				if($this->Data['Test_Ind'] == true){
					echo '<br>Boekdatum: '.$Book_DateTimeObject->getDateTimeText();
					echo '<br>Excludedatum: '.$this->ReminderInternvalData['ExcludeInterval_DateTime']->getDateTimeText();
				}
				
				if( $Book_DateTimeObject->TimeStamp >= 
					$this->ReminderInternvalData['ExcludeInterval_DateTime']->TimeStamp ){
					
					//Niet versturen
					$Appointment->setReminderReason( 'Afspraak is recent.' );
					$Send_Ind = false;
					echo 'Buiten de tijd';
				}
				else{
					echo 'Binnen de tijd';
				}
			}
			
			if( $Appointment->Data['customer_email'] ==  '' or
			    $Appointment->Data['customer_email'] ==  null ){
				$Appointment->setReminderReason( 'Afspraak is recent.' );
				$Send_Ind = false;
			}
			
			if($Send_Ind == true){
				//Set appointment reminder fields
				$Appointment->setReminderReason( 'Verstuurd naar: ' . $Appointment->Data['customer_email'] );
				$Appointment->setReminderStatusText( 'Verzonden' );
				
				//Send email
				$ReminderDocumentData = $this->fillReminderDocumentData( $Appointment );
				$ReminderDocumentData['Test_Ind'] = $this->Data['Test_Ind'];

				$Email = Email::createByData($ReminderDocumentData);				
				if($this->Data['Send_Email_Ind'] == true){
					$Email->send();
				}
			}
			else{
				$Appointment->setReminderStatusText( 'Niet verzonden' );
			}
			
			if($this->Data['Test_Ind'] == true){
				$Appointment->update();
			}
			
			$this->logWriteAppointment($Appointment);
		
			$Appointment = $this->List->getNext();
			
		}
	
	}
	
	private function fillReminderDocumentData($Appointment){
		
		$Doc = [
			"Subject" => "Pedicure afspraak morgen om ".
				date( 'H:i', strtotime($Appointment->Data['appointment_start_datetime']) ).
				" bij Pedicure in Blaricum",
			"Sender" => [
				"Name" => "Pedicure in Blaricum",
				"Url" => "http://www.pedicureinblaricum.nl",
				"Email" => "info@pedicureinblaricum.nl"
			],
			"Receivers" => [
				"To" => [
					[
						"Name"  => $Appointment->Data['customer_first_name'].' '.$Appointment->Data['customer_last_name'],
						"Email" => $Appointment->Data['customer_email']
					]
				],
				"Cc" => [],
				"Bcc" => []
			],
			"Content" => [
				"Title" => "Herinnering",
				"Message" => "Morgen heten wij u van harte welkom op uw afspraak in onze nieuwe salon.",
				"AppointmentService"    => $Appointment->Data['service_name'],
				"AppointmentProvider"   => $Appointment->Data['provider_first_name'].' '.$Appointment->Data['provider_last_name'],
				"AppointmentStartDate"  => date( 'j-m-Y H:i', strtotime($Appointment->Data['appointment_start_datetime']) ),
				"AppointmentEndDate"    => date( 'j-m-Y H:i', strtotime($Appointment->Data['appointment_end_datetime']) ),
				"CustomerName"          => $Appointment->Data['customer_first_name'].' '.$Appointment->Data['customer_last_name'],
				"CustomerEmail"         => $Appointment->Data['customer_email'],
				"CustomerPhone"         => $Appointment->Data['customer_phone_number'],
				"CustomerAddress"       => "",
				"AppointmentLink"       => Config::BASE_URL."/index.php/appointments/index/".$Appointment->Data['appointment_hash']
			]
		];
		
		if($this->Data['Send_Email_To_Test_Receiver_Accounts_Ind'] == true){
			$Doc['Receivers']['To'] = [];
			$Doc['Receivers']['To'] = $this->Data['Test_Receiver_Accounts'];
		}
		
		$Doc['Receivers']['Bcc'][] = [
			"Name" => 'Alwin van de Put - Bcc-er',
			"Email" => 'alwinvandeput@gmail.com'
		];
	
		return $Doc;
	}
	
	private function logWriteParameters(){
		if($this->Data['Log_Ind'] == true){
			echo "*********************************************************************";
			echo "\nPedicure in Blaricum - Reminder emails";
			echo "\n*********************************************************************";
			echo "\nSystem time zone          : " . date_default_timezone_get();
			echo "\nReference Date/Time       : " . $this->Data['Reference_DateTimeObject']->getText();
			echo "\nTest indicator            : " . $this->Data['Test_Ind'];
			echo "\nLog indicator             : " . $this->Data['Log_Ind'];
			echo "\nUpdate indicator          : " . $this->Data['Update_Ind'];
			echo "\nSend email indicator      : " . $this->Data['Send_Email_Ind'];
			echo "\nSend email to test acc.   : " . $this->Data['Send_Email_To_Test_Receiver_Accounts_Ind'];
			echo "\nExclude late bookings ind.: " . $this->Data['Check_ExcludeInterval_DateTime_Ind'];

			echo "\n";
			echo "\nCurrent directory         :". getcwd ()."\n";

			echo "\nTest email receivers: ";
			foreach($this->Data['Test_Receiver_Accounts'] as $Receiver_Account){
				echo "\n  Email name          : " . $Receiver_Account['Name'];
				echo "\n  Email address       : " . $Receiver_Account['Email'];
			}
			echo "\n";
		}
	}
	
	private function logWriteListCount(){
		$count = count($this->List->Data);
		echo "\n*********************************************************************";
		echo "\nSelected appointments count      : " . $count;
	}
	
	private function logWriteReminderInternvalData(){
		echo "\n*********************************************************************";
		echo "\nStart Date Time    : " . $this->ReminderInternvalData['StartDateTime']->getText();
		echo "\nEnd Date Time      : " . $this->ReminderInternvalData['EndDateTime']->getText();
		echo "\nExclude Date Time  : " . $this->ReminderInternvalData['ExcludeInterval_DateTime']->getText();
	}
	
	private function logWriteAppointment($Appointment){
		$Counter = $this->List->RecordIndex + 1;
		$TimeText  = date('H:i', strtotime($Appointment->Data['appointment_start_datetime']));
		$DateText  = date('d-m-Y', strtotime($Appointment->Data['appointment_start_datetime']));
		echo "\n*********************************************************************";
		echo "\n*** Appointment " . $Counter . " - " . $TimeText . "  (" . $DateText . ")";
		echo "\nId                             : " . $Appointment->Data['appointment_id'];
		echo "\nStart date/time                : " . $Appointment->Data['appointment_start_datetime'];
		echo "\nEnd date/time                  : " . $Appointment->Data['appointment_end_datetime'];
		echo "\nHash                           : " . $Appointment->Data['appointment_hash'];
		echo "\nReminder date/time             : " . $Appointment->Data['appointment_reminder_datetime'];
		echo "\nReminder status                : " . $Appointment->Data['appointment_reminder_statustext'];
		echo "\nReminder reason                : " . $Appointment->Data['appointment_reminder_reasontext'];
		echo "\nService name                   : " . $Appointment->Data['service_name'];
		echo "\nProvider name                  : " . $Appointment->Data['provider_first_name'] . " " . $Appointment->Data['provider_last_name'];
		echo "\nCustomer name                  : " . $Appointment->Data['customer_first_name'] . " " . $Appointment->Data['customer_last_name'];
		echo "\nCustomer email                 : " . $Appointment->Data['customer_email'];
		echo "\nCustomer phone                 : " . $Appointment->Data['customer_phone_number'];
		
	}
	
}

?>