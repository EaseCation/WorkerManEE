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

namespace EaseCation\WorkerManEE\Page;

abstract class AbstractPage{
	abstract public static function onRequest();

	public static function getParameters(string $raw){
		$args = [];
		foreach(explode("&", $raw) as $arg){
			if(!strstr($arg, "=")){
				continue;
			}
			$a = explode("=", $arg);
			$args[$a[0]] = $a[1];
		}
		return $args;
	}
}