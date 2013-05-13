<?php
require_once ('/dat/player/app/player/classes/models/ContentItem.php');
require_once ('/dat/player/app/player/classes/services/AssetService.php');
require_once ('/dat/player/app/common/functions/database.php');

class ContentItemService{
	protected $id;


	public function __construct(){
		$this->id = $id;
		return self::getContentItem($id);
	}

	public static function getContentItem($id){
		$sql = 'SELECT * FROM content_items WHERE id = '. $id;
		if($result = $db->querySingle($sql)){
			$ad = new ContentItem($result['id'], $result['name'], $result['status']);
			$ad->setAsset(AssetService::getAsset($result['asset_id']));
			return $ad;
		} else {
			return FALSE;
		}
	}

}



?