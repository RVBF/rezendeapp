<?php

/**
 *	Classe para Debuger
 *  @author		Rafael Vinicius Barros Ferreira
 */
class Debuger {
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
	
}

?>
