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

$params  = $displayData->params;
$images  = json_decode($displayData->images);

if (empty($images->image_fulltext))
{
	return;
}

$imgclass   = empty($images->float_fulltext) ? $params->get('float_fulltext') : $images->float_fulltext;
$layoutAttr = [
	'src'      => $images->image_fulltext,
	'itemprop' => 'image',
	'alt'      => empty($images->image_fulltext_alt) && empty($images->image_fulltext_alt_empty) ? false : $images->image_fulltext_alt,
];
?>
<figure class="pull-<?php echo $this->escape($imgclass); ?> item-image">
	<?php echo LayoutHelper::render('joomla.html.image', $layoutAttr); ?>
	<?php if (isset($images->image_fulltext_caption) && $images->image_fulltext_caption !== '') : ?>
		<figcaption class="caption"><?php echo $this->escape($images->image_fulltext_caption); ?></figcaption>
	<?php endif; ?>
</figure>
