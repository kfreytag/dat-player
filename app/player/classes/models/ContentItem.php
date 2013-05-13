<?php
require_once('Model.php');
/**
* Model Class for Content Items
*/		
class ContentItem extends Model{
	protected $id;
	protected $name;
	protected $status;
	protected $asset;

	public function __construct($id = null, $name = null, $status = null, $asset = null){
		$this->id = $id;
		$this->name = $name;
		$this->status = $status;
		$this->asset = $asset;
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
	}

	public function getAsset(){
		return $this->asset;
	}

	public function setAsset(Asset $asset){
		$this->asset = $asset;
	}

}



?>