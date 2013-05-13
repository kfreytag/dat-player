<?php

require_once ('/dat/player/app/player/classes/models/Ad.php');
require_once ('/dat/player/app/player/classes/services/AssetService.php');
require_once ('/dat/player/app/common/functions/database.php');

class AdService{
	protected $id;


	public function __construct(){
		$this->id = $id;
		return self::getAd($id);
	}

	public static function getAd($id){
		$sql = 'SELECT * FROM ads WHERE id = '. $id;
		if($result = $db->querySingle($sql)){
			$ad = new Ad($result['id'], $result['name'], $result['status'], $result['advertiser']);
			$ad->setAsset(AssetService::getAsset($result['asset_id']));
			return $ad;
		} else {
			return FALSE;
		}
	}

}





$ad = new AdService($id);
$ad->


?>
