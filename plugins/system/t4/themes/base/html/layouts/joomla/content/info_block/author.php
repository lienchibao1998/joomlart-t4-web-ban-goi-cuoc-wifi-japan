<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use T4\Helper\Author as Author;
$params = $displayData['item']->params;
$doc =  Factory::getDocument();
$author_info = Author::authorInfo($displayData['item']);
if(!$params->get('show_author')) return;
// load language t4
Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
?>
<dd class="createdby" itemprop="author" itemscope itemtype="https://schema.org/Person">
	<?php $author = $img_alt = ($displayData['item']->created_by_alias ?: $displayData['item']->author); ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php if(isset($author_info->author_avatar)):?>
		<span  class="author-img">
		<?php if(!empty($author_info->link)): ?>
		<a href="<?php echo $author_info->link;?>" title="<?php echo $img_alt;?>">
		<?php endif; ?>
		<?php echo LayoutHelper::render('joomla.html.image',array('src'=>$author_info->author_avatar,'alt'=>$img_alt));  ?>
		<?php if(!empty($author_info->link)): ?>
		</a>
		<?php endif;?>
		</span>
	<?php endif;?>
	<?php if (!empty($author_info->link )) : ?>
		<?php echo Text::sprintf('TPL_CONTENT_WRITTEN_BY', HTMLHelper::_('link', $author_info->link, $author, array('itemprop' => 'url'))); ?>
	<?php else : ?>
		<?php echo Text::sprintf('TPL_CONTENT_WRITTEN_BY', $author); ?>
	<?php endif; ?>
</dd>

<span style="display: none;" itemprop="publisher" itemtype="http://schema.org/Organization" itemscope>
	<?php $author = ($displayData['item']->created_by_alias ?: $displayData['item']->author); ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php echo $author; ?>
</span>
