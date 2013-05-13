<?php

require_once ('/dat/player/app/player/classes/services/remote/FileAssetService.php');
require_once ('/dat/player/app/player/classes/services/remote/CommandService.php');
require_once ('/dat/player/app/player/classes/services/remote/Rsync.php');
require_once ('/dat/player/app/common/functions/spyc.php');

$spyc = new Spyc();
$config = $spyc->loadFile('/dat/player/config/environment.eng.yaml');

/*
 * CONSTANTS
 */
define('PLAYER_ID', $config['player_id']);
define('STATUS_URL', $config['urls']['status']);
define('LOCAL_PATH', $config['paths']['assets']);
define('SSH_USER', $config['ssh']['user']);
define('SSH_HOST', $config['ssh']['host']);

/*
 * Fetch Commands from DB
 */
try{
	$commands = CommandService::fetchCommandsFromDB('acknowledged');
} catch(CommandException $e) {
	$command = $e->getCommand();
	echo 'Exception Caught: ' . $e->getMessage() . ' on line: ' . $e->getLine() . ' in: ' . $e->getFile();
	CommandService::makeStatusPing(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), $e->getMessage());
} catch(Exception $e){
	echo 'Exception Caught: ' . $e->getMessage() . ' on line: ' . $e->getLine() . ' in: ' . $e->getFile();
}


/*
 * Loop Through Commands
 */
foreach($commands as $command){
	switch($command->getName()){
		case 'GetVideoAsset':
			/*
			 * Download the associated Asset
			 */
			$asset = $command->getRemoteVideoAsset();
			CommandService::statusProcessing($command->getId());
			/*
			 * Rsync the asset
			 */
			$rsync = new Rsync(SSH_HOST, $asset->getRemotePath() . $asset->getFileName(), LOCAL_PATH, SSH_USER);
			$rsync_result = $rsync->ssh(true)->sizeonly(true)->resume(true)->download();
			if($rsync_result == TRUE){
				// check md5 values to make sure they match
				if(FileAssetService::md5Match($asset->getId(), md5_file(LOCAL_PATH . $asset->getFileName())) == FALSE){
					//if they don't match - ping the server with an error message and continue; to next iteration of loop.
					if($server_response = CommandService::makeStatusPing(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'error', 'MD5 Values Do Not Match')){
						if($server_response->success == FALSE){
							CommandService::makeStatusDump(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'error', 'MD5 Values Do Not Match');
						}
					}
					continue;
				}
				CommandService::statusSuccess($command->getId());
				// ping the server transmit complete status
				if($server_response = CommandService::makeStatusPing(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'complete')){
					if($server_response->success == FALSE){
						CommandService::makeStatusDump(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'complete');
					}
				} 
				CommandService::statusComplete($command->getId());
			/*
			 * Failed to Rsync the Asset
			 */
			} else {
				// Tell the server, and add an error status to the DB
				if($server_response = CommandService::makeStatusPing(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'error', $rsync->getError())){
					if($server_response->success == FALSE){
						CommandService::makeStatusDump(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'error', $rsync->getError());
					}
				}			
				CommandService::statusError($command);
			}
			break;
		case 'NewLoop':
			// If we ever want to handle anything with loops at this point, stick it in here
			break;
	}
	
}



?>
