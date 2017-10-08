<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Workerman;

use iTXTech\SimpleFramework\Console\Logger;
use Workerman\Protocols\Http;

/**
 *  WebServer.
 */
class WebServer extends Worker{

	/**
	 * Mime mapping.
	 *
	 * @var array
	 */
	protected static $mimeTypeMap = array();


	/**
	 * Used to save user OnWorkerStart callback settings.
	 *
	 * @var callback
	 */
	protected $_onWorkerStart = null;

	/** @var callable */
	public $onReceive;

	/**
	 * Construct.
	 *
	 * @param string $socket_name
	 * @param array  $context_option
	 */
	public function __construct($socket_name, $context_option = array()){
		list(, $address) = explode(':', $socket_name, 2);
		parent::__construct('http:' . $address, $context_option);
		$this->name = 'WebServer';
	}

	/**
	 * Run webserver instance.
	 *
	 * @see Workerman.Worker::run()
	 */
	public function run(){
		$this->_onWorkerStart = $this->onWorkerStart;
		$this->onWorkerStart = array($this, 'onWorkerStart');
		$this->onMessage = array($this, 'onMessage');
		parent::run();
	}

	/**
	 * Emit when process start.
	 *
	 * @throws \Exception
	 */
	public function onWorkerStart(){
		// Init mimeMap.
		$this->initMimeTypeMap();

		// Try to emit onWorkerStart callback.
		if($this->_onWorkerStart){
			try{
				call_user_func($this->_onWorkerStart, $this);
			}catch(\Throwable $e){
				Logger::logException($e);
			}
		}
	}

	/**
	 * Init mime map.
	 *
	 * @return void
	 */
	public function initMimeTypeMap(){
		$mime_file = Http::getMimeTypesFile();
		if(!is_file($mime_file)){
			Logger::info("$mime_file mime.type file not fond");
			return;
		}
		$items = file($mime_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if(!is_array($items)){
			Logger::info("get $mime_file mime.type content fail");
			return;
		}
		foreach($items as $content){
			if(preg_match("/\s*(\S+)\s+(\S.+)/", $content, $match)){
				$mime_type = $match[1];
				$workerman_file_extension_var = $match[2];
				$workerman_file_extension_array = explode(' ', substr($workerman_file_extension_var, 0, -1));
				foreach($workerman_file_extension_array as $workerman_file_extension){
					self::$mimeTypeMap[$workerman_file_extension] = $mime_type;
				}
			}
		}
	}

	/**
	 * Emit when http message coming.
	 *
	 * @param Connection\TcpConnection $connection
	 * @return void
	 */
	public function onMessage($connection){
		// REQUEST_URI.
		$workerman_url_info = parse_url($_SERVER['REQUEST_URI']);
		if(!$workerman_url_info){
			Http::header('HTTP/1.1 400 Bad Request');
			$connection->close('<h1>400 Bad Request</h1>');
			return;
		}
		$_SERVER['REMOTE_ADDR'] = $connection->getRemoteIp();
		$_SERVER['REMOTE_PORT'] = $connection->getRemotePort();

		$content = call_user_func($this->onReceive, $connection);

		if(strtolower($_SERVER['HTTP_CONNECTION']) === "keep-alive"){
			$connection->send($content);
		}else{
			$connection->close($content);
		}
	}
}
