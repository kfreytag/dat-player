<?php
require_once('Model.php');
/**
 *
 * Generic Model Class for File Assets 
 * CHECCKAPOO
 */
Class VideoAsset extends Model{
	/*
	 * DB Specific attributes (named exactly the same as in the database)
	 */
    protected $id;
    protected $filename;
    protected $local_path;
   	protected $asset_id;
	protected $video_meta_data_id;

	protected $asset;
	protected $meta_data;

	public function getId(){
		return $this->id;
	}	

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getFileName(){
		return $this->filename;
	}
		
	public function setFileName($filename){
		$this->filename = $filename;
		return $this;
	}

	public function getLocalPath(){
		return $this->local_path;
	}

	public function setLocalPath($local_path){
		$this->local_path = $local_path;
		return $this;
	}	

	public function getAssetId(){
		return $this->asset_id;
	}

	public function setAssetId($asset_id){
		$this->asset_id = $asset_id;
		return $this;
	}

	public function getVideoMetaDataId(){
		return $this->video_meta_data_id;
	}

	public function setVideoMetaDataId($video_meta_data_id){
		$this->video_meta_data_id = $video_meta_data_id;
		return $this;
	}

	public function getAsset(){
		return $this->asset;
	}

	public function setAsset($asset){
		$this->asset = $asset;
		return $this;
	}

	public function getMetaData(){
		return $this->meta_data;
	}

	public function setMetaData($meta_data){
		$this->meta_data = $meta_data;
		return $this;
	}	

}

?>

