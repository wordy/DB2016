<?php

App::uses('FilepickerInfo', 'Filepicker.Lib');
class FilepickerHelper extends AppHelper {
    var $helpers = array('Html');

	function scriptTag() {
		$apikey = FilepickerInfo::getConfig('apikey');
		$out = array();
		$out[] = $this->Html->script('//api.filepicker.io/v2/filepicker.js');
		$out[] = $this->Html->scriptBlock("filepicker.setKey('$apikey')");
		return implode("\n", $out);
	}

}

