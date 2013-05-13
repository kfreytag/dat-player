<?php
require_once ('/dat/player/app/player/classes/models/ContentBin.php');
require_once ('/dat/player/app/player/classes/services/ContentItemService.php');
require_once ('/dat/player/app/common/functions/database.php');

class ContentBinService{
	protected $id;

	public function __construct(){
		$this->id = $id;
		return self::getContentBin($id);
	}

	public static function getContentBin($id){
		$sql = 'SELECT * FROM content_bins WHERE id = '. $id;
		if($result = $db->querySingle($sql)){
			$content_bin = new ContentBin($result['id'], $result['status'], $result['name']);
			$content_bin->setContentItems(self::getContentItems($id));
			return $content_bin;
		} else {
			return FALSE;
		}
	}

	public static function getContentItems($id){
		$sql = 'SELECT id FROM content_items JOIN content_bin_items ON content_items.id = content_bin_items.content_item_id WHERE content_bin_items.content_bin_id = ' . $id;	
		$db = Database::sqlite();
		$contentItemArray = array();

		if($result = $db->query($sql)){
			foreach($result as $row){
				$contentItemArray[] = ContentItemService::getContentItem($row['id']);
			}
			return $contentItemArray;
		} else {
			return FALSE;
		}

	}

}



?>