<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

$params  = $displayData->params;
$images  = json_decode($displayData->images);
if (empty($images->image_intro))
{
	return;
}

$imgclass   = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro;
$layoutAttr = [
	'src' => $images->image_intro,
	'alt' => empty($images->image_intro_alt) && empty($images->image_intro_alt_empty) ? false : $images->image_intro_alt,
];
$added_link_image = version_compare(JVERSION, '4.0', '>=') ? $params->get('link_intro_image') :$params->get('link_titles');
?>
<figure class="pull-<?php echo $this->escape($imgclass); ?> item-image">
	<?php if ($added_link_image && ($params->get('access-view') || $params->get('show_noauth', '0') == '1')) : ?>
		<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>" itemprop="url" title="<?php echo $this->escape($displayData->title); ?>">
			<?php echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnailUrl'])); ?>
		</a>
	<?php else : ?>
		<?php echo LayoutHelper::render('joomla.html.image', array_merge($layoutAttr, ['itemprop' => 'thumbnail'])); ?>
	<?php endif; ?>
	<?php if (isset($images->image_intro_caption) && $images->image_intro_caption !== '') : ?>
		<figcaption class="caption"><?php echo $this->escape($images->image_intro_caption); ?></figcaption>
	<?php endif; ?>
</figure>
