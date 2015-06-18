<?php
class Sign{
	private $id = 0;
	private $supervisor_id = "";
	private $time = "";
	private $venue = "";
	private $signtype = "";

	public function __construct($id, $supervisor_id, $time, $venue, $signtype) {
		$this->id = $id;
		$this->supervisor_id = $supervisor_id;
                $this->time = $time;
                $this->venue = $venue;
                $this->signtype = $signtype;
	}

	public function getID(){
		return $this->id;
	}

	public function setID($id){
		$this->id = $id;
	}

	public function getSupervisorID(){
		return $this->supervisor_id;
	}

	public function setSupervisorID($supervisor_id){
		$this->supervisor_id = $$supervisor_id;
	}

	public function getTime(){
		return $this->time;
	}

	public function setTime($time){
		$this->time = $time;
	}

	public function getVenue(){
		return $this->venue;
	}


	public function setVenue($venue){
		$this->venue = $venue;
	}
        
        public function getSignType(){
		return $this->signtype;
	}


	public function setSignType($signtype){
		$this->signtype = $signtype;
	}




}

?>