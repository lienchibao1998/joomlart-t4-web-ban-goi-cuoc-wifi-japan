<?php
/**
T4 Overide
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$title      = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
$anchor_css = $item->anchor_css ?: '';

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

if ($item->level > 1) {
	$anchor_css .= " dropdown-item";
} else {
	$anchor_css .= " nav-link";
}

$attributes = '';

if($item->deeper){
	if(isset($item->mega_sub)){
		$anchor_css .= ' dropdown-toggle';
	}
	$attributes .= ' role = "button"';
	$attributes .= ' aria-haspopup = "true"';
	$attributes .= ' aria-expanded = "false"';
	$attributes .= $params->get('jamegamenu') ? '' : ' data-toggle = "dropdown"';
}

$itemCaption = !empty($item->caption)  ? '<span class="menu-item-caption">' . $item->caption . '</span>'  : "";
$linktype = (!empty($item->icon)  ? $item->icon  : "") . $linktype  . $itemCaption;

?>
<a itemprop="url" href="javascript:;" class="nav-header <?php echo $anchor_css; ?>"<?php echo $title; ?> <?php echo $attributes; ?>>
	<span itemprop="name"><?php echo $linktype; ?></span>
	<?php echo !empty($item->caret)  ? $item->caret  : "" ?>
</a>
