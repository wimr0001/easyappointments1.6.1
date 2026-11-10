<?php

	//Cron job
	//Tijd:
	//- Minuut: 0
	//- Uur: */1
	/*- Commando:
	 /usr/local/bin/php /home/domains/pedicureinkampen.nl/public_html/afspraak/reminder/ReminderSender_JobProgram.php
	*/

	date_default_timezone_set('Europe/Amsterdam');

	header("Content-Type: text/plain");

	include "../config.php";
	include "DateTimeObject.php";
	include "Appointment.php";
	include "ReminderAppointmentList.php";
	include "ReminderTimeSetting.php";
	include "Email.php";
	include "ReminderSender_Kampen.php";

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);


	ini_set('xdebug.var_display_max_depth', 5);
	ini_set('xdebug.var_display_max_children', 256);
	ini_set('xdebug.var_display_max_data', 1024);

	/*TODO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

	// if(Config::BASE_URL == 'http://localhost:81/easyappointments'){
	// 	//$Reference_DateTimeObject = DateTimeObject::createByText('27-09-2017 09:31:15');

	// }
	// else{
	// 	$Reference_DateTimeObject = DateTimeObject::createByText('11-11-2017 08:31:15');
	// }
	$Reference_DateTimeObject = DateTimeObject::createByCurrentDateTime();


	$Data = [
		'Reference_DateTimeObject' => $Reference_DateTimeObject,
		'Test_Ind' => false,                                       //false
		'Log_Ind' => true,                                         //true
		'Update_Ind' => true,                                      //true
		'Send_Email_Ind' => true,                                  //true
		'Send_Email_To_Test_Receiver_Accounts_Ind' => false,       //false
		'Check_Has_Already_Email' => true,                        //true!!!!
		'Check_ExcludeInterval_DateTime_Ind' => false,             //false
		'Test_Receiver_Accounts' => [
			[
				"Name"  => "Herinnering",
				"Email" => 'herinneringen@pedicureinkampen.nl'

			]
		]
	];

	$Sender = ReminderSender::create($Data);

	$Sender->execute();

?>
