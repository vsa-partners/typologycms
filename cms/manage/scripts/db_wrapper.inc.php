<?php

class DatabaseConnection {

		private $_user;
		private $_pass;
		private $_host;
		private $_db;
		
		private $_connection_id	= false;

		private $_error;
		
		private $_table_fields 	= array();
		
		function __construct($database, $host, $user, $pass) {
		
			$id = @mysql_connect($host, $user, $pass, TRUE) or trigger_error('Unable to connect to database server', E_USER_ERROR);

			@mysql_select_db($database, $id) or trigger_error('Unable to select to database', E_USER_ERROR);
			
			mysql_set_charset('utf8', $id); 
			
			$this->_connection_id 	= $id;
			$this->_user 			= $user;
			$this->_pass 			= $pass;
			$this->_host 			= $host;
			$this->_db 				= $database;

			return TRUE;
		
		}
		
		function __destruct() {
			if ($this->_connection_id) mysql_close($this->_connection_id);
		}


		public function select($table, $where=null) {

			$this->_error = null;

			$query 	= 'SELECT * FROM '.$table . (!is_null($where) ? ' WHERE '.trim($where) : '' );
			$return	= array();
					
					
			if ($query_result = mysql_query($query, $this->_connection_id)) {

				if (mysql_num_rows($query_result) == 0) return array();

				while ($row = mysql_fetch_assoc($query_result)) {
					$return[] = $row;
				}

				mysql_free_result($query_result);
				return $return;
				
			 } else {

				$this->_error = mysql_error($this->_connection_id);
				return FALSE;
			
			}
		
		}



		public function query($query) {
		
			$this->_error = null;
			
//echo '<p>('.$this->_connection_id.') '.$query.'</p>';
	
			$queryid = mysql_unbuffered_query($query, $this->_connection_id);

			if (mysql_error($this->_connection_id) || !$queryid) {
				
				$this->_error = mysql_error($this->_connection_id);
				return FALSE;
			
			}
			
			return $queryid;

		}		
		
		
		public function prepData($data) {
		
			$return = '';
		
			if (is_array($data)) {

				$return = array();
				
				foreach ($data as $item) {
					$return[] = $this->prepData($row);
				}
			
			} else if (!strlen($data)) {
				$return = "''";
			} else if (is_numeric($data)) {
				$return = $data;
			} else {
			
				// Commenting this out because it's messing up JSON strings. Keep an eye on it thou.
				//$data = stripslashes($data);

				if (function_exists('mysql_real_escape_string') AND is_resource($this->_connection_id)) {
					$data =  mysql_real_escape_string($data, $this->_connection_id);
				} else if (function_exists('mysql_escape_string')) {
					$data = mysql_escape_string($data);
				} else {
					$data = addslashes($data);
				}

				$return = '"' . $data . '"';
			}		

			return $return;
			
		}

		
		public function getError() {
				return $this->_error;
		}

		public function getTableFields($table) {
				return (array_key_exists($table, $this->_table_fields)) ? $this->_table_fields[$table] : $this->_getTableFields($table);
		}

		private function _getTableFields($table) {

			$fields = mysql_list_fields($this->_db, $table, $this->_connection_id);

			for ($i=0; $i<mysql_num_fields($fields); $i++) {
				$this->_table_fields[$table][$i] = mysql_field_name($fields,$i);
			}
			
			return $this->_table_fields[$table];
		
		}


}