<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Form\Field\RadioField;
use Joomla\CMS\Form\FormHelper;

defined('JPATH_PLATFORM') or die;
FormHelper::loadFieldClass('radio');

if (!class_exists('RadioFieldLegacy')) {
	if (version_compare(JVERSION, 4, 'ge')) {
		class RadioFieldLegacy extends RadioField
		{
		}
	} else {
		class RadioFieldLegacy extends JFormFieldRadio
		{
		}
	}
}

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldT4Switch extends RadioFieldLegacy
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'T4Switch';
	protected function getInput()
	{
		if(version_compare(JVERSION, '4', 'lt')){
			$this->layout = 'joomla.form.field.radio';
		}
		return parent::getInput();
	}
	protected function getOptions()
	{
		$options = parent::getOptions();
		if(version_compare(JVERSION, '4', 'lt')){
			$options = array_reverse($options);
		}
		return $options;
	}
}
