<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use T4\Helper\J3J4;
use T4\Helper\T4Bootstrap;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$canEdit   = $displayData['params']->get('access-edit');
$articleId = $displayData['item']->id;

?>
<?php if ($canEdit) : ?>
	<div class="icons float-right float-end">
		<div class="edit-link">
			<?php echo HTMLHelper::_('icon.edit', $displayData['item'], $displayData['params']); ?>
		</div>
	</div>
<?php endif; ?>