<?php

require_once ('/dat/player/app/player/classes/models/Loop.php');
require_once ('/dat/player/app/player/classes/services/AdService.php');
require_once ('/dat/player/app/player/classes/services/ContentBinService.php');
require_once ('/dat/player/app/common/functions/database.php');

class LoopService{
	protected $id;


	public function __construct($id){
		$this->id = $id;
		return self::getLoop($id);
	}

	public static function getLoop($id){
		$sql = 'SELECT * FROM loops WHERE id = ' . $id;
		$db = Database::sqlite();

		if($esult = $db->querySingle($sql)){
			$loop = new Loop($result['id'], $result['name'], $result['status'], $result['loop_length'], $result['allowed_variance']);
			$loop->setAds(self::getAds($id));
			$loop->setContentBins(self::getContentBins($id));
			return $loop;
		} else {
			return FALSE;
		}
	}


	public static function getAds($id){
		$sql = 'SELECT id FROM ads JOIN loop_ads ON ads.id = loop_ads.ad_id WHERE loop_ad.loop_id = ' . $id;	
		$db = Database::sqlite();
		$adArray = array();

		if($result = $db->query($sql)){
			foreach($result as $row){
				$adsArray[] = AdService::getAd($row['id']);
			}
			return $adsArray;
		} else {
			return FALSE;
		}
	}

	public static function getContentBins($id){
		$sql = 'SELECT id FROM content_bins JOIN loop_content_bins ON content_bins.id = loop_content_bins.content_bin_id WHERE loop_content_bins.loop_id = ' . $id;
		$db = Database::sqlite();
		$binArray = array();

		if($result = $db->query($sql)){
			foreach($result = $row){
				$binArray[] = ContentBinService::getContentBin($row['id']);
			}
			return $binArray();
		} else {
			return FALSE;
		}
	}

}

?>