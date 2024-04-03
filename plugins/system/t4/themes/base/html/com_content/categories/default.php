<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Component\Content\Site\Helper\RouteHelper;

if(!class_exists('ContentHelperRoute')){
	if(version_compare(JVERSION, '4', 'ge')){
		abstract class ContentHelperRoute extends RouteHelper{};
	}else{
		JLoader::register('ContentHelperRoute', $com_path . '/helpers/route.php');
	}
}

HTMLHelper::_('behavior.core');

// Add strings for translations in Javascript.
Text::script('JGLOBAL_EXPAND_CATEGORIES');
Text::script('JGLOBAL_COLLAPSE_CATEGORIES');

$js = <<<JS
(function() {
	document.addEventListener('DOMContentLoaded', function() {
		var categories = [].slice.call(document.querySelectorAll('.categories-list'));

		categories.forEach(function(category) {
			var buttons = [].slice.call(document.querySelectorAll('.categories-list'));

			buttons.forEach(function(button) {
				var span = button.querySelector('span');

				if(span) {
				  span.classList.toggle('icon-plus')
				  span.classList.toggle('icon-minus')
				}

				if (button.getAttribute('aria-label') === Joomla.Text._('JGLOBAL_EXPAND_CATEGORIES'))
				{
					button.setAttribute('aria-label', Joomla.Text._('JGLOBAL_COLLAPSE_CATEGORIES'));
				} else {
					button.setAttribute('aria-label', Joomla.Text._('JGLOBAL_EXPAND_CATEGORIES'));
				}
			})
	  })
	});
})();
JS;

// @todo move script to a file
Factory::getDocument()->addScriptDeclaration($js);
?>
<div class="com-content-categories categories-list">
	<?php
		echo LayoutHelper::render('joomla.content.categories_default', $this);
		echo $this->loadTemplate('items');
	?>
</div>
