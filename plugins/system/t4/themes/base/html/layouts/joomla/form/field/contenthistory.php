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
use Joomla\CMS\Router\Route;
/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck       Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var   string   $link            The link for the content history page
 * @var   string   $label           The label text
 */
extract($displayData);
if(version_compare(JVERSION, '4', 'ge')){
  $attr_btn_modal = '  data-bs-toggle="modal" data-bs-target="#versionsModal" ';
  $footer_btn = ' data-bs-dismiss="modal" ';
}else{
  $attr_btn_modal = '  data-toggle="modal" data-target="#versionsModal" ';
  $footer_btn = ' data-dismiss="modal" ';
   $dataAttribute = " onclick=\"jQuery('#versionsModal').modal('show')\" title=\"".$label."\"";
}
$attr_btn_modal = \T4\Helper\T4Bootstrap::getAttrs(array('toggle'=>'modal','target'=>'#versionsModal'));
$footer_btn = \T4\Helper\T4Bootstrap::getAttrs(array('dismiss'=>'modal'));
echo HTMLHelper::_(
  'bootstrap.renderModal',
  'versionsModal',
  array(
    'url'    => Route::_($link),
    'title'  => $label,
    'height' => '100%',
    'width'  => '100%',
    'modalWidth'  => '80',
    'bodyHeight'  => '60',
    'footer' => '<button type="button" class="btn btn-secondary"'.$footer_btn.'aria-hidden="true">'
      . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
  )
);

?>
<button
  type="button"
  class="btn btn-secondary"
  <?php echo $attr_btn_modal;?>
  <?php echo $dataAttribute; ?>>
    <span class="icon-code-branch" aria-hidden="true"></span>
    <?php echo $label; ?>
</button>