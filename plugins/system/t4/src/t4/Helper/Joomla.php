<?php

namespace T4\Helper;

use Joomla\Filesystem\File;

class Joomla
{
	// Create alias class for original call in $filepath, then overload the class
	public static function makeAlias($filepath, $originClassName, $aliasClassName)
	{
		if (!is_file($filepath)) return false;

		$cachePath = JPATH_CACHE . '/t4core/';
		$fileInfo = pathinfo($filepath);
		$cacheFile = $cachePath . $fileInfo['basename'];
		$fileTime = filemtime($filepath);

		if (!is_file($cacheFile) || filemtime($cacheFile) < $fileTime) {
			$code = file_get_contents($filepath);
			$code = str_replace('class ' . $originClassName, 'class ' . $aliasClassName, $code);

			File::write($cacheFile, $code);
		}

		require_once $cacheFile;
	}
}
