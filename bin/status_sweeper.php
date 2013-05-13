<?php
/*
 * Sweeps through any peristed status messages and attempts to re-issue them to the server
 */
require_once ('/dat/player/app/player/classes/services/remote/CommandService.php');
require_once ('/dat/player/app/player/classes/services/remote/Rsync.php');


$sql = 'SELECT * FROM status_messages';
$results = CommandService::getSQL($sql, TRUE);

foreach($results as $row){
	$response = ($row['message']) ? 
		CommandService::makeStatusPing($row['url'], $row['player_id'], $row['command_id'], $row['secret'], $row['status'], $row['message']) 
			:
		CommandService::makeStatusPing($row['url'], $row['player_id'], $row['command_id'], $row['secret'], $row['status']);
	if($response){
		if($response->success == TRUE){
			$sql = 'DELETE FROM status_messages WHERE id = ' . $row['id'];
			CommandService::doSQL($sql);
		}
	}
}


?>