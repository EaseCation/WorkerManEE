<?php

/**
 *
 * WorkerManEE
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 */

namespace EaseCation\WorkerManEE;

use FunDNS\Page\AbstractPage;
use Workerman\Protocols\Http;
use Workerman\Worker;

class RequestHandler{
	const USER_AGENT = "EaseCation Client";

	public static $prefix;

	/** @var AbstractPage[] */
	private static $knownPages;

	public static function init(){
		self::$prefix = "WorkerMan EaseCation Edition v" . Worker::VERSION . "<br>EaseCation Server Page v" .
			Loader::getInstance()->getInfo()->getVersion() . "<br>";
	}

	public static function registerPage(string $name, string $class){
		self::$knownPages[$name] = $class;
	}

	public static function process(): string{
		/*if($server["HTTP_USER_AGENT"] != self::USER_AGENT){
			Http::header("HTTP/1.1 403 Forbidden");
			return self::$prefix . "403 Forbidden";
		}*/
		$uri = $_SERVER["REQUEST_URI"];
		if(strstr($uri, "?")){
			$uri = explode("?", $uri)[0];
		}
		if(isset(self::$knownPages[$uri])){
			return self::$knownPages[$uri]::onRequest();
		}else{
			Http::header("HTTP/1.1 404 Not Found");
			return self::$prefix . "404 Not Found " . $uri;
		}
	}
}