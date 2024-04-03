<?php
/**
T4 Overide
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if(!class_exists('ContentHelperRoute') && version_compare(JVERSION, '4', 'ge')) {
	class ContentHelperRoute extends \Joomla\Component\Content\Site\Helper\RouteHelper{};
}
if(empty($displayData['item']->parent_language)) $displayData['item']->parent_language = "*";
?>
<dd class="parent-category-name hasTooltip" title="<?php echo Text::sprintf('COM_CONTENT_PARENT', ''); ?>">
	<i class="fa fa-folder"></i>
	<?php $title = $this->escape($displayData['item']->parent_title); ?>
	<?php if ($displayData['params']->get('link_parent_category') && !empty($displayData['item']->parent_id)) : ?>
		<?php $url = '<a href="' . Route::_(ContentHelperRoute::getCategoryRoute($displayData['item']->parent_id, $displayData['item']->parent_language)) . '" itemprop="genre">' . $title . '</a>'; ?>
		<?php echo Text::sprintf('COM_CONTENT_PARENT', $url); ?>
	<?php else : ?>
		<?php echo Text::sprintf('COM_CONTENT_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
	<?php endif; ?>
</dd>