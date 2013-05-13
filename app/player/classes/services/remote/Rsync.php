<?php
/**
 *
 * Simple Rsync Class wrapped around the command line interface
 *
 *  EXAMPLE USAGE:
 *	$rsync = new Rsync('192.168.144.128', '/var/www/icons/quill.png', '/Users/kamran/', 'kamran');
 *	$rsync->resume(true)->ssh(true)->download();
 */
Class Rsync {
	protected $server;
	protected $directory;
	protected $username;
	protected $password;
	protected $serverpath;
	protected $ssh;
	protected $resume;
	protected $recursive;
	protected $timeout;
	protected $compress;
	protected $sizeonly;
	protected $logfile;
	protected $error;

	/*
	 * prepares the basic requirements for an Rsync command
	 */
	public function __construct($server, $serverpath, $directory, $username){
		$this->server = $server;
		$this->serverpath = $serverpath;
		$this->directory = $directory;
		$this->username = $username;
	}

	/*
	 * function to begin download of supplied resources
	 */
	public function download(){
		$option_string = $this->prepare_options();

		// here we are tacking on 2> /dev/null so that we don't get any output form rsync, we handle it ourselves
		(string) $rsync_string = 'rsync ' . $option_string . $this->server . ':' . $this->serverpath . ' ' . $this->directory . ' 2> /dev/null';
	
		return $this->execute_string($rsync_string);
	}
	
	/*
	 * function to begin upload of supplied resources
	 */
	public function upload(){
		$option_string = $this->prepare_options();

		// here we are tacking on 2> /dev/null so that we don't get any output form rsync, we handle it ourselves
		(string) $rsync_string = 'rsync ' . $option_string . $this->directory . ' ' . $this->server . ':' . $this->serverpath . ' 2> /dev/null';

		$this->execute_string($rsync_string);
	}

    /*
      * prepares the option string to splice into the command
      */
	protected function prepare_options(){
		(string) $option_string = '';
		if ($this->recursive) $option_string .= '-r '; 
		if ($this->sizeonly) $option_string .= '--size-only ';
		if ($this->logfile) $option_string .= '--log-file=' . $this->logfile . ' ';
		if ($this->resume) $option_string .= '-P ';
		if($this->timeout) $option_string .= '--timeout='.$this->timeout.' ';
		if ($this->compress) $option_string .= '-z ';
		if ($this->ssh) $option_string .= '--rsh="ssh -l '. $this->username .'" ';
		return $option_string;
	}

    /*
      * actually executes the command, and handles errors
      */
    protected function execute_string($rsync_string){
		$output = exec($rsync_string, $temp);
		// if we match this string, the size of the file is zero, we didn't get the file
		if(strpos($output, 'total size is 0') !== FALSE){
			$error = "failed to transfer file to remote server\n";
			echo $error;
			$this->error = $error;
			return FALSE;	
		// if the $temp array is empty, the return values don't exist, and an unexplained error occurred
		} elseif(empty($temp)) {
			$error = "failed to connect to server, connection timed out\n";
			$this->error = $error;			
			return FALSE;
		} else {
			// here we test for a percentage in the $output string that is less than 100
			if(preg_match('/\s\d?\d%/', $output) == 1){
				$error = "file was partially downloaded, connection was interrupted\n";
				$this->error = $error;
				sleep(10);
				$this->execute_string($rsync_string);
			} else {
				$error = "file succesfully transferred to remote server\n";
				$this->error = $error;
				return TRUE;

			}	
		}

	}

	public function getError(){
		return $this->error;
	}

	public function logfile($logfile = null){
		if($logfile == null){
			return $this->logfile;
		} else {
			$this->logfile = $logfile;
            return $this;
		}
	}

	public function sizeonly($sizeonly = null){
		if($sizeonly == null){
			return $this->sizeonly;
		} else {
			$this->sizeonly = $sizeonly;
            return $this;
		}
	}

	public function ssh($ssh = null){
		if($ssh == null){
			return $this->ssh;
		} else {
			$this->ssh = $ssh;
			return $this;
		}
	}

	public function timeout($timeout = null){
		if($timeout == null){
			return $this->timeout;
		} else {
			$this->timeout = $timeout;
			return $this;
		}
	}

	public function resume($resume = null){
		if($resume == null){
			return $this->resume;
		} else {
			$this->resume = $resume;
			return $this;
		}
	}

	public function recursive($recursive = null){
		if($recursive == null){
			return $this->recursive;
		} else {
			$this->recursive = $recursive;
			return $this;
		}
	}

	public function compress($compress = null){
		if($compress == null){
			return $this->compress;
		} else {
			$this->compress = $compress;
			return $this;
		}
	}


}

?>
