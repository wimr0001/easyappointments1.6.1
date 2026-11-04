<?php

class DateTimeObject{

	var $TimeStamp = null; //Unix timestampst
	var $DateTimeText = null;
	
	private function setTimeStamp($TimeStamp){
		$this->TimeStamp = $TimeStamp;
		$this->DateTimeText = $this->getDateTimeText();
	}
	
	public static function createByCurrentDateTime(){
		$Instance = new DateTimeObject();
		$Instance->setTimeStamp(time());
		return $Instance;
	}
	
	public static function createByTimeStamp($TimeStamp) {
		$Instance = new DateTimeObject();
		$Instance->setTimeStamp($TimeStamp);
		return $Instance;
	}
	
	
	public static function createByDateTime($DateTime){
		$Instance = new DateTimeObject();
		$Instance->setTimeStamp($DateTime->getTimestamp());
		return $Instance;
	}

	public static function createByNumbers($Hours, $Minutes, $Seconds, $Day, $Month, $Year) {

		$Instance = new DateTimeObject();
		$Instance->setTimeStamp( mktime($Hours, $Minutes, $Seconds, $Month, $Day, $Year) );
		return $Instance;
	}

	//'1-7-2014 12:30:15'
	public static function createByText($Text){
		$Instance = new DateTimeObject();
		
		$Instance->setTimeStamp( strtotime($Text) );
		return $Instance;
	}
	
	public static function createByText_mm_dd_yyyy($Text) {
		return DateTimeObject::createByText($Text);
	}
	
	//08:30
	public static function createByTimeText_HH_MM($Text) {
		$Instance = new DateTimeObject();
		
		$Pieces = explode(':', $Text);

		$Hours = (int)$Pieces[0];
		$Minutes = (int)$Pieces[1];
		
		$Day = 1;
		$Month = 1;
		$Year = 1970;
		
		$Instance->setTimeStamp( mktime($Hours, $Minutes, 0, $Month, $Day, $Year) );
		return $Instance;
	}
	
	public static function createByTime_HH_MM($Hours, $Minutes) {
		$Instance = new DateTimeObject();
		
		$Day = 1;
		$Month = 1;
		$Year = 1970;
		
		$Instance->setTimeStamp( mktime($Hours, $Minutes, 0, $Month, $Day, $Year) );
		return $Instance;
	}
	
	public static function createByTimeNumbers($Hours, $Minutes) {
		$Instance = new DateTimeObject();
		
		$Day = 1;
		$Month = 1;
		$Year = 1970;
		
		$Instance->setTimeStamp( mktime($Hours, $Minutes, 0, $Month, $Day, $Year) );
		return $Instance;
	}

	//Sql date = string '2017-09-24'  / Labels: where-clause
	public static function createBySqlDate($SqlDate) {
		$Instance = new DateTimeObject();
		$Instance->setTimeStamp( strtotime($SqlDate) );
		return $Instance;
	}
	
	//Sql date = string '2017-09-24 13:09:37'  / Labels: where-clause
	public static function createBySqlDateTime($SqlDate) {
		$Instance = new DateTimeObject();
		$Instance->setTimeStamp( strtotime($SqlDate) );
		return $Instance;
	}
	
	//"2011-07-18 00:00:00"   / Labels: where-clause
	public function getSqlDateTime() {
		return date('Y-m-d H:i:s', $this->TimeStamp);
	}
	
	public function getDateTime() {
		$DateTime = new DateTime();
		$DateTime->setTimestamp($this->TimeStamp);

		return $DateTime;
	}
	
	public function getText() {
		return date('c', $this->TimeStamp);
	}

	public function getDateTimeText() {
		return date('c', $this->TimeStamp);
	}

	public function getTimeStamp() {
		return $this->TimeStamp;
	}
	
	public function getDay(){
		return (int)date('j', $this->TimeStamp);
	}
	
	public function getMonth(){
		return (int)date('n', $this->TimeStamp);
	}
	
	public function getYear(){
		return (int)date('Y', $this->TimeStamp);
	}
	
	public function getHours(){
		return (int)date('G', $this->TimeStamp);
	}
	
	public function getMinutes(){
		return (int)date('i', $this->TimeStamp);
	}
	
	public function addDays($Days){
		$TimeStamp = $this->TimeStamp + 24*60*60  * $Days;
		$this->setTimeStamp($TimeStamp);
	}
	
	public function getRoundedToNearestMinute_DateTimeObject($minuteInterval = 15)
	{
		$dateTime = $this->getDateTime();
		
		$dateTime->setTime(
			$dateTime->format('H'),
			round($dateTime->format('i') / $minuteInterval) * $minuteInterval,
			0
		);
		
		$DateTimeObject = DateTimeObject::createByDateTime($dateTime);
		
		return $DateTimeObject;
	}
	
}
?>
