<?php

/**
 *	Classe para toArray
 *  @author		Rafael Vinicius Barros Ferreira
 */
class Util {
	static function printr($data) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";	
		exit;
	}

	static function consoleToLog($data) {
		if(is_array($data) || is_object($data))
		{
			echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
		} 
		else 
		{
			echo("<script>console.log('PHP: ".$data."');</script>");
		}
	}
	static function objetosParaArray($objects = []) {

		foreach($objects as $objeto) {
			self::toArray($objeto);
		}
	}

	// static function toArray($object) {
		
	// 	$reflectionClass = new ReflectionClass(get_class($object));
	// 	$array = array();
	// 	foreach ($reflectionClass->getProperties() as $property) {
	// 		$property->setAccessible(true);
	// 		$array[$property->getName()] = $property->getValue($object);
	// 		$property->setAccessible(false);
	// 	}
	// 	return $array;
	// }
}

?>
