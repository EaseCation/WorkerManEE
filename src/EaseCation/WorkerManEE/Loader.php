<?php

/**
 *
 * WorkerManEE
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 */

namespace EaseCation\WorkerManEE;

use iTXTech\SimpleFramework\Module\Module;
use Workerman\Worker;

class Loader extends Module{
	/** @var Loader */
	private static $instance;

	public function load(){
		self::$instance = $this;
		RequestHandler::init();
	}

	public function unload(){
	}

	public static function getInstance(){
		return self::$instance;
	}

	public function doTick(int $currentTick){
		Worker::loop();
	}
}