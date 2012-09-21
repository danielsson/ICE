<?php
namespace Ice;

defined('SYSINIT') or die('<b>Error:</b> No direct access allowed');

require_once(dirname(__FILE__).'/../lib/Auth.php');

Auth::init(1); //Userlevel 1 or higher required

class IceCmsEdit extends \IceCms {
	
	/**
	 * Extend head function to include the necessary scripts
	 */
	public function head($include_jquery = true) {
		global $config;
		echo '<script type="text/javascript"> var iceBasePath = "' , $config['baseurl'], $config['sys_folder'], '";';
		echo 'var icePageName = "', ICE_CURRENT_PAGE, '"; </script>';
		
		if($include_jquery===true) {
			echo '<script type="text/javascript" src="', $config['baseurl'], $config['sys_folder'], 'lib/jquery.js"></script>';
			echo '<script type="text/javascript" src="', $config['baseurl'], $config['sys_folder'], 'lib/jquery_ui_custom.js"></script>';
		}
		echo '<script type="text/javascript" src="', $config['baseurl'], $config['sys_folder'], 'editor/editor.js"></script>';
		echo '<link href="', $config['baseurl'], $config['sys_folder'], 'editor/editor.css" rel="stylesheet" type="text/css" />';
		echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
	}
	
	public function e($field_name, $element, $type = 'field', $attrs = array()) {  //Inserts an element into the page. Equal to element().

		global $config;

		$attrs = array_change_key_case($attrs);
		if(!isset($attrs['class'])) {
			$attrs['class'] = '';
		}

		$attrs['data-ice-fieldname'] = $field_name;
		$attrs['class'] .= ' iceEditable';
		if($type == 'field') {
			$attrs['class'] .= ' iceField';
		} else {
			$attrs['class'] .= ' iceArea';
		}

		parent::e($field_name, $element, $type, $attrs);
	}

	public function img($field_name, $width=0, $height=0, $attrs = array()) {
		
		$attrs = array_change_key_case($attrs);
		if(!isset($attrs['class'])) {
			$attrs['class'] = "";
		}
		$attrs['class'] .= ' iceEditable iceImage';
		$attrs['data-ice-fieldname'] = $field_name;
		
		parent::img($field_name, $width, $height, $attrs);

	}
	
}
?>