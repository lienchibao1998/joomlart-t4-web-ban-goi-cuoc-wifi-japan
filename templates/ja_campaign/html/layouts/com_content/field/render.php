<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (!key_exists('field', $displayData))
{
	return;
}

$field = $displayData['field'];
$label = Text::_($field->label);
$value = $field->value;
$showLabel = $field->params->get('showlabel');
$labelClass = $field->params->get('label_render_class');

if ($value == '')
{
	return;
}
?>

<?php if ($showLabel == 1) : ?>
	<span class="field-label <?php echo $labelClass; ?>"><?php echo htmlentities($label, ENT_QUOTES | ENT_IGNORE, 'UTF-8'); ?> </span>
<?php endif; ?>

<?php if($field->name == "coupon-code") { ?>
<span class="coupon-wrap">
  <span class="field-value" id='cp_coupon'><?php echo $value; ?></span>
  <button type="button" id="cp_copy" style="display:none;"><i class="fa fa-copy"></i>Copy</button type="button">
</span>
<?php }else if ($field->name == "offer-image") {?>
	<span class="field-value" id='cp_image'><?php echo $value; ?></span>
<?php } else { ?>
  <span class="field-value"><?php echo $value; ?></span>
<?php } ?>
