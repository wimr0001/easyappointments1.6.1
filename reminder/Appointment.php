<?php

class Appointment{

	public $Data = null;

	public static  function createByReminderData($Data){

		$Instance = new Appointment();
		$Instance->Data = $Data;
		return $Instance;
	}

	public function setReminderDateTime($StatusDateTimeObject){
		$this->Data['ReminderDateTime'] = $StatusDateTimeObject;
	}

	public function setReminderStatusText($StatusText){
		$this->Data['ReminderStatusText'] = $StatusText;
	}

	public function setReminderReason($ReasonText){
		$this->Data['ReminderReasonText'] = $ReasonText;
	}

	public function update(){
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

		$sql = "UPDATE ea_appointments ".
			"SET ".
				"reminder_datetime   = '".$this->Data['ReminderDateTime']->getSqlDateTime() . "', ".
				"reminder_statustext = '".$this->Data['ReminderStatusText'] . "', ".
				"reminder_reasontext = '".$this->Data['ReminderReasonText'] . "' ".
			"WHERE id = ".$this->Data['appointment_id'];

		if ($conn->query($sql) === TRUE) {
			//echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $conn->error;
		}

		$conn->close();
	}

	/*
	private function addColumns(){

		$fields = mysql_list_fields($config::DB_NAME, 'ea_appointments');
		$columns = mysql_num_fields($fields);

		if(!array_key_exists ('reminder_datetime', $columns){

		}
		//$sql = "ALTER TABLE `ea_appointments` ADD `reminder_datetime` DATETIME NULL ;";
		//$sql = "ALTER TABLE `ea_appointments` ADD `reminder_statustext` TEXT NULL ;";
		//$sql = "ALTER TABLE `ea_appointments` ADD `reminder_reasontext` TEXT NULL ;";

	}

	//$TableName = 'ea_appointments', $FieldName = reminder_datetime,
	private function addColumn($TableName, $FieldName, $DataType, $NullValue){
		$sql = "ALTER TABLE `ea_appointments` ADD `reminder_datetime` DATETIME NULL ;";
	}
	*/
}

?>
