<?php
namespace Joomla\CMS\WebAsset;

use Joomla\CMS\Factory;

class WebAssetManager {
	var $registry = null;
	var $activeAssets = [];

	public function __construct() {
		// create registry
		$this->registry = new WebAssetRegistry();

		$this->debug = (int)Factory::getConfig()->get('debug');
		//$this->registry->addRegistryFile(T4PATH_BASE . $coreasset);
		//$this->registry->addRegistryFile(T4PATH_TPL . $coreasset);
	}

	public function getRegistry() {
		return $this->registry;
	}

	public function useAsset($type,$name) {

		// already enabled
		if (isset($this->activeAssets[$type][$name])) return;
		$asset = $this->registry->get($type,$name);
		if ($asset) {
			$this->activeAssets[$type][$name] = $asset;
			// enable dependency
			if (!empty($asset['dependencies'])) {
				foreach($asset['dependencies'] as $dep) {
					$this->useAsset($type,$dep);
				}
			}

			// add assets to document
			$doc = Factory::getDocument();
			if (!empty($asset['css'])) {
				foreach ($asset['css'] as $url) {
					$url = preg_match('/^(https?:)?\/\//', $url) ? $url : \T4\Helper\Path::findInTheme($url, true);
					if (!$url) continue;
					// remove .min if in debug mode
					if ($this->debug && preg_match('/media\/jui\/css/', $url)) {
						$url = str_replace('.min.css', '.css', $url);
					}
					$doc->addStylesheet($url);
				}
			}

			if (!empty($asset['js'])) {
				foreach ($asset['js'] as $url) {
					$url = preg_match('/^(https?:)?\/\//', $url) ? $url : \T4\Helper\Path::findInTheme($url, true);
					if (!$url) continue;
					// remove .min if in debug mode
					if ($this->debug && preg_match('/media\/jui\/js/', $url)) {
						$url = str_replace('.min.js', '.js', $url);
					}
					$doc->addScript($url);
				}
			}
			if (!empty($asset['uri']) && !empty($asset['assetSource'])) {
				$url_bs5 = preg_match('/^(https?:)?\/\//', $asset['uri']) ? $asset['uri'] : \T4\Helper\Path::findInTheme($asset['uri'], true);
					// remove .min if in debug mode
					if ($this->debug && preg_match('/media\/jui\/js/', $url_bs5)) {
						$url_bs5 = str_replace('.min.js', '.js', $url_bs5);
					}
					$doc->addScript($url_bs5);
			}
		}
	}
	public function useScript($name){
		$this->useAsset('script',$name);
	}
	public function useStyle($name){
		$this->useAsset('style',$name);
	}

	public function assetExists($type,$name){
		return $this->registry->exists($type,$name); 
	}

	public function getAssets($type, $sort = false) {
		return !empty($this->activeAssets[$type]) ? $this->activeAssets[$type] : [];
	}
}
