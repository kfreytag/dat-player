<?php
require_once('Model.php');
/**
 *
 * Generic Model Class for Assets 
 *
 */
class Asset extends Model{
	protected $id;
	protected $type;
	protected $name;
	protected $duration;

	public function __construct($id = null, $type = null, $name = null, $duration = null){
		$this->id = $id;
		$this->type = $type;
		$this->name = $name;
		$this->duration = $duration;
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getType(){
		return $this->type;
	}

	public function setType($type){
		$this->type = $type;
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
		return $this;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function setDuration($duration){
		$this->duration = $duration;
		return $this;
	}


}


?>