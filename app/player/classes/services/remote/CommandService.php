<?php
require_once ('/dat/player/app/player/classes/models/Command.php');
require_once ('/dat/player/app/player/classes/models/Asset.php');
require_once ('/dat/player/app/player/classes/models/VideoAsset.php');
require_once ('/dat/player/app/player/classes/models/RemoteVideoAsset.php');
require_once ('/dat/player/app/player/classes/models/Loop.php');
require_once ('/dat/player/app/player/classes/models/Ad.php');
require_once ('/dat/player/app/player/classes/models/ContentBin.php');
require_once ('/dat/player/app/player/classes/models/ContentItem.php');
require_once ('/dat/player/app/player/classes/models/VideoMetaData.php');
require_once ('/dat/player/app/common/functions/database.php');
/**
 *
 * Class to handle remote/local fetching and storing of commands and assets
 *
 */
class CommandService {
	/**
	 * returns a list of commands in array format from Web
 */
	public static function fetchCommandsFromWeb($url, $playerId){
		$getURL    = $url . '?player_id=' . $playerId;
		$curlReq   = curl_init($getURL);
		// Configuring curl options
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
		);
		curl_setopt_array($curlReq, $options);
		$rawJSON = curl_exec($curlReq);
	
		try{
			if($rawJSON == FALSE){
				throw new Exception('failed to connect to: ' . $getURL);
			}
			
		} catch(Exception $e){
			echo $e->getMessage();
		}	
		return self::constructFromJSON($rawJSON);		
	}
	/*
	 * returns a list of commands in array format from DB (optional limit clause)
	 */
	public static function fetchCommandsFromDB($limit = 5){
			$db = Database::sqlite();
			$sql = "SELECT * FROM commands LIMIT $limit";
		    if ($row = self::$db->query($sql)){
		    	$db-close();
				return self::constructFromDB($row);	
		    }
			$db-close();
	}

	/**
	 * takes in a list of commands in array format and stores them in sqlite
	 */
    public static function dumpCommands(array $commandList){
		$db = Database::sqlite();
		foreach($commandList as $command){
			// FIRST LET'S ADD THE COMMAND
			$sql = 'INSERT OR IGNORE INTO commands (id, priority, name) VALUES (' . $command->outputCSV('id', 'priority', 'name') . ')';
			if($db->query($sql)){
				switch($command->getName()){
					case 'GetFileAsset':
						// NEXT LET'S ADD THE FILE ASSET
						if($asset = $command->getRemoteVideoAsset()){
							$sqlRemoteAsset = 'INSERT OR IGNORE INTO remote_assets (id, asset_id, source_host, filename, md5, remote_path) VALUES ('. $asset->outputCSV('id', 'asset_id', 'source_host', 'filename', 'md5', 'remote_path') . ')';
							$sqlVideoAsset = 'INSERT OR IGNORE INTO video_assets (id, asset_id, filename, local_path, video_meta_data_id) VALUES ('. $asset->getVideoAsset()->outputCSV('id', 'asset_id', 'filename', 'local_path', 'video_meta_data_id') . ')';	
							$sqlAsset = 'INSERT OR IGNORE INTO assets (id, type, duration, name) VALUES (' . $asset->getAsset()->outputCSV('id', 'type', 'duration', 'name').')';
							$sqlMetaData = 'INSERT OR IGNORE INTO video_meta_data (id, height, width, duration, container, video_codec, video_bitrate, framerate, timebase_framerate, audio_codec, audio_bitrate, sampling_rate) VALUES ('. $asset->getVideoAsset()->getMetaData()->outputCSV('id', 'height', 'width', 'duration', 'container', 'video_codec', 'video_bitrate', 'framerate', 'timebase_framerate', 'audio_codec', 'audio_bitrate', 'sampling_rate').')'; 
							$sqlCommandFileAsset = 'INSERT OR IGNORE INTO command_remote_assets (command_id, remote_asset_id) VALUES ('.$command->getId().', '.$asset->getId().')';
							if($db->query($sqlRemoteAsset)){
								if($db->query($sqlVideoAsset)){
									if($db->query($sqlMetaData)){
										// FINALLY LET'S ADD THE ASSET AND THE COMMAND / FILE ASSET ASSOCIATION
										$db->query($sqlAsset);
										$db->query($sqlCommandFileAsset);
									} else {
										throw new Exception("unable to persist video_meta_data to the database");
										return FALSE;
									}
								} else {
									throw new Exception("unable to persist video_assets to the database");
									return FALSE;
								}
							} else {
								throw new Exception('Unable to persist remote_video_asset to the database.');
								return FALSE;
							}
						}
						break;
					case 'NewLoop':
						// FIRST LET'S ADD THE LOOP
						if($loop = $command->getLoop()){
							$sqlLoop = 'INSERT OR IGNORE INTO loops (id, name, loop_length, allowed_variance) VALUES ('. $loop->outputCSV('id', 'name', 'loop_length', 'allowed_variance') . ')';
							if($db->query($sqlLoop)){
								// NEX LET'S ADD THE CONTENT BIN
								foreach($loop->getContentBins() as $content_bin){
									$sqlContentBin = 'INSERT OR IGNORE INTO content_bins (id, name, status) VALUES ('.$content_bin->outputCSV('id', 'name', 'status').')';
										if($db->query($sqlContentBin)){
											// NEXT LET'S ADD THE CONTENT ITEM
											foreach($content_bin->getContentItems() as $content_item){
												$sqlContentItem = 'INSERT OR IGNORE INTO content_item (id, name, status, asset_id) VALUES ('.$content_item->outputCSV('id', 'name', 'status').', '.$content_item->getAsset()->getId(). ')';
												$sqlAsset = 'INSERT OR IGNORE INTO assets (id, type, name, duration) VALUES ('.$content_item->getAsset()->outputCSV('id', 'type', 'name', 'duration').')';
												$sqlLoopContentBins = 'INSERT OR IGNORE INTO loop_content_bins (loop_id, content_bin_id) VALUES ('.$loop->getId().', '.$content_bin->getId().')';
												if($db->query($sqlContentItem)){
													// FINALLY WE ADD THE ASSET AND THE LOOP / CONTENT BIN ASSOCIATION
													$db->query($sqlAsset);
													$db->query($sqlLoopContentBins);
												} else {
													echo('unable to persist content item to the database');
													return FALSE;
												}							
											}
										} else {
											echo('unable to persist content bin to the database');
											return FALSE;
										}
								}
								// NEXT LET'S ADD THE ADS
								foreach($loop->getAds() as $ad){
									$sqlAd = 'INSERT OR IGNORE INTO ads (id, name, status, asset_id) VALUES ('.$ad->outputCSV('id', 'name', 'status').', '.$ad->getAsset()->getId().')';
									$sqlAsset = 'INSERT OR IGNORE INTO assets (id, type, name, duration) VALUES ('.$ad->getAsset()->outputCSV('id', 'type', 'name', 'duration').')';
									$sqlLoopAds = 'INSERT OR IGNORE INTO loop_ads (loop_id, ad_id) VALUES ('.$loop->getId().', '.$ad->getId().')';
									if($db->query($sqlAd)){
										// FINALLY WE ADD THE ASSET AND THE LOOP / AD ASSOCIATION
										$db->query($sqlAsset);
										$db->query($sqlLoopAds);
									} else {
										echo('unable to persist ad to the database');
										return FALSE;
									}
								}								

							} else {
								echo('Unable to persist loop to the database');
								return FALSE;
							}

						}
				}

				// ok, so we have succesfully persisted the command to the database, let's tell the server
				self::statusAcknowledged($command->getId());
			} else {
				echo('Unable to persist commands to the database.');
				return FALSE;
			}
		}
		$db->close();
		return TRUE;
    }
	
	/* Recieved:
	 * When a Player first retrieves a Command from the server and persists that Command to the local datastore, 
	 * it is inserted with a status of received. There is no pre-requisite for this status; it is the default.
	 */
	public static function statusRecieved($command_id){
		$sql = "UPDATE commands SET status = 'recieved' WHERE id = $command_id";
		$db = Database::sqlite();
		if(!$db->query($sql)){
			echo "unable to update command with id of $command_id";
		} 
		$db->close();
	}	
	/* Acknowledged:
	 * Once a Command and any associated meta information passed with that{{Command}} (e.g., CommandFileAssets are correctly 
	 * persisted to the Player's local datastore, the Player calls the server to acknowledge receipt of the command. Once the 
	 * Player makes a successful call (e.g, HTTP 200 Status), the Player changes the status of the Command from received to acknowledged
	 */
	public static function statusAcknowledged($command_id){
		$sql = "UPDATE commands SET status = 'acknowledged' WHERE id = $command_id";
		$db = Database::sqlite();
		if(!$db->query($sql)){
			echo "unable to update command with id of $command_id";
		}
		$db->close();
	}

	/* Processing:
	 * The next step in the command process is to work on the Command. Just before starting work, the Player changes 
	 * the status of the Command to processing.
	 */
	public static function statusProcessing($command_id){
		$sql = "UPDATE commands SET status = 'processing' WHERE id = $command_id";
		$db = Database::sqlite();
		if(!$db->query($sql)){
			echo "unable to update command with id of $command_id";
		}
		$db->close();
	}

	/* Success:
	 * Once the Player has handled the Command (either successfully or unsuccessfully), it changes the status to processed. 
	 * At the same time, it sets the result column to success or error.
	 */
	public static function statusSuccess($command_id, $status = TRUE){
		$result = ($status) ? 'success' : 'error'; 
		$sql = "UPDATE commands SET result = '$result' WHERE id = $command_id";
		$db = Database::sqlite();
		if(!$db->query($sql)){
			echo "unable to update command with id of $command_id";
		}
		$db->close();
	}
	/* Error:
	 * Should the Player encounter a problem while trying to handle the Command, it will also populate the error_message column 
	 * in the command table. If the Player encounters an error, it stops processing the Command (and any associated tasks).
	 */	
	public static function statusError($command_id){
		$sql = "UPDATE commands SET status = 'error' WHERE id = $command_id";
		$db = Database::sqlite();
		if(!$db->query($sql)){
			echo "unable to update command with id of $command_id";
		}
		$db->close();
	}

	/* Complete:
	 * No matter whether the Player finished its command processing with a status of success or error, it should communicate that 
	 * status (and possible error_message to the server. Once the player successfully communicates the success or error to the 
	 * server, it changes the Command's status to complete.
	 */
	public static function statusComplete($command_id){
		$sql = "UPDATE commands SET status = 'complete' WHERE id = $command_id";
		$db = Database::sqlite();
		if(!$db->query($sql)){
			echo "unable to update command with id of $command_id";
		}
		$db->close();
	}



	/*
	 * Takes in a result set form database query and returns an array of commands
	 */
	protected function constructFromDB($result_set){
		$commandArray = array();
		foreach($result_set as $entry){
		    $command = new Command();
		    $command
		    	->setId($entry['id'])
		    	->setName($entry['name'])
		    	->setPriority($entry['priority'])
		    	->setSecret($entry['secret']);
			$sqlAssets = 'SELECT * FROM file_assets JOIN command_file_assets ON file_assets.id = command_file_assets.asset_id WHERE command_file_asset.command_id = '. $entry['id'];				  
			if ($asset = self::$db->query($sqlAssets)){
				$new_asset = new Asset();
				$new_asset->setId($asset['id']);
				$new_asset->setHost($asset['host']);
				$new_asset->setName($asset['name']);
				$new_asset->setDuration($asset['duration']);
				$new_asset->setWidth($asset['width']);
				$new_asset->setHeight($asset['height']);
				$new_asset->setFilename($asset['filename']);
				$new_asset->setremote_path($asset['remote_path']);
				$new_asset->setlocal_path($asset['local_path']);
				$new_asset->setExtension($asset['extension']);
				$new_asset->setType($asset['type']);
				$new_asset->setAssetId($asset['asset_id']);
				$new_asset->setmd5($asset['md5']);	
				$command->setFileAsset($new_asset);
		    }			  
		    $commandArray[] = $command;					
		}
		return $commandArray;		
	}
	
	/*
	 * takes in a raw JSON string and produces subsequent command and asset objects
	 */
	protected function constructFromJSON($json){
		$jsonObject = json_decode($json);
		if($jsonObject->success == FALSE){
			//get the fuck out
			return FALSE;
		}
		$commandArray = array();
		/*
		 * Let's create the Command object (parent)
		 */
		foreach($jsonObject->commands as $singleCommand){
			$command = new Command();
			$command
				->setName($singleCommand->name)
				->setId($singleCommand->id)
				->setPriority($singleCommand->priority)
				->setSecret($singleCommand->secret);
			switch($singleCommand->name){
				/*
				 * Are We dealing with a FileAsset?
				 */
				case 'GetFileAsset':
					if(isset($singleCommand->commandData->fileAsset)){
						$singleFileAsset = $singleCommand->commandData->fileAsset;
						$asset           = new Asset();						
						$videoMetaData   = new VideoMetaData;
						/*
						 * Let's setup the Asset first
						 */
						if(isset($singleFileAsset->asset)){
							$asset
								->setId($singleFileAsset->asset->id)
								->setType($singleFileAsset->asset->assetType)
								->setName($singleFileAsset->asset->name)
								->setDuration($singleFileAsset->asset->duration);
						} else {
							throw new Exception("There is no asset...");
						}
						/*
						 * Next let's setup the VideoMetData
						 */
						if(isset($singleFileAsset->videoMetaData)){
							$videoMetaData
								->setId($singleFileAsset->videoMetaData->id)
								->setHeight($singleFileAsset->videoMetaData->height)
								->setWidth($singleFileAsset->videoMetaData->width)
								->setDuration($singleFileAsset->videoMetaData->duration)
								->setContainer($singleFileAsset->videoMetaData->container)
								->setVideoCodec($singleFileAsset->videoMetaData->videoCodec)
								->setVideoBitRate($singleFileAsset->videoMetaData->videoBitrate)
								->setFrameRate($singleFileAsset->videoMetaData->framerate)
								->setTimeBaseFrameRate($singleFileAsset->videoMetaData->timebaseFramerate)
								->setAudioCodec($singleFileAsset->videoMetaData->audioCodec)
								->setAudioBitRate($singleFileAsset->videoMetaData->audioBitrate)
								->setSamplingRate($singleFileAsset->videoMetaData->samplingRate);
						} else {
							throw new Exception("There is no videoMetaData...");
						}
						/*
						 * Create the Video Asset and assign the Meta Data and the Asset to it
						 */
						$videoAsset = new VideoAsset();
						$videoAsset
							->setId($singleFileAsset->id)
							->setAssetId($asset->getId())
							->setVideoMetaDataId($videoMetaData->getId())
							->setFilename($singleFileAsset->filename)
							->setLocalPath(LOCAL_PATH)
							->setAssetId($singleFileAsset->asset->id)
							->setVideoMetaDataId($singleFileAsset->videoMetaData->id)
							->setAsset($asset)
							->setMetaData($videoMetaData);
						/*
						 * Create the Remote Video Asset and assign the Video Asset and the Asset to it
						 */
						$remoteVideoAsset = new RemoteVideoAsset();
						$remoteVideoAsset
							->setId($singleFileAsset->id)
							->setSourceHost($singleCommand->commandData->host)
							->setFileName($singleFileAsset->filename)
							->setRemotePath($singleFileAsset->path)
							->setHash($singleFileAsset->hash)
							->setAssetId($singleFileAsset->asset->id)
							->setVideoAsset($videoAsset)
							->setAsset($asset);
						$command->setRemoteVideoAsset($remoteVideoAsset);		
					} else {
						throw new Exception("There is no fileAsset...");
					}
					break;
				/*
				 * Are we dealing wtih a Loop?
				 */
				case 'NewLoop':
					$loop = new Loop();
					$loop
						->setId($singleCommand->commandData->id)
						->setName($singleCommand->commandData->name)
						->setStatus($singleCommand->commandData->status)
						->setLoopLength($singleCommand->commandData->loopLength)
						->setVariance($singleCommand->commandData->allowedVariance);
					$adArray = array();
					foreach($singleCommand->commandData->ads as $singleAd){
						$ad = new Ad();
						$ad->setId($singleAd->id);
						$ad->setName($singleAd->name);
						$ad->setStatus($singleAd->status);
						$ad->setAdvertiser($singleAd->advertiser);
						if(isset($singleAd->asset)){
							$asset = new Asset();
							$asset->setId($singleAd->asset->id);
							$asset->setType($singleAd->asset->assetType);
							$asset->setName($singleAd->asset->name);
							$asset->setDuration($singleAd->asset->duration);
							$ad->setAsset($asset);
						} else {
							throw new Exception("There is no asset...");
						}
						$adArray[] = $ad;
					}
					$loop->setAds($adArray);
					$binArray = array();
					foreach($singleCommand->commandData->contentBins as $singleBin){
						$contentBin = new ContentBin();
						$contentBin->setId($singleBin->id);
						$contentBin->setName($singleBin->name);
						$contentBin->setStatus($singleBin->status);
						$itemArray = array();
						foreach($singleBin->contentItems as $singleItem){
							$contentItem = new ContentItem();
							$contentItem->setId($singleItem->id);
							$contentItem->setName($singleItem->name);
							$contentItem->setStatus($singleItem->status);
							if(isset($singleItem->asset)){
								$asset = new Asset();
								$asset->setId($singleItem->asset->id);
								$asset->setType($singleItem->asset->assetType);
								$asset->setName($singleFileAsset->asset->name);
								$asset->setDuration($singleFileAsset->asset->duration);									
								$contentItem->setAsset($asset);
							} else {
								throw new Exception("there is no asset...");
							}
							$itemArray[] = $contentItem;
						}
						$contentBin->setContentItems($itemArray);
						$binArray[] = $contentBin;
					}
					$loop->setContentBins($binArray);
					$command->setLoop($loop);
					break;
			}
		
			$commandArray[] = $command;
		}
		return $commandArray;		
	}

}

?>
