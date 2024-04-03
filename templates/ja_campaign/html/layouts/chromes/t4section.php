<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * html5 (chosen html5 tag and font header tags)
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs  = $displayData['attribs'];

// Get joomla default params
$badge          = preg_match ('/badge/', $params->get('moduleclass_sfx',''))? '<span class="badge">&nbsp;</span>' : '';
$moduleTag      = htmlspecialchars($params->get('module_tag', 'div'));
$headerTag      = htmlspecialchars($params->get('header_tag', 'h4'));
$headerClass    = $params->get('header_class');
$bootstrapSize  = $params->get('bootstrap_size');
$moduleClass    = !empty($bootstrapSize) ? ' span' . (int) $bootstrapSize . '' : '';
$moduleClassSfx = htmlspecialchars($params->get('moduleclass_sfx',''));
$moduleType = $module->module;

$modHeading = $params->get('mod-heading');
$modSubHeading = $params->get('mod-sub-heading');
$headingTextAlign = strtolower($params->get('heading-text-align'));

if (!empty ($module->content)) {

	$html = "<{$moduleTag} class=\"t4-section-inner section\">";

	$html .= "<div class=\"section-title-wrap text-{$headingTextAlign} {$headerClass}\">";
	$html .= "<{$headerTag}>" . $modHeading . "</{$headerTag}>";
	$html .= "<div class=\"sub-heading lead\">" . $modSubHeading . "</div>";
	$html .= "</div>";

	$html .="<div class=\"section-ct\">" . $module->content . "</div>";

	$html .= "</{$moduleTag}>";

	echo $html;
}