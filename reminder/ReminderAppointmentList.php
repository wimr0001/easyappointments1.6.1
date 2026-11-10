<?php

class ReminderAppointmentList{

	public $Parameters;
	public $Data = [];
	public $Sql;
	public $RecordIndex = 0;

	public static function create($Parameters){
		$Instance = new ReminderAppointmentList();
		return $Instance;
	}

	/*
	$Parameters = [
		"Start_From_DateTimeObject" => DateTimeObject::createByText('25-09-2017 18:00:00'),
		"Start_Till_DateTimeObject" => DateTimeObject::createByText('25-09-2017 20:00:00')
	];
	*/
	public function execute($Parameters){

		$config = new Config();

		$servername = $config::DB_HOST;
		$username = $config::DB_USERNAME;
		$password = $config::DB_PASSWORD;
		$dbname = $config::DB_NAME;

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		echo "\nTest" . $Parameters['Start_From_DateTimeObject']->getSqlDateTime();

		//query
		$this->Sql = "SELECT ".
			"app.id                   as appointment_id, ".
			"app.book_datetime        as appointment_book_datetime, ".
			"app.start_datetime       as appointment_start_datetime, ".
			"app.end_datetime         as appointment_end_datetime, ".
			"app.hash                 as appointment_hash, ".
			"app.reminder_datetime    as appointment_reminder_datetime, ".
			"app.reminder_statustext  as appointment_reminder_statustext, ".
			"app.reminder_reasontext  as appointment_reminder_reasontext, ".
			"service.name             as service_name, ".
                        "service.is_private       as service_private, ".
			"provider.first_name      as provider_first_name, ".
			"provider.last_name       as provider_last_name, ".
			"cust.first_name          as customer_first_name, ".
			"cust.last_name           as customer_last_name, ".
			"cust.email               as customer_email, ".
			"cust.phone_number        as customer_phone_number ".
			"FROM ea_appointments as app ".
			"INNER JOIN ea_users as cust ".
				"ON cust.id = app.id_users_customer ".
			"INNER JOIN ea_users as provider ".
				"ON provider.id = app.id_users_provider ".
			"INNER JOIN ea_services as service ".
				"ON service.id = app.id_services ";

		$WhereSql =
			"WHERE app.start_datetime >= '". $Parameters['Start_From_DateTimeObject']->getSqlDateTime() ."' ".
			  "AND app.start_datetime <  '". $Parameters['Start_Till_DateTimeObject']->getSqlDateTime() ."' ".
			  "AND app.is_unavailability = 0 "
			;
		if($Parameters['Check_Has_Already_Email'] == true){
			$WhereSql = $WhereSql . " " .
				"AND app.reminder_datetime IS NULL";
		}
		else{
			echo "\nWarning:The Check_Has_Already_Email is off!!!";
		}

		$this->Sql = $this->Sql . $WhereSql . " ORDER BY app.start_datetime";

		//Result
		$result = $conn->query($this->Sql);

		//Fetch
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {

				$this->Data[] = $row;

			}
		} else {
			//echo "0 results";
		}

		$conn->close();
	}

	public function getFirst(){
		$this->RecordIndex = 0;
		if( count($this->Data) <= $this->RecordIndex){
			return null;
		}
		return Appointment::createByReminderData($this->Data[$this->RecordIndex]);
	}

	public function getNext(){
		$this->RecordIndex = $this->RecordIndex + 1;
		if( count($this->Data) <= $this->RecordIndex){
			return null;
		}
		return Appointment::createByReminderData($this->Data[$this->RecordIndex]);
	}

}

?>
