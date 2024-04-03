<?php

/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;

defined('JPATH_PLATFORM') or die;

if (!class_exists('ListFieldLegacy')) {
	if (version_compare(JVERSION, 4, 'ge')) {
		class ListFieldLegacy extends ListField
		{
		}
	} else {
		class ListFieldLegacy extends JFormFieldList
		{
		}
	}
}

class JFormFieldT4layouts extends ListFieldLegacy
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 't4layouts';

	protected function getOptions()
	{
		$options = [];
		$options[] = (object) ['value' => '', 'text' => Text::_('JGLOBAL_INHERIT')];
		// get all exist layouts
		$layouts = \T4\Helper\Path::files('etc/layout');
		if (!empty($layouts)) {
			foreach ($layouts as $layout) {
				$options[] = (object) ['value' => $layout, 'text' => $layout];
			}
		}

		return $options;
	}
}
