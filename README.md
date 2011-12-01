Kohana Mcache
===

This module is an add-on for Kohana v3.2.0 that allows you to cache database queries (using the query builder) and to invalidate on a table per table basis. Has the ability to cache by unique user id.

Place into modules/mcache directory

Place the following code in bootstrap.php under Kohana::modules

	'mcache' => MODPATH.'mcache'	// Memcache query caching for the database