<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Test case to ensure stability in the Mcache module
 *
 * @package    Kohana_Mcache
 * @author     Bryce Bedwell <brycebedwell@gmail.com>
 **/
class Tests_Mcache extends Unittest_TestCase {
	
	private $memcache;
	private $mcache;
	private $baseline_query;
	private $read_query;
	private $read_result_before;
	private $read_result_after;
	
	public function setUp()
	{
		$this->memcache = new Memcache();
		$this->memcache->addServer('localhost',11211);
		
		$this->mcache = new Mcache();
		
		$this->memcache->flush();
		
		$this->baseline_query = "SELECT * FROM `another_table`";
		$this->baseline_result = array(
			array(
				'id' => 100,
				'data' => "don't touch me"
			)
		);
		
		$this->mcache->set( array('another_table'), $this->baseline_query, $this->baseline_result, 3600 );
		
		$this->read_query = "SELECT * FROM `users`";
		$this->read_result_before = array(
			array(
				'id' => 1,
				'data' => 'test1'
			)
		);
		$this->read_result_after = array(
			array(
				'id' => 1,
				'data' => 'test1'
			),
			array(
				'id' => 2,
				'data' => 'test2'
			)
		);
	}
	
	public function tearDown()
	{
		$this->memcache->flush();
	}

	public function testDoesItSet()
	{
		$this->assertNull( $this->mcache->get($this->read_query) );
		
		$this->mcache->set( array('users'), $this->read_query, $this->read_result_before, 3600 );
		
		$this->assertTrue( is_array($this->mcache->get($this->read_query)) );
	}

	/**
     * @depends testDoesItSet
     */
	public function testDoesItInvalidate()
	{	
		$this->mcache->invalidate('users');
		
		$this->assertNull( $this->mcache->get($this->read_query) );
	}

	/**
     * @depends testDoesItInvalidate
     */
	public function testDoesItWork()
	{
		$this->assertNull( $this->mcache->get($this->read_query) );
		
		$this->mcache->set( array('users'), $this->read_query, $this->read_result_before, 3600 );
		
		$first = $this->mcache->get($this->read_query);
		
		$this->assertTrue( is_array($first) );
		
		$this->mcache->invalidate('users');
		
		$this->assertNull( $this->mcache->get($this->read_query) );
		
		$this->mcache->set( array('users'), $this->read_query, $this->read_result_after, 3600 );
		
		$second = $this->mcache->get($this->read_query);
		
		$this->assertTrue( is_array($second) );
		
		$this->assertNotEquals($first, $second);
	}
	
	/**
     * @depends testDoesItWork
     */
	public function testDoesItContainItself()
	{
		$this->assertTrue( is_array($this->mcache->get($this->baseline_query)) );
	}

} // End McacheTest