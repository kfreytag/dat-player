<?php
require_once('Model.php');
/**
 *
 * Generic Model Class for Remote Assets 
 *
 */
class RemoteAsset extends Model{
	protected $id;
	protected $asset_id;
	protected $source_host;
	protected $filename;
	protected $md5;
	protected $remote_path;
	protected $ready;
	protected $created;

	protected $asset;


	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getAssetId(){
		return $this->asset_id;
	}

	public function setAssetId($asset_id){
		$this->asset_id = $asset_id;
		return $this;
	}

	public function getSourceHost(){
		return $this->source_host;
	}

	public function setSourceHost($source_host){
		$this->source_host = $source_host;
		return $this;
	}

	public function getFileName(){
		return $this->filename;
	}

	public function setFileName($filename){
		$this->filename = $filename;
		return $this;
	}

	public function getHash(){
		return $this->md5;
	}

	public function setHash($md5){
		$this->md5 = $md5;
		return $this;
	}

	public function getRemotePath(){
		return $this->remote_path;
	}

	public function setRemotePath($remote_path){
		$this->remote_path = $remote_path;
		return $this;
	}

	public function getReady(){
		return $this->ready;
	}

	public function setReady($ready){
		$this->ready = $ready;
		return $this;
	}

	public function getCreated(){
		return $this->created;
	}

	public function setCreated($created){
		$this->created = $created;
		return $this;
	}

	public function getAsset(){
		return $this->asset;
	}

	public function setAsset($asset){
		$this->asset = $asset;
	}


}

?>