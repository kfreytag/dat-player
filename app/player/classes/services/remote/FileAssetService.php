<?php

require_once ('/dat/player/app/player/classes/models/RemoteVideoAsset.php');
require_once ('/dat/player/app/player/classes/models/Asset.php');
require_once ('/dat/player/app/player/classes/services/AssetService.php');
require_once ('/dat/player/app/common/functions/database.php');
/**
 *
 * Class to handle updating of FileAssets in the DB
 *
 */
Class FileAssetService {
	protected $id;

	public function __construct($id){
		$this->id = $id;
		return self::getFileAsset($id);
	}

	/*
	 * sets and asset to ready
	 */
	public static function assetReady($asset_id, $flag = TRUE){
		$flag = ($flag)? 1 : 0;
		$sql = "UPDATE file_assets SET ready = $flag WHERE id = $asset_id";
		$db = Database::sqlite();		
		if(!$db->query($sql)){
			echo "unable to update command with id of $asset_id";
			$db->close();
			return FALSE;
		}
		$db->close();	
		return TRUE;	
	}
	/*
	 * Compares a provided md5 value with the one in the database
	 */
	public static function md5Match($asset_id, $md5){
		$sql = "SELECT md5 FROM remote_assets WHERE id = $asset_id";
		$db = Database::sqlite();
		if($old_md5 = $db->querySingle($sql)){
			if($old_md5 === $md5){
				return TRUE;
			} else {
				return FALSE;
			}
		}
		$db->close();
	}

	/*
	 * Fetch a single asset from the DB based on ID
	 */
	public static function getFileAsset($id){
		$sql = 'SELECT * FROM asset WHERE id = '.$id;
		$db = Database::sqlite();
		if($result = $db->querySingle($sql)){
			$fileAsset = new FileAsset;
			$fileAsset->setId($result['id']);
			$fileAsset->setName($result['name']);
			$fileAsset->setType($result['type']);
			$fileAsset->setFileName($result['filename']);
			$fileAsset->setExtension($result['extension']);
			$fileAsset->setHost($result['source_host']);
			$fileAsset->setRemotePath($result['remote_path']);
			$fileAsset->setLocalPath($result['local_path']);
			$fileAsset->setHash($result['md5']);
			$fileAsset->setHeight($result['height']);
			$fileAsset->setWidth($result['width']);
			$fileAsset->setDuration($result['duration']);
			$fileAsset->setCreated($result['created']);
			$fileAsset->setAssetId($result['asset_id']);
			$fileAsset->setAsset(AssetService::getAsset($result['asset_id']));
			return $fileAsset;
		}
	}
}	



?>
