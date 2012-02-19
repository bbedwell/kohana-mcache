<?php defined('SYSPATH') or die('No direct script access.');

class Database_Query extends Kohana_Database_Query {
	
	protected $_lifetime = 3600;
	protected $_other_tables = array();

	public function add_table_to_cache($table)
	{
		$this->_other_tables[] = $table;

		return $this;
	}
	
	public function execute($db = NULL, $as_object = NULL, $object_params = NULL)
	{
		// Check that memcache cache exists
		if ( class_exists('Memcache') )
		{
			$cache_available = TRUE;
		}
		else
		{
			$cache_available = FALSE;
		}
		
		// Instantiate new Mcache object
		if ($cache_available)
		{
			$mcache = new Mcache();
		}
		
		if ( ! is_object($db))
		{
			// Get the database instance
			$db = Database::instance($db);
		}

		if ($as_object === NULL)
		{
			$as_object = $this->_as_object;
		}
		
		if ($object_params === NULL)
		{
			$object_params = $this->_object_params;
		}

		// Compile the SQL query
		$sql = $this->compile($db);
		
		if ($this->_lifetime !== NULL AND !$this->_force_execute AND $this->_type === Database::SELECT AND $cache_available)
		{
			if( ($result = $mcache->get($sql)) !== NULL AND !$this->_force_execute)
			{
				return new Database_Result_Cached($result, $sql, $as_object, $object_params);
			}
		}

		// Execute the query
		$result = $db->query($this->_type, $sql, $as_object, $object_params);

		if ($cache_available) {
			if($this->_type === Database::SELECT)
			{
				$from = array_merge($this->_from,$this->_other_tables);
				$mcache->set($from, $sql, $result->as_array(), $this->_lifetime);
			}
			elseif(isset($this->_table))
			{
				$mcache->invalidate($this->_table);
			}
		}
		
		return $result;
	}

} // End Database_Query
