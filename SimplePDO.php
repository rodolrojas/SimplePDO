<?php
	class SimplePDO{
		private $conn;
		private $query;
		private $results;
		private $options;
		private $debug;
		
		public function SimplePDO($arrOptions){
			$this -> options = $arrOptions;
			$this -> checkOptions();
			$this -> connect();
		}
		
		private function displayError($exception,$query = ''){
			echo $exception->getMessage();
			if($this->debug) echo "<pre>".$query."</pre>";
			exit;
		}
		
		public function connect(){
			if($this -> options['type'] == 'mysql'){
				$strCommand = "mysql:host={$this -> options['host']};dbname={$this -> options['database']}";
			}elseif($this -> options['type'] == 'pgsql'){
				$strCommand = "pgsql:host={$this -> options['host']}port={$this -> options['port']} dbname = {$this -> options['database']}";
			}
			try{
				$this -> conn = new PDO($strCommand, $this -> options['user'], $this -> options['pass']);
				$this -> conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                $this -> conn -> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			}catch(PDOException $exc){
				$this -> displayError($exc);
			}
			
			if($this -> options['type'] == 'mysql'){
				try{
					$this -> conn -> query("USE {$this -> options['database']};");
				}catch(PDOException $exc){
					$this -> displayError($exc);
				}
			}
		}
		public function disconnect(){
			$this -> conn = null;
			$this -> query = null;
			$this -> results = null;
		}
		protected function checkOptions(){
		
			//Validar si hay host especifico, de lo contrario apunta a localhost
			if(!isset($this -> options['host'])){
				$this -> options['host'] = 'localhost';
			}
			
			//Validar tipo de conexion
			if(isset($this -> options['type'])){
				if(!(($this -> options['type'] == 'mysql')||($this -> options['type'] == 'pgsql'))){
					exit("Invalid Connection Type");
				}				
			}else{
				$this -> options['type'] = 'mysql';
			}
			
			//Validar si hay usuario
			if(!isset($this -> options['user'])){
				exit("Missing username");
			}else{
				if(!($this -> options['user'])){
					exit("Missing username");
				}
			}
			
			//Validar si hay password, no se discrimina si hay indice pass vacio (localhost);
			if(!isset($this -> options['pass'])){
				exit("Missing password");
			}
			
			//Validar si hay database
			if(!isset($this -> options['database'])){
				exit("Missing DB name");
			}else{
				if(!($this -> options['database'])){
					exit("Missing DB name");
				}
			}
			
			//Validar si debug activado
			if(!isset($this -> options['debug'])){
				$this -> debug = 0;
			}else{
				$this -> debug = $this -> options['debug'];
			}
			// debug($this);
			return true;
		}
		
		private function getResults(){
			return $this -> results;
		}
		
		public function getSingleRow($strQuery){
			//Escapamos la query por seguridad
			// $strQuery = addslashes($strQuery);			
			try{
				$this -> query = $this -> conn -> query($strQuery);
				$this -> results = $this -> query -> fetch();				
			}catch(PDOException $exc){
				$this -> displayError($exc,$strQuery);
			}
			return $this -> getResults();
		}
		
		public function getObject($strQuery){
			//Escapamos la query por seguridad
			// $strQuery = addslashes($strQuery);			
			try{
				$this -> query = $this -> conn -> query($strQuery);
				$this -> results = $this -> query -> fetchObject();				
			}catch(PDOException $exc){
				$this -> displayError($exc,$strQuery);
			}
			return $this -> getResults();
		}
		
		
		public function countRows($strQuery){
			//Escapamos la query por seguridad
			// $strQuery = addslashes($strQuery);			
			try{
				$this -> query = $this -> conn -> query($strQuery);
				$this -> results = $this -> query -> rowCount();				
			}catch(PDOException $exc){
				$this -> displayError($exc,$strQuery);
			}
			return $this -> getResults();
		}
		
		public function getRows($strQuery){
			//Escapamos la query por seguridad
			// $strQuery = addslashes($strQuery);			
			try{
				$this -> query = $this -> conn -> query($strQuery);
				$this -> results = $this -> query -> fetchAll();				
			}catch(PDOException $exc){
				$this -> displayError($exc,$strQuery);
			}
			return $this -> getResults();
		}
		
		public function execute($strQuery){
			//Escapamos la query por seguridad
			// $strQuery = addslashes($strQuery);			
			try{
				$this -> query = $this -> conn -> query($strQuery);			
			}catch(PDOException $exc){
				$this -> displayError($exc,$strQuery);
			}
		}
		
		public function lastInsert(){	
			try{
				$data = $this -> conn -> lastInsertId();			
			}catch(PDOException $exc){
				$this -> displayError($exc);
			}
			return $data;
		}
	}
?>
