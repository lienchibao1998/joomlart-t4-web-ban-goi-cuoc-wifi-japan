<?php

/**
 * @package     JT Search
 * @subpackage  mod_jt_search
 *
 * @copyright   (C) 2011 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Module\Finder\Site\Helper\FinderHelper;


// Load the smart search component language file.
$lang = $app->getLanguage();
$lang->load('com_finder', JPATH_SITE);

$input = '<input type="text" name="q" id="mod-finder-searchword' . $module->id . '" class="js-finder-search-query form-control" value="' . htmlspecialchars($app->input->get('q', '', 'string'), ENT_COMPAT, 'UTF-8') . '"'
    . ' placeholder="' . Text::_('MOD_JT_SEARCH_SEARCH_VALUE') . '">';

$showLabel  = $params->get('show_label', 1);
$labelClass = (!$showLabel ? 'visually-hidden ' : '') . 'finder';
$label      = '<label for="mod-finder-searchword' . $module->id . '" class="' . $labelClass . '">' . $params->get('alt_label', Text::_('JSEARCH_FILTER_SUBMIT')) . '</label>';

$output = '';

if ($params->get('show_button', 0)) {
    $output .= $label;
    $output .= '<div class="mod-finder__search input-group">';
    $output .= $input;
    $output .= '<button class="btn btn-primary" type="submit"><span class="icon-search icon-white" aria-hidden="true"></span> ' . Text::_('JSEARCH_FILTER_SUBMIT') . '</button>';
    $output .= '</div>';
} else {
    $output .= $label;
    $output .= $input;
}

Text::script('MOD_JT_SEARCH_SEARCH_VALUE', true);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_finder');

/*
 * This segment of code sets up the autocompleter.
 */
if ($params->get('JTshow_autosuggest', 1)) {
    $wa->usePreset('awesomplete');
    $app->getDocument()->addScriptOptions('finder-search', array('url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component', false)));
}

$wa->useScript('com_finder.finder');

?>

<div class="jt-search-wrap">
<nav>
      <button class="btn search-btn">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
      <div class="jt-search container" id="search-input-container">
        <form class="mod-jtsearch js-finder-searchform form-search" action="<?php echo Route::_($route); ?>" method="get" role="search">
    <?php echo $output; ?>

    <?php $show_advanced = $params->get('show_advanced', 0); ?>
    <?php if ($show_advanced == 2) : ?>
        <br>
        <a href="<?php echo Route::_($route); ?>" class="mod-finder__advanced-link"><?php echo Text::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
    <?php elseif ($show_advanced == 1) : ?>
        <div class="mod-finder__advanced js-finder-advanced">
            <?php echo HTMLHelper::_('filter.select', $query, $params); ?>
        </div>
    <?php endif; ?>
    <?php echo FinderHelper::getGetFields($route, (int) $params->get('set_itemid', 0)); ?>
</form>
        <button class="btn close-btn">
          <i class="fa fa-times"></i>
        </button>
      </div>
    </nav>
	</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
  function initializeDefaultLayout() {
    let searchBtn = document.querySelector(".search-btn");
    let closeBtn = document.querySelector(".close-btn");
    let searchInputContainer = document.getElementById("search-input-container");

    searchBtn.addEventListener("click", () => {
      searchInputContainer.classList.add("show");
    });

    closeBtn.addEventListener("click", () => {
      searchInputContainer.classList.remove("show");
      searchInputContainer.querySelector("input").value = "";
    });
  }

  // Call the function for the default layout
  initializeDefaultLayout();
    });
</script>
