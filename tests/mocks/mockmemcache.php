<?php
/**
 * Mock Memcache module
 *
 * @package    Kohana_Mcache
 * @author     Bryce Bedwell <bryce@familylink.com>
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
if( ! class_exists('Memcache') )
{
	class Memcache {}
}

class MockMemcache extends Memcache {
	
	private $environment;
	
	public function construct() 
	{
		$this->environment = array();
	}
	
	public function get($key)
	{
		$value = @$this->environment[$key];
		
		return $value;
	}
	
	public function set($key,$value)
	{
		$this->environment[$key] = $value;
	}
	
	public function delete($key)
	{
		unset($this->environment[$key]);
	}
	
	public function flush()
	{
		$this->environment = array();
	}
	
	public function addServer($host, $port)
	{
		
	}
	
}