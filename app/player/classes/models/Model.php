<?php


abstract Class Model{
	/*
	 * this funky little method outputs the values of whatever object variables are passed in (minus dollar sign), 
	 * as a list of comma separated values. Very useful for inserting Object values into SQL tables easily. 
	 * Example Usage: $command->outputCSV('id', 'asset_id', 'source_host', 'type', 'name', 'filename', 'remote_path', 'local_path');
	 */
	public function outputCSV(){
		$arguments = func_get_args();
		$values = array();
		foreach($arguments as $arg){
			try{
				if(isset($this->$arg)){
					if(is_string($this->$arg)){
						$values[] = "'".SQLite3::escapeString($this->$arg)."'";						
					} else {
						$values[] = $this->$arg;	
					}				
				} else {
					throw new Exception("The property:" . $arg . " doesn't exist, or has not been set.\n");
				}
			} catch(Exception $e) {
				echo $e->getMessage();
				return FALSE;
			}

		}
		return implode(', ', $values);	
    }

}

?>