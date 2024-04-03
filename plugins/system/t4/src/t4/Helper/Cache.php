<?php
namespace T4\Helper;

use Joomla\CMS\Cache\Cache as CacheCache;
use Joomla\CMS\Factory;

class Cache {
	static $cache_group = 't4';
	static $attribs = ['_scripts', '_styleSheets', '_script', '_style', '_links', '_custom', '_file'];

	public static function getCache() {
		$conf = Factory::getConfig();

		$devmode = (int)$conf->get('devmode', 0);
		$caching = (int)$conf->get('caching', 0);
		$options = [
			'caching' => !$devmode && $caching ? true : false,
			'defaultgroup' => self::$cache_group,
			'cachebase'    => $conf->get('cache_path', JPATH_CACHE),
			'lifetime' => (int)$conf->get('cachetime', 60) // use default joomla
		];

		return CacheCache::getInstance('', $options);
	}

	public static function clean() {
		$cache = self::getCache();
		$cache->clean(self::$cache_group);
	}

	public static function store($key, $data) {

		$cache = self::getCache();
		$cache->store($data, $key, self::$cache_group);

		return $key;
	}

	public static function load($key) {

		$cache = self::getCache();
		$data = $cache->get($key, self::$cache_group);

		if ($data) {
			//$data = @json_decode($data, true);
			return $data;
		}

		return null;
	}


	public static function loadLayout($key) {
		$data = self::load($key);
		if (!is_array($data) || empty($data['layout'])) return null;
		// set css/js/style/script
		$doc = Factory::getDocument();

		// load attribs data
		foreach (self::$attribs as $attr) {
			if (!empty($data[$attr])) {
				// merge
				if (is_array($data[$attr]))  {
					foreach ($data[$attr] as $key => $val) {
						if (empty($doc->$attr[$key])) $doc->$attr[$key] = $val;
					}
				} else {
					$doc->$attr = $data[$attr];
				}
			}
		}

		// enable webasset data
		if (!empty($data['assets'])) {
			$wam = \T4\Helper\Asset::getWebAssetManager();
			foreach ($data['assets'] as $type => $assets){
				forEach($assets as $name){
					if($wam->assetExists($type,$name)){
						$wam->useAsset($type,$name);	
					}
					
				}
			}
		}


		return $data['layout'];
	}

	public static function storeLayout($key, $layout) {
		$data = ['layout' => $layout];
		$doc = Factory::getDocument();
		$wam = \T4\Helper\Asset::getWebAssetManager();
		$assetsType = array('script','style','preset');
		forEach($assetsType as $type){
			// store webasset data
			$assets = $wam->getAssets($type);
			if (!empty($assets)) $data['assets'][$type] = array_keys($assets);
		}
		// store attribs data
		// load attribs data
		foreach (self::$attribs as $attr) {
			if (!empty($doc->$attr)) {
				$data[$attr] = $doc->$attr;
			}
		}

		self::store($key, $data);
	}

}
