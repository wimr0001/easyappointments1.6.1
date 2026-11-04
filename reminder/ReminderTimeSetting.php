<?php
class ReminderTimeSetting
{
	var $List = [];

   function __construct() {

		$this->addTime('00:00', -1, '00:00', -1, '00:00');
		$this->addTime('01:00', -1, '01:00', -1, '00:00');
		$this->addTime('02:00', -1, '02:00', -1, '00:00');
		$this->addTime('03:00', -1, '03:00', -1, '00:00');
		$this->addTime('04:00', -1, '04:00', -1, '00:00');
		$this->addTime('05:00', -1, '05:00', -1, '00:00');
		$this->addTime('06:00', -1, '06:00', -1, '00:00');
		$this->addTime('07:00', -1, '07:00', -1, '00:00');
		$this->addTime('08:00', -1, '08:00', -1, '00:00');
		$this->addTime('09:00', -1, '09:00', -1, '00:00');
		$this->addTime('10:00', -1, '10:00', -1, '00:00');
		$this->addTime('11:00', -1, '11:00', -1, '00:00');
		$this->addTime('12:00', -1, '12:00', -1, '00:00');
		$this->addTime('13:00', -1, '13:00', -1, '00:00');
		$this->addTime('14:00', -1, '14:00', -1, '00:00');
		$this->addTime('15:00', -1, '15:00', -1, '00:00');
		$this->addTime('16:00', -1, '16:00', -1, '00:00');
		$this->addTime('17:00', -1, '17:00', -1, '00:00');
		$this->addTime('18:00', -1, '18:00', -1, '00:00');
		$this->addTime('19:00', -1, '19:00', -1, '00:00');
		$this->addTime('20:00', -1, '20:00', -1, '00:00');
		$this->addTime('21:00', -1, '21:00', -1, '00:00');
		$this->addTime('22:00', -1, '22:00', -1, '00:00');
		$this->addTime('23:00', -1, '23:00', -1, '00:00');

   }

	public function getReminderInterval($RequestDateTimeObject){

		$Setting = $this->getReminderSetting($RequestDateTimeObject);

		$RequestTime = DateTimeObject::createByTime_HH_MM(
			$RequestDateTimeObject->getHours(),
			$RequestDateTimeObject->getMinutes() );

		$Interval = [];

		//Start Date Time
		if($RequestTime->TimeStamp > $Setting['AppointmentStartTime']->TimeStamp &&
			$Setting['ReminderDays'] == 0){
			$AppointmentStartTime = $RequestTime;
		}else{
			$AppointmentStartTime = $Setting['AppointmentStartTime'];
		}
		$Internval['StartDateTime'] = DateTimeObject::createByNumbers(
			$AppointmentStartTime->getHours(),
			$AppointmentStartTime->getMinutes(),
			0,
			$RequestDateTimeObject->getDay(),
			$RequestDateTimeObject->getMonth(),
			$RequestDateTimeObject->getYear());

		//- Add days
		$AddDays = $Setting['ReminderDays'] * -2; //wro
		$Internval['StartDateTime']->addDays($AddDays);

		//End Date Time
		$Internval['EndDateTime'] = DateTimeObject::createByNumbers(
			$Setting['AppointmentEndTime']->getHours(),
			$Setting['AppointmentEndTime']->getMinutes(),
			0,
			$RequestDateTimeObject->getDay(),
			$RequestDateTimeObject->getMonth(),
			$RequestDateTimeObject->getYear());
		
		//- Add days
		$AddDays = $Setting['ReminderDays'] * -2 + $Setting['AppointmentEndDays']; //wro
		$Internval['EndDateTime']->addDays($AddDays);

		//Exclude Date Time
		$Internval['ExcludeInterval_DateTime'] = DateTimeObject::createByNumbers(
			$Setting['ExcludeIntervalTime']->getHours(),
			$Setting['ExcludeIntervalTime']->getMinutes(),
			0,
			$RequestDateTimeObject->getDay(),
			$RequestDateTimeObject->getMonth(),
			$RequestDateTimeObject->getYear());

		//- Add days
		$AddDays = ($Setting['ReminderDays'] * -1) + $Setting['ExcludeInternvalDays'];
		$Internval['ExcludeInterval_DateTime']->addDays($AddDays);

		return $Internval;

	}

	public function getReminderSetting($Request_DateTimeObject){

		$RequestHours   = $Request_DateTimeObject->getHours();
		$RequestMinutes = $Request_DateTimeObject->getMinutes();

		$RequestTime_DateTimeObject =
			DateTimeObject::createByTimeNumbers($RequestHours, $RequestMinutes);

		$SelectedRow = null;

		foreach ($this->List as $Row) {

			if($RequestTime_DateTimeObject->TimeStamp >= $Row['ReminderTime']->TimeStamp){
				if($SelectedRow == null){
					$SelectedRow = $Row;
					continue;
				}

				if($Row['ReminderTime'] < $SelectedRow['ReminderTime']){
					$SelectedRow['AppointmentEndTime'] = $Row['AppointmentStartTime'];
					$SelectedRow['AppointmentEndDays'] = 0;
					return $SelectedRow;
				}

				$SelectedRow = $Row;
			}else{
				if($SelectedRow == null){
					continue;
				}else{
					$SelectedRow['AppointmentEndTime'] = $Row['AppointmentStartTime'];
					$SelectedRow['AppointmentEndDays'] = 0;
					return $SelectedRow;
				}
			}

			if($Row['ReminderTime']->TimeStamp > $SelectedRow['ReminderTime']->TimeStamp){
				$SelectedRow = $Row;
			}
		}

		$SelectedRow['AppointmentEndTime'] = DateTimeObject::createByTimeText_HH_MM("00:00");
		$SelectedRow['AppointmentEndDays'] = +1;
		return $SelectedRow;
	}



	private function addTime(
		$AppointmentStartTimeText,
		$ReminderDays,
		$ReminderTimeText,
		$ExcludeIntervalDays,
		$ExcludeIntervalTimeText
	){
		$row = [];
		$row['AppointmentStartTime'] =
			DateTimeObject::createByTimeText_HH_MM($AppointmentStartTimeText);
		$row['ReminderDays'] =
			$ReminderDays;
		$row['ReminderTime'] =
			DateTimeObject::createByTimeText_HH_MM($ReminderTimeText);
		$row['ExcludeInternvalDays'] =
			$ExcludeIntervalDays;
		$row['ExcludeIntervalTime'] =
			DateTimeObject::createByTimeText_HH_MM($ExcludeIntervalTimeText);

		$this->List[] = $row;
	}

	/*
	private function convertTimeTextToDateTime($TimeText){
		$pieces = explode(":", $TimeText);

		$Hours = (int)$pieces[0];
		$Minutes = (int)$pieces[1];

		//$DateTime = new DateTime('1999-01-01');
		//$DateTime->setTime($hours, $minutes);
		//return $DateTime;

		return date('c', mktime($Hours, $Minutes, 0, 0, 0, 0));
	}

	public function roundToNearestMinuteInterval(\DateTime $dateTime, $minuteInterval = 15)
	{
		return $dateTime->setTime(
			$dateTime->format('H'),
			round($dateTime->format('i') / $minuteInterval) * $minuteInterval,
			0
		);
	}
	*/
}
?>
