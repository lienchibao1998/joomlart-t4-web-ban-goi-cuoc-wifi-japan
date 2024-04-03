<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  2.5
 */
class JFormFieldLegend extends FormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Legend';
	protected function getInput()
	{
		$html = '';

		return $html;
	}

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{

		$html = '';
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? Text::_($text) : $text;
		$desc = $this->element['panel-heading'] ? (string) $this->element['panel-heading'] : '';
		$desc = $this->translateLabel ? Text::_($desc) : $desc;
		$class = 'legend';
		$class .= 	$text ? ' ' . preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($text))) : '';
		//$class .= 	$text ? ' ' . str_replace(' ', '_', strtolower($text)) : '';
		$class .= !empty($this->class) ? ' ' . $this->class : '';
		$class .= $this->element['subgroup'] ? ' sub-legend' : '';
		$class = 'class="' . $class . '"';
		$icon = $this->element['icon'] ? '<span class="fal fa-' . $this->element['icon'] . '"></span>' : '';
		//
		$expend = $this->element['expend'] ? ' data-expend="' . $this->element['expend'] . '"' : '';

		$tooltip = $desc ? ' class="hasTooltip" title="' . htmlentities($desc) . '"' : '';
		$html .= "<div $class$expend>$icon<div class='item-content'><span class='item-title'>$text</span><span class='item-desc'>$desc</span></div>";
		$html .='</div>';
		return $html;
	}

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		// get template name
		$path = str_replace (JPATH_ROOT, '', dirname(__DIR__));
		$path = str_replace ('\\', '/', substr($path, 1));

		$doc = Factory::getDocument();
		$doc->addStyleSheet (Uri::root() . $path . '/assets/css/legend.css');
		//$doc->addScript ('http://livejs.com/live.js#css');
		return parent::setup($element, $value, $group);
	}

}
