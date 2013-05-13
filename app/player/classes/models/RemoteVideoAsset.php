<?php
require_once('RemoteAsset.php');

class RemoteVideoAsset extends RemoteAsset{
	protected $videoAsset;


	public function getVideoAsset(){
		return $this->videoAsset;
	}

	public function setVideoAsset($videoAsset){
		$this->videoAsset = $videoAsset;
		return $this;
	}

}

?>