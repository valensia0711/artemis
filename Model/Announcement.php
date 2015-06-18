<?php
class Announcement{
	private $id = 0;
	private $title = "";
	private $content = "";
	private $time = "";
	private $timeline = "";
	private $poster = "";

	public function __construct($id, $title, $content, $time, $timeline, $poster) {
		$this->id = $id;
		$this->title = $title;
		$this->content = $content;
		$this->time = $time;
		$this->timeline = $timeline;
		$this->poster = $poster;
	}

	public function getID(){
		return $this->id;
	}

	public function setID($id){
		$this->id = $id;
	}

	public function getTitle(){
		return $this->title;
	}

	public function setTitle($title){
		$this->title = $title;
	}

	public function getContent(){
		return $this->content;
	}

	public function setContent($content){
		$this->content = $content;
	}

	public function getTime(){
		return $this->time;
	}

	public function setTime($time){
		$this->time = $time;
	}

	public function getTimeline(){
		return $this->timeline;
	}


	public function setTimeline($timeline){
		$this->timeline = $timeline;
	}




}

?>