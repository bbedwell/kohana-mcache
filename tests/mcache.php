<?php defined('SYSPATH') or die('No direct script access.');

require_once(dirname(__FILE__).'/mocks/mockmemcache.php');

/**
 * Test case to ensure stability in the Mcache module
 *
 * @package    Kohana_Mcache
 * @author     Bryce Bedwell <brycebedwell@gmail.com>
 *
 * Copyright (c) 2011 FamilyLink.com
 * 
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
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
		$this->memcache = new MockMemcache();
		$this->memcache->addServer('localhost',11211);
		
		$this->mcache = new Mcache('',$this->memcache);
		
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