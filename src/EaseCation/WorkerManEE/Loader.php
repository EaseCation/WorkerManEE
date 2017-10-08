<?php
/**
 * Created by IntelliJ IDEA.
 * User: PeratX
 * Date: 2017/10/8
 * Time: 14:03
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