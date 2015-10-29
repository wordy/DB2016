<?php

/* Filepicker Plugin Information */
class FilepickerInfo {

	/**
	  * Filepicker configuration stored in app/Config/filepicker.php
	  * Example located at Filepicker/Config/filepicker.php.example
	  */
	static $_configs = array();

	/**
	  * Getting a config option. Reads over the file and gets the appropriate key
	  * Idea and implementation from github.com/webtechnick/CakePHP-Facebook-Plugin
	  */

	static function getConfig($key = null){
		if (!empty($key)) {
			if (isset(self::$_configs[$key]) || (self::$_configs[$key] = Configure::read("Filepicker.$key"))) { 
				return self::$_configs[$key]; 
			} elseif (Configure::load('filepicker') && (self::$_configs[$key] = Configure::read("Filepicker.$key"))) {
				return self::$_configs[$key]; 
			}
		} else {
			Configure::load('filepicker'); 
			return Configure::read('Filepicker');
		}
		return null; 
	}
	
	static $_information = array(
							'version' => '1.0.0',
							'name' => 'CakePHP Filepicker Plugin',
							'email' => 'plugins+cakephp@filepicker.io',
							'link' => 'https://www.filepicker.io',
							'license' => 'MIT',
							'description' => 'A simple way to upload files using Filepicker.io',
							);
	
	
	static function getInformation($key = null){
		if (!empty($key)) {
			return self::$_information[$key];
		} else {
			return self::$_information;
		}
		return null;
	}
}

?>