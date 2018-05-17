<?php
namespace j4rek\database;

use j4rek\database\exceptions\DBException;

/**
 * db class.
 *
 * PDO lib
 *
 */
class database{
	/**
	 * motor
	 * mysql - sqlserver - postgres
	 * @var mixed
	 * @access private
	 */
	static private $motor;

	/**
	 * ip
	 * Host de conexion
	 * @var mixed
	 * @access private
	 */
	static private $ip;

	/**
	 * base
	 * base de datos
	 * @var mixed
	 * @access private
	 */
	static private $base;

	/**
	 * usuario
	 *
	 * @var mixed
	 * @access private
	 */
	static private $usuario;

	/**
	 * clave
	 *
	 * @var mixed
	 * @access private
	 */
	static private $clave;

	/**
	 * conexion
	 * cadena de conexion
	 * @var mixed
	 * @access private
	 */
	static private $conexion;

	/**
	 * link
	 * objeto conexion
	 * @var mixed
	 * @access public
	 */
	static $link;

	/**
	 * stmt
	 * Statement PDO
	 * @var mixed
	 * @access public
	 * @static
	 */
	static $stmt;

	/**
	 * lastId
	 * LastInsertId PDO
	 * @var int
	 * @access public
	 * @static
	 */
	static $lastId;

	/**
	 * debug
	 * Set Debug mode
	 * @var bool
	 * @access public
	 * @static
	 */
	static $debug;

	/**
	 * init function.
	 *
	 * @access public
	 * @param mixed $motor
	 * @param mixed $ip
	 * @param mixed $base
	 * @param mixed $usuario
	 * @param mixed $clave
	 * @return void
	 */
	static function turnOn($config = null){
		if(!$config){
			$config = include __DIR__ . '/config.php';
		}
		self::$conexion = $config['db']['driver'] . ":host=" . $config['db']['host'] . ";dbname=" . $config['db']['database'] . ";";
		try{
			self::$link = new \PDO(self::$conexion, $config['db']['user'], $config['db']['pass'],array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
			self::$link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}catch(\PDOException $ex){
			throw new DBException($ex->getMessage(), $ex->getCode(), true);
		}
	}

	/**
	 * consulta function.
	 * Ejecuta la consulta sql
	 * @access public
	 * @static
	 * @param mixed $consulta (default: null)
	 * @param mixed $fetch (default: true) | return array assoc if true or statement object if false
	 * @param bool $debug (default: false)
	 * @return void
	 */
	static function executeQuery($consulta = null, $opts = []){
		$options = array_replace(['fetch'=>true, 'debug'=>self::$debug], $opts);
		if(!isset(self::$link)){
			self::turnOn();
		}
		try{
			if(isset($consulta) && !is_null($consulta)){
				self::$stmt = self::$link->prepare($consulta);
				$queryResult = self::$stmt->execute();
				if($queryResult){
					self::$lastId = self::$link->lastInsertId();
					if(self::rowCount() > 0){
						if($options['fetch'] && strtolower(substr(trim($consulta), 0, 6)) == 'select'){
							return self::$stmt->fetchAll(\PDO::FETCH_ASSOC);
						}else{
							return self::$stmt;
						}
					}
				}
			}
			self::$stmt = null;
		}catch(\PDOException $ex){
			throw new DBException($ex->getMessage(), (int)$ex->getCode(), $options['debug']);
		}
	}

	/**
	 * rowCount function.
	 * retorna el numero de registros devueltos por la consulta
	 * @access public
	 * @static
	 * @param object $stmt [stament PDO, en caso de ser NULL utiliza el objeto propio de la clase]
	 * @return void
	 */
	static function rowCount($stmt = NULL){
		return !is_null($stmt) ? $stmt->rowCount() : self::$stmt->rowCount();
	}

	/**
	 * resultsToArray function.
	 * convierte la data del resultado en un array asociativo
	 * @access public
	 * @static
	 * @param mixed $result
	 * @return void
	 */
	static function resultToArray($result){
		return $result->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * resultToJson function.
	 * convierte la data del resultado a formato json
	 * @access public
	 * @static
	 * @param mixed $result
	 * @return void
	 */
	static function resultToJson($result){
		return json_encode($result->fetchAll(\PDO::FETCH_ASSOC));
	}

	/**
	 * setDebugMode function
	 * Establece si al ocurrir un error se muestra informacion o no
	 * @access public
	 * @static
	 * @param bool $active
	 * @return void
	 */
	static function setDebugMode($active){
		self::$debug = $active;
	}
}	

?>
