<?php
namespace T4Admin\Action;

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class Typelist {
	public static function doLoad() {
		$input = Factory::getApplication()->input;
		$name =  $input->get('name', 'default');
		$type = $input->get('type');
		$value = \T4\Helper\Path::getFileContent(self::getPath($type, $name));
		$value = @json_decode($value);
		if (empty($value)) $value = [];

		return [
			'value' => $value
		];
	}

	public static function doSave() {
		$input = Factory::getApplication()->input->post;
		$name =  $input->get('name', 'default');

		$value =  $input->getRaw('value');
		$type =  $input->get('type');

		if (!$name || !$value || !$type) {
			return ['error' => 'Missing params'];
		}

		self::save($type, $name, $value);
		return ['ok' => 1, 'status' => self::getStatus($type, $name)];
	}


	public static function doClone() {
		$input = Factory::getApplication()->input->post;
		$name =  $input->get('name', 'default');
		$newname =  $input->get('newname', 'default');
		$type =  $input->get('type');

		if (!$name || !$newname || !$type) {
			return ['error' => 'Missing params'];
		}
		// check exist
		if (\T4\Helper\Path::findInTheme(self::getPath($type, $newname))) {
			return ['error' => 'New name existed!'];
		}

		$value = \T4\Helper\Path::getFileContent(self::getPath($type, $name));
		self::save($type, $newname, $value);

		return ['ok' => 1, 'status' => self::getStatus($type, $newname)];

	}

	public static function doDelete() {
		$input = Factory::getApplication()->input->post;
		$name =  $input->get('name', 'default');
		$type =  $input->get('type');

		if (!$name || !$type) {
			return ['error' => 'Missing params'];
		}
		// check exist
		$file = T4PATH_LOCAL . '/' . self::getPath($type, $name);
		if (!is_file($file)) {
			return ['error' => 'New name existed!'];
		}
		// delete
		if (!File::delete($file)) {
			return ['error' => 'Cannot delete'];
		}

		return ['ok' => 1, 'status' => self::getStatus($type, $name)];
	}


	protected static function getPath($type, $name) {
		return 'etc/' . $type . '/' . $name . '.json';
	}

	protected static function save ($type, $name, $value) {
		if (is_string($value)) {
			$value = json_decode($value, true);
		}

		self::presave($type, $name, $value);

		$value = json_encode($value);

		$file = T4PATH_LOCAL . '/' . self::getPath($type, $name);
		$dir = dirname($file);
		if (!is_dir($dir)) Folder::create($dir);
		File::write($file, $value);
	}

	private static function preSave($type, $name, &$value) {
		// preprocess for navigation, favicon file
		if ($type == 'site' && !empty($value['other_faviconFile'])) {
			$tpl = \T4Admin\Admin::getTemplate(true);
			// Convert favicon if it is image
			$faviconfile = $value['other_faviconFile'];
		}
	}


	public static function getStatus ($type, $name) {
		$local = $tpl = false;
		// status: org (origin), loc (local only), ovr (overwrite in local)
		$path = '/etc/' . $type . '/' . $name . '.json';
		// check local
		$lfile = T4PATH_LOCAL . $path;
		if (is_file($lfile)) $local = true;
		// check base & template
		$tfile = T4PATH_TPL . $path;
		if (is_file($tfile)) $tpl = true;
		$bfile = T4PATH_BASE . $path;
		if (is_file($bfile)) $tpl = true;

		if ($tpl && $local) return 'ovr';
		if ($tpl) return 'org';
		if ($local) return 'loc';
		return 'del';
	}
}
