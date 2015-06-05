<?php



/**
 * Database model.
 */
class Database extends Object
{
	/** @var DibiConnection */
	private $db;


	public static function initialize()
	{
		$db = dibi::connect(Environment::getConfig('database'));
	}

}
