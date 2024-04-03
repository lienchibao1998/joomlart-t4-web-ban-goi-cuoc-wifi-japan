<?php
namespace T4\Optimizer;

use Joomla\CMS\Factory;

class Js extends Base {
	var $params;
	var $default_options;
	var $exclude;

	public function __construct($params) {
		parent::__construct($params);

		// default group
		$this->default_options = [
			'type' => 'text/javascript',
			'options' => []
		];

		// exclude
		$exclude = trim($params->get('system_optimizejs_exclude', ''));
		$exclude = $exclude ? $exclude . "\ntinymce(\.min)?\.js" : 'tinymce.';
		// optimize exclude file php support dt register calendar
		$exclude .=  "\n.php";
		$this->setExclude($exclude);

		// output path
		$this->outputpath = '/optimize/js';
		$this->outputext = '.js';
	}

	// protected function getMinifier() {
	// 	return new Minify\Js();
	// }

	protected function getSources() {
		$doc = Factory::getDocument();
		return $doc->_scripts;
	}

	protected function setSources($output) {
		$doc = Factory::getDocument();
		$doc->_scripts = $output;
	}

}
