<?php
require_once ('/dat/player/app/player/classes/models/Asset.php');
require_once ('/dat/player/app/common/functions/database.php');

class AssetService{
	protected $id;


	public function __construct(){
		$this->id = $id;
		return self::getAsset($id);
	}

	public static function getAsset($id){
		$sql = 'SELECT * FROM assets WHERE id = '. $id;
		if($result = $db->querySingle($sql)){
			$asset = new Asset($result['id'], $result['type'], $result['name'], $result['duration']);
			return $asset;
		} else {
			return FALSE;
		}
	}

}


?>