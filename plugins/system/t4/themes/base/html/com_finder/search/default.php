<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if(version_compare(JVERSION, '4', 'ge')){
	$this->document->getWebAssetManager()
		->useStyle('com_finder.finder')
		->useScript('com_finder.finder');
}else{
	HTMLHelper::_('behavior.core');
	HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

	HTMLHelper::_('stylesheet', 'com_finder/finder.css', array('version' => 'auto', 'relative' => true));
	HTMLHelper::_('stylesheet', 'vendor/awesomplete/awesomplete.css', array('version' => 'auto', 'relative' => true));

	Text::script('MOD_FINDER_SEARCH_VALUE', true);

	HTMLHelper::_('stylesheet', 'com_finder/finder.css', array('version' => 'auto', 'relative' => true));
	HTMLHelper::_('script', 'com_finder/finder.js', array('version' => 'auto', 'relative' => true));
}

?>
<div class="com-finder finder">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1>
			<?php if ($this->escape($this->params->get('page_heading'))) : ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
		</h1>
	<?php endif; ?>
	<?php if ($this->params->get('show_search_form', 1)) : ?>
		<div id="search-form" class="com-finder__form">
			<?php echo $this->loadTemplate('form'); ?>
		</div>
	<?php endif; ?>
	<?php // Load the search results layout if we are performing a search. ?>
	<?php if ($this->query->search === true) : ?>
		<div id="search-results" class="com-finder__results">
			<?php echo $this->loadTemplate('results'); ?>
		</div>
	<?php endif; ?>
</div>
