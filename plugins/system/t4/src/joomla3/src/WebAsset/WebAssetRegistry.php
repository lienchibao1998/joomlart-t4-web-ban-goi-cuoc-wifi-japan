<?php
namespace Joomla\CMS\WebAsset;


class WebAssetRegistry {
	var $assets = [];

	public function addRegistryFile($path) {
		$filepath = (strpos($path, JPATH_ROOT) === 0) ? $path : JPATH_ROOT . '/' . $path;

		// parse assets
		if (is_file($filepath)) {
			$data = file_get_contents($filepath);
			$data = $data ? json_decode($data, true) : null;

			if (!$data || empty($data['assets'])) return;

			// Keep source info
			$assetSource = [
				'registryFile' => $path,
			];

			// Prepare WebAssetItem instances
			foreach ($data['assets'] as $name => $item)
			{
				if (!empty($item['name'])) $name = $item['name'];
				$item['assetSource'] = $assetSource;
				$this->add($item['type'], $item);
			}
		}

		return $this;
	}

	public function get($type,$name) {
		return !empty($this->assets[$type][$name]) ? $this->assets[$type][$name] : null;
	}

	public function getAssets() {
		return $this->assets;
	}
	
	public function add($type,$asset) {
		$name = $asset['name'];
		$this->assets[$type][$name] = $asset;
	}
	/**
	 * Check whether the asset exists in the registry.
	 *
	 * @param   string  $type  Asset type, script or style
	 * @param   string  $name  Asset name
	 *
	 * @return  boolean
	 *
	 * @since   4.0.0
	 */
	public function exists(string $type, string $name)
	{
		return !empty($this->assets[$type][$name]);
	}
	public function createAsset($name, $asset = []) {
		$asset['name'] = $name;
		return $asset;
	}
}
