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
define('COMMAND_URL', $config['urls']['commands']);
define('LOCAL_PATH', $config['paths']['assets']);
define('SSH_USER', $config['ssh']['user']);
define('SSH_HOST', $config['ssh']['host']);
/*
 * Fetch Commands from Web
 */
try{
	$commands = CommandService::fetchCommandsFromWeb(COMMAND_URL, PLAYER_ID);
	/*
	 * Store Commands in the DB
	 */
	if(CommandService::dumpCommands($commands)){
		/*
		 * Loop Through Commands
		 */
		foreach($commands as $command){
			if($response = CommandService::makeStatusPing(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'acknowledged')){
				if($response->success == FALSE){
					CommandService::makeStatusDump(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), 'acknowledged');
				}				
			}
			CommandService::statusAcknowledged($command->getId());
		}
	/*
	 * Failed to store Commands in DB
	 */
	} else {
		CommandService::statusError($command);
	}

} catch(CommandException $e) {
	$command = $e->getCommand();
	echo 'Exception Caught: ' . $e->getMessage() . ' on line: ' . $e->getLine() . ' in: ' . $e->getFile();
	if($response = CommandService::makeStatusPing(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), $e->getMessage())){
		if($response->success == FALSE){
			CommandService::makeStatusDump(STATUS_URL, PLAYER_ID, $command->getId(), $command->getSecret(), $e->getMessage());
		}
	}
} catch(Exception $e){
	echo 'Exception Caught: ' . $e->getMessage() . ' on line: ' . $e->getLine() . ' in: ' . $e->getFile();
}






?>
