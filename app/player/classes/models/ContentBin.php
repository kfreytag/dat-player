<?php
require_once('Model.php');
/**
* Model Class for Content Bins
*/
class ContentBin extends Model{
	protected $id;
	protected $status;
	protected $name;
	protected $contentItems;

	public function __construct($id = null, $name = null, $status = null, $contentItems = null){
		$this->id = $id;
		$this->name = $name;
		$this->status = $status;
		$this->contentItems = $contentItems;
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getContentItems(){
		return $this->contentItems;
	}

	public function setContentItems(array $contentItems){
		$this->contentItems = $contentItems;
	}

}




?>