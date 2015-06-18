<?php
class User {
    private $id = 0;
    private $name = "";
    private $matricNumber = "";
    private $contact = "";
    private $email = "";
    private $cell = "";
    private $position = "";
    private $status = "";
    
    public function __construct($id, $name, $matricNumber, $contact, $email, $cell, $position, $status) {
    	$this->id = $id;
    	$this->name = $name;
    	$this->matricNumber = $matricNumber;
    	$this->contact = $contact;
    	$this->email = $email;
    	$this->cell = $cell;
    	$this->position = $position;
    	$this->status = $status;
    }
    
    public function getID() {
    	return $this->id;
    }
    
    public function setID($id) {
    	$this->id = $id;
    }
   
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getMatricNumber() {
    	return $this->matricNumber;
    }

    public function setMatricNumber($matricNumber) {
    	$this->matricNumber = $matricNumber;
    }

    public function getContact() {
    	return $this->contact;
    }

    public function setContact($contact) {
    	$this->contact = $contact;
    }

    public function getEmail() {
    	return $this->email;
    }

    public function setEmail($email) {
    	$this->email = $email;
    }

    public function getCell() {
    	return $this->cell;
    }

    public function setCell($cell) {
    	$this->cell = $cell;
    }
    
    public function getPosition() {
        if ($this->position != null) {
            return $this->position;
        } 
        $users = new DBOperation("users");
        $queryResult = $users->getQuery('SELECT position FROM users WHERE id = '.$this->id);
        return $queryResult[0]['position'];
    }

    public function setPosition($position) {
    	$this->position = $position;
    }

    public function getStatus() {
    	return $this->status;
    }

    public function setStatus($status) {
    	$this->status = $status;
    }
}

class Member extends User {
    public function __construct($id, $name, $matricNumber, $contact, $email, $cell, $position, $status) {
    	parent::__construct($id, $name, $matricNumber, $contact, $email, $cell, $position, $status);
    }
    public function isAdmin() {
        return false;
    }
}

class Admin extends User {
    public function __construct($id, $name, $matricNumber, $contact, $email, $cell, $position, $status) {
    	parent::__construct($id, $name, $matricNumber, $contact, $email, $cell, $position, $status);
    }
    public function isAdmin() {
        return true;
    }
}
?>