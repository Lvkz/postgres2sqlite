<?php
class ConvertToSQLite {
	private $pg_dump;
 	private $pg_host;
 	private $pg_port;
 	private $pg_database;
 	private $pg_user;
 	private $pg_password;
 	private $dump_location;
 	private $sqliteFileContent = array();
	
	public function __construct($host=false, $port=false, $database=false, $user=false, $password=false) {
  		if (($host != false) && ($port != false) && ($database != false) && ($user != false) && ($password != false)) {
   			$this->pg_host = $host;
   			$this->pg_port = $port;
   			$this->pg_database = $database;
   			$this->pg_user = $user;
   			$this->pg_password = $password;
  		} else {
   			echo 'Data required missing for this class to work.';
  		}
 	}

 	private function dumpDatabase() {
  		## Console Variables:
  		putenv("PGHOST=" . $this->pg_host);
  		putenv("PGPORT=" . $this->pg_port);
  		putenv("PGDATABASE=" . $this->pg_database);
  		putenv("PGUSER=" . $this->pg_user);
 		putenv("PGPASSWORD=" . $this->pg_password);

  		## pg_dump Location:
  		$dump_cmd = array($this->pg_dump, "--data-only", "--inserts", ">", $this->dump_location);

  		## Running Command
  		exec(join(' ', $dump_cmd), $cmdout, $cmdresult);

  		if ($cmdresult != 0) {
   			die('Status: Something when wrong while running the command. Error No: ' . $cmdresult . '<br />');
   			return false;
  		} else {
   			return true;
  		}
 	}

 	private function prepareDumpFile() {
		## SQLite’s default behavior is putting each statement into a transaction,
		## which seems to be the time waster. To prevent this behavior you can run 
		## the script within 1 transaction by specifying BEGIN; at the top of the  
		## db.sqlite3 and END; at the end of the file.
		
  		array_push($this->sqliteFileContent, 'BEGIN' . PHP_EOL);

  		foreach ($this->fileData() as $line) {
   			$firstWord = explode(' ',trim($line));

   			if (strcmp('SET', $firstWord[0]) == 0){
    			#This line is ignored since is not used in SQLite.
   			} else if(strcmp('SELECT', $firstWord[0]) == 0) {
    			#This line is ignored since is not used in SQLite.
   			} else {
    			#this line is accepted but some mofifications are needed before adding it to the file.
    			#Replace True for 't'
    			$line = str_replace('true', "'t'", $line);

    			#Replace False for 'f'
    			$line = str_replace('false', "'f'", $line);

    			#Add line to the pile.
    			$selectedLine = $line;
    			array_push($this->sqliteFileContent, $selectedLine);
   			}
  		}

 		array_push($this->sqliteFileContent, 'END');
  		return true;
 	}
	
	private function generateSQLiteFile() {
		#Creating File
  		$my_file = 'db.sqlite3';
  		$handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
  		fclose($handle);

  		#Writing lines into the file.
  		$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);

  		foreach ($this->sqliteFileContent as $line) {
   			fwrite($handle, $line);
  		}

  		#Closing File
  		fclose($handle);

  		return true;
 	}
	
	private function fileData() {
  		$file = fopen($this->dump_location, 'r');

  		if(!$file)
   			die('Status: File could not be read/opened.');

  		while(($line = fgets($file)) !== false) {
   			yield $line;
  		}

  		fclose($file);
 	}
	
	private function printErrorMessage($process) {
  		echo 'Status: Something went wrong while executing: ' . $process . '<br />';
  		return;
 	}

 	public function SetAppLocation($pg_dump) {
		$this->pg_dump = $pg_dump;
	}
	
	public function SetDumpLocation($dump_location) {
		$this->dump_location = $dump_location;
	}
	
	public function ProcessConversion() {
		if(!$this->dumpDatabase()) { $this->printErrorMessage('dumpDatabase'); die(); }
		if(!$this->prepareDumpFile()) { $this->printErrorMessage('prepareDumpFile'); die(); }
		if(!$this->generateSQLiteFile()) { $this->printErrorMessage('generateSQLiteFile'); die(); }
		
		echo 'Conversion ended successfully. ';
		echo 'You may want to look into <a href="db.sqlite3" target="_blank">db.sqlite3</a> to find your data.';
	}
}
?>
