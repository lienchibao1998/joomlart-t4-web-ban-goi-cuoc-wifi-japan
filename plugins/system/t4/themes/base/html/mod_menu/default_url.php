<?php
/**
T4 Overide
 */

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$attributes = array();
$attributes['itemprop'] = 'url';

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

// T4: add class nav-link
if ($item->anchor_css)
{
	if(($item->level > 1)) {
		$attributes['class'] = $item->anchor_css . ' dropdown-item';
	} else {
		$attributes['class'] = $item->anchor_css . ' nav-link';
	}
}else{
	if(($item->level > 1)) {
		$attributes['class'] = ' dropdown-item';
	} else {
		$attributes['class'] = ' nav-link';
	}
}

if (empty($item->caret)) $item->caret = '';

if ($item->anchor_rel)
{
	$attributes['rel'] = $item->anchor_rel;
}

$linktype = '<span class="menu-item-title">' . $item->title . '</span>';

if ($item->menu_image)
{
	$itemParams = version_compare('4','ge') ? $item->getParams() : $item->params;

	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title);
	}
	if ($itemParams->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

if ($item->browserNav == 1)
{
	$attributes['target'] = '_blank';
	$attributes['rel'] = 'noopener noreferrer';
}
elseif ($item->browserNav == 2)
{
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $params->get('window_open');

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

if($item->deeper){
	if (!preg_match('/http/', $item->flink) && preg_match('/#/', $item->flink)) {
		$attributes['class'] .= ' dropdown-toggle anchoring';
	}else {
		$attributes['class'] .= ' dropdown-toggle';
	}
	$attributes['role'] = 'button';
	$attributes['aria-haspopup'] = 'true';
	$attributes['aria-expanded'] = 'false';
	$attributes['data-toggle'] = $params->get('jamegamenu') ? '' : 'dropdown';
}

$itemCaption = !empty($item->caption)  ? '<span class="menu-item-caption">' . $item->caption . '</span>'  : "";

$linktype = (!empty($item->icon)  ? $item->icon  : "") . $linktype . $itemCaption;

$linktype = '<span itemprop="name">'.$linktype.'</span>' . (!empty($item->caret)  ? $item->caret  : "");

echo HTMLHelper::_('link', OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
