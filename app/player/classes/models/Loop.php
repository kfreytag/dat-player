<?php
require_once('Model.php');
/**
* Model Class For Loops
*/
class Loop extends Model{
	protected $id;
	protected $name;
	protected $loop_length;
	protected $allowed_variance;
	protected $active;
	protected $playing;
	protected $status;

	protected $ads;
	protected $contentBins;

	public function __construct($id = null, $name = null, $status = null, $loop_length = null, $allowed_variance = null, $ads = null, $contentBins = null){
		$this->id = $id;
		$this->name = $name;
		$this->loop_length = $loop_length;
		$this->allowed_variance = $allowed_variance;
		$this->status = $status;
		$this->ads = $ads;
		$this->contentBins = $contentBins;
	}


	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
		return $this;
	}

	public function getLoopLength(){
		return $this->loop_length;
	}

	public function setLoopLength($loop_length){
		$this->loop_length = $loop_length;
		return $this;
	}

	public function getVariance(){
		return $this->allowed_variance;
	}

	public function setVariance($variance){
		$this->allowed_variance = $variance;
		return $this;
	}

	public function getActive(){
		return $this->active;
	}

	public function setActive($active){
		$this->active = $active;
		return $this;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
		return $this;
	}

	public function getPlaying(){
		return $this->playing;
	}

	public function setPlaying($playing){
		$this->playing = $playing;
		return $this;
	}

	public function getAds(){
		return $this->ads;
	}

	public function setAds(array $ads){
		$this->ads = $ads;
		return $this;
	}

	public function getContentBins(){
		return $this->contentBins;
	}

	public function setContentBins(array $contentBins){
		$this->contentBins = $contentBins;
		return $this;
	}


}



?>