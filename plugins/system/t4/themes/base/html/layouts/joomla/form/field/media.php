<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;


use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
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
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 *
 * @var   string   $preview         The preview image relative path
 * @var   integer  $previewHeight   The image preview height
 * @var   integer  $previewWidth    The image preview width
 * @var   string   $asset           The asset text
 * @var   string   $authorField     The label text
 * @var   string   $folder          The folder text
 * @var   string   $link            The link text
 */
extract($displayData);


$attr = '';

if(version_compare(JVERSION, '4', 'lt')):

// Load the modal behavior script.
HTMLHelper::_('behavior.modal');

// Include jQuery
HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'media/mediafield-mootools.min.js', array('version' => 'auto', 'relative' => true, 'framework' => true));

// Tooltip for INPUT showing whole image path
$options = array(
	'onShow' => 'jMediaRefreshImgpathTip',
);

HTMLHelper::_('behavior.tooltip', '.hasTipImgpath', $options);

if (!empty($class))
{
	$class .= ' hasTipImgpath';
}
else
{
	$class = 'hasTipImgpath';
}


$attr .= ' title="' . htmlspecialchars('<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8') . '"';

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="input-small field-media-input ' . $class . '"' : ' class="input-small"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

// The text field.
echo '<div class="input-prepend input-append">';

// The Preview.
$showPreview = true;
$showAsTooltip = false;

switch ($preview)
{
	case 'no': // Deprecated parameter value
	case 'false':
	case 'none':
		$showPreview = false;
		break;

	case 'yes': // Deprecated parameter value
	case 'true':
	case 'show':
		break;
	case 'tooltip':
	default:
		$showAsTooltip = true;
		$options = array(
				'onShow' => 'jMediaRefreshPreviewTip',
		);
		HTMLHelper::_('behavior.tooltip', '.hasTipPreview', $options);
		break;
}

// Pre fill the contents of the popover
if ($showPreview)
{
	if ($value && file_exists(JPATH_ROOT . '/' . $value))
	{
		$src = Uri::root() . $value;
	}
	else
	{
		$src = '';
	}

	$width = $previewWidth;
	$height = $previewHeight;
	$style = '';
	$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
	$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

	$imgattr = array(
		'id' => $id . '_preview',
		'class' => 'media-preview',
		'style' => $style,
	);

	$img = HTMLHelper::_('image', $src, Text::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
	$previewImg = '<div id="' . $id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
	$previewImgEmpty = '<div id="' . $id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
		. Text::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

	if ($showAsTooltip)
	{
		echo '<div class="media-preview add-on">';
		$tooltip = $previewImgEmpty . $previewImg;
		$options = array(
			'title' => Text::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
					'text' => '<span class="icon-eye" aria-hidden="true"></span>',
					'class' => 'hasTipPreview'
					);

		echo HTMLHelper::_('tooltip', $tooltip, $options);
		echo '</div>';
	}
	else
	{
		echo '<div class="media-preview add-on" style="height:auto">';
		echo ' ' . $previewImgEmpty;
		echo ' ' . $previewImg;
		echo '</div>';
	}
}

echo '	<input type="text" name="' . $name . '" id="' . $id . '" value="'
	. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' data-basepath="'
	. Uri::root() . '"/>';

?>
<a class="modal btn" title="<?php echo Text::_('JLIB_FORM_BUTTON_SELECT'); ?>" href="
	<?php echo ($readonly ? '' : ($link ? Uri::root() . $link : 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
	. $authorField) . '&amp;fieldid=' . $id . '&amp;folder=' . $folder) . '"'
	. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}"'; ?>">
 <?php echo Text::_('JLIB_FORM_BUTTON_SELECT'); ?></a><a class="btn hasTooltip" title="<?php echo Text::_('JLIB_FORM_BUTTON_CLEAR'); ?>" href="#" onclick="jInsertFieldValue('', '<?php echo $id; ?>'); return false;">
	<span class="icon-remove" aria-hidden="true"></span></a>
</div>
<?php else:

// Initialize some field attributes.
$attr .= !empty($class) ? ' class="form-control field-media-input ' . $class . '"' : ' class="form-control field-media-input"';
$attr .= !empty($size) ? ' size="' . $size . '"' : '';
$attr .= $dataAttribute;

// Initialize JavaScript field attributes.
$attr .= !empty($onchange) ? ' onchange="' . $onchange . '"' : '';

switch ($preview) {
	case 'no': // Deprecated parameter value
	case 'false':
	case 'none':
		$showPreview = false;
		break;
	case 'yes': // Deprecated parameter value
	case 'true':
	case 'show':
	case 'tooltip':
	default:
		$showPreview = true;
		break;
}

// Pre fill the contents of the popover
if ($showPreview) {
	if ($value && file_exists(JPATH_ROOT . '/' . $value)) {
		$src = Uri::root() . $value;
	} else {
		$src = '';
	}
	$width = $previewWidth;
	$height = $previewHeight;
	$style = '';
	$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
	$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

	$imgattr = array(
		'id' => $id . '_preview',
		'class' => 'media-preview',
		'style' => $style,
	);

	$img = HTMLHelper::_('image', $src, Text::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);

	$previewImg = '<div id="' . $id . '_preview_img">' . $img . '</div>';
	$previewImgEmpty = '<div id="' . $id . '_preview_empty"' . ($src ? ' class="hidden"' : '') . '>'
		. Text::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

	$showPreview = 'static';
}

// The url for the modal
$url = ($readonly ? ''
	: ($link ?: 'index.php?option=com_media&view=media&tmpl=component&mediatypes=' . $mediaTypes
		. '&asset=' . $asset . '&author=' . $authorId)
	. '&fieldid={field-media-id}&path=' . $folder);

// Correctly route the url to ensure it's correctly using sef modes and subfolders
$url = Route::_($url);
$doc = Factory::getDocument();
$wam = $doc->getWebAssetManager();

$wam->useScript('webcomponent.media-select');
$doc->addScriptOptions('media-picker-api', ['apiBaseUrl' => Uri::base() . 'index.php?option=com_media&format=json']);

Text::script('JFIELD_MEDIA_LAZY_LABEL');
Text::script('JFIELD_MEDIA_ALT_LABEL');
Text::script('JFIELD_MEDIA_ALT_CHECK_LABEL');
Text::script('JFIELD_MEDIA_ALT_CHECK_DESC_LABEL');
Text::script('JFIELD_MEDIA_CLASS_LABEL');
Text::script('JFIELD_MEDIA_FIGURE_CLASS_LABEL');
Text::script('JFIELD_MEDIA_FIGURE_CAPTION_LABEL');
Text::script('JFIELD_MEDIA_LAZY_LABEL');
Text::script('JFIELD_MEDIA_SUMMARY_LABEL');
Text::script('JFIELD_MEDIA_EMBED_CHECK_DESC_LABEL');
Text::script('JFIELD_MEDIA_DOWNLOAD_CHECK_DESC_LABEL');
Text::script('JFIELD_MEDIA_DOWNLOAD_CHECK_LABEL');
Text::script('JFIELD_MEDIA_EMBED_CHECK_LABEL');
Text::script('JFIELD_MEDIA_WIDTH_LABEL');
Text::script('JFIELD_MEDIA_TITLE_LABEL');
Text::script('JFIELD_MEDIA_HEIGHT_LABEL');
Text::script('JFIELD_MEDIA_UNSUPPORTED');
Text::script('JFIELD_MEDIA_DOWNLOAD_FILE');
Text::script('JLIB_APPLICATION_ERROR_SERVER');
Text::script('JLIB_FORM_MEDIA_PREVIEW_EMPTY', true);

$modalHTML = HTMLHelper::_(
	'bootstrap.renderModal',
	'imageModal_' . $id,
	[
		'url'         => $url,
		'title'       => Text::_('JLIB_FORM_CHANGE_IMAGE'),
		'closeButton' => true,
		'height'      => '100%',
		'width'       => '100%',
		'modalWidth'  => '80',
		'bodyHeight'  => '60',
		'footer'      => '<button type="button" class="btn btn-success button-save-selected">' . Text::_('JSELECT') . '</button>'
			. '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JCANCEL') . '</button>',
	]
);

$wam->useStyle('webcomponent.field-media')
	->useScript('webcomponent.field-media');

if (count($doc->getScriptOptions('media-picker')) === 0) {
	$doc->addScriptOptions('media-picker', [
		'images'    => $imagesExt,
		'audios'    => $audiosExt,
		'videos'    => $videosExt,
		'documents' => $documentsExt,
	]);
}

?>
<joomla-field-media class="field-media-wrapper" type="image" 
	<?php // @TODO add this attribute to the field in order to use it for all media types?> 
	base-path="<?php echo Uri::root(); ?>" root-folder="<?php echo ComponentHelper::getParams('com_media')->get('file_path', 'images'); ?>" url="<?php echo $url; ?>" modal-container=".modal" modal-width="100%" modal-height="400px" input=".field-media-input" button-select=".button-select" button-clear=".button-clear" button-save-selected=".button-save-selected" preview="static" preview-container=".field-media-preview" preview-width="<?php echo $previewWidth; ?>" preview-height="<?php echo $previewHeight; ?>" supported-extensions="<?php echo str_replace('"', '&quot;', json_encode(['images' => $imagesAllowedExt, 'audios' => $audiosAllowedExt, 'videos' => $videosAllowedExt, 'documents' => $documentsAllowedExt])); ?>">
	<?php echo $modalHTML; ?>
	<?php if ($showPreview) : ?>
		<div class="field-media-preview">
			<?php echo ' ' . $previewImgEmpty; ?>
			<?php echo ' ' . $previewImg; ?>
		</div>
	<?php endif; ?>
	<div class="input-group">
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" readonly="readonly" <?php echo $attr; ?>>
		<?php if ($disabled != true) : ?>
			<button type="button" class="btn btn-success button-select"><?php echo Text::_('JLIB_FORM_BUTTON_SELECT'); ?></button>
			<button type="button" class="btn btn-danger button-clear"><span class="icon-times" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('JLIB_FORM_BUTTON_CLEAR'); ?></span></button>
		<?php endif; ?>
	</div>
</joomla-field-media>

<?php endif; ?>