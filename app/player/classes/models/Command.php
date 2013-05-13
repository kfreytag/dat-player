<?php
require_once('Model.php');
/**
 *
 * Generic Model Class for Commands 
 *
 */
Class Command extends Model{
	/*
	 * DB specific attributes (named exactly the same as in the database)
	 */
    protected $id;
	protected $priority;
    protected $name;
	protected $status;
	protected $result;
	protected $error_message;
	protected $created;
	protected $completed;
	protected $secret;
	/*
	 * Object specific attributes
	 */
    protected $remoteVideoAsset;
    protected $loop;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getPriority(){
		return $this->priority;
	}

	public function setPriority($priority){
		$this->priority = $priority;
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
		return $this;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
		return $this;
	}
	
	public function getResult(){
		return $this->result;
	}

	public function setResult($result){
		$this->result = $result;
		return $this;
	}

	public function getError(){
		return $this->error_message;
	}

	public function setError($error_message){
		$this->error_message = $error_message;
		return $this;
	}

	public function getCompleted(){
		return $this->completed;
	}

	public function setCompleted($completed){
		$this->completed = $completed;
		return $this;
	}

	public function getRemoteVideoAsset(){
		return $this->remoteVideoAsset;
	}

	public function setRemoteVideoAsset($remoteVideoAsset){
		$this->remoteVideoAsset = $remoteVideoAsset;
		return $this;
	}

	public function getLoop(){
		return $this->loop;
	}

	public function setLoop(Loop $loop){
		$this->loop = $loop;
		return $this;
	}

	public function getSecret(){
		return $this->secret;
	}	

	public function setSecret($secret){
		$this->secret = $secret;
		return $this;
	}

}

?>
