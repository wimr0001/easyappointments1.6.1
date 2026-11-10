<?php
class DateTimeStampRounder
{
	public function test(){
		echo 'Test';
	}
	public function roundToNearestMinuteInterval(\DateTime $dateTime, $minuteInterval = 15)
	{
		return $dateTime->setTime(
			$dateTime->format('H'),
			round($dateTime->format('i') / $minuteInterval) * $minuteInterval,
			0
		);
	}
}
?>


