<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

if(version_compare(JVERSION, '4', 'ge')){
	HTMLHelper::_('bootstrap.tab');
	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->document->getWebAssetManager();
	$wa->useScript('keepalive')
		->useScript('form.validate')
		->useScript('com_content.form-edit');

}else{
	HTMLHelper::_('behavior.tabstate');
	HTMLHelper::_('behavior.keepalive');
	HTMLHelper::_('behavior.formvalidator');
}

// HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 1));
// HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
// HTMLHelper::_('formbehavior.chosen', 'select');
$this->tab_name = 'com-content-form';
$this->ignore_fieldsets = array('image-intro', 'image-full', 'jmetadata', 'item_associations');

// Create shortcut to parameters.
$params = $this->state->get('params');

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);

if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}

if($this->item->id){
	$this->return_page = '';
}
if(version_compare(JVERSION, '4', 'lt')){

	Factory::getDocument()->addScriptDeclaration("
		Joomla.submitbutton = function(task)
		{
			if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				" . $this->form->getField('articletext')->save() . "
				Joomla.submitform(task);
			}
		}
	");
}
?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form action="<?php echo Route::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
		<fieldset>
			<?php echo HTMLHelper::_('bootstrap.startTabSet', $this->tab_name, array('active' => 'editor')); ?>

			<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'editor', Text::_('COM_CONTENT_ARTICLE_CONTENT_OVERRIDE')); ?>

			<div class="custom-edit-content">
				<div class="mt-4 text-center">
					<img src="//static.joomlart.com/images/blog/2020/covid-offer/covid-campaign-submit.png" alt="Submit Guide" />
				</div>

				<div class="row">
					<div class="col-12 col-lg-6">
						<!-- Article title -->
						<?php echo $this->form->renderField('title'); ?>

						<!-- Article alias -->
						<?php if (is_null($this->item->id)) : ?>
							<div class="hide-alias" style="display: none;">
								<?php echo $this->form->renderField('alias'); ?>
							</div>
						<?php endif; ?>

						<!-- Categories -->
						<?php echo $this->form->renderField('catid'); ?>

						<!-- Tag -->
						<?php echo $this->form->renderField('tags'); ?>														
					</div>

					<div class="col-12 col-lg-6">
						<div class="custom-field">
							<?php echo LayoutHelper::render('joomla.edit.customField', $this); ?>
						</div>
					</div>
				</div>

			</div>

				<div class="control-label">
					<label id="jform_intro_text-lbl" for="jform_intro_text"><?php echo Text::_('COM_CONTENT_ARTICLE_INTRO_TEXT');?></label>
				</div>
				<!-- Intro text -->
				<?php echo $this->form->getInput('articletext'); ?>

				<?php if ($this->captchaEnabled) : ?>
					<?php echo $this->form->renderField('captcha'); ?>
				<?php endif; ?>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

			<?php if ($params->get('show_urls_images_frontend')) : ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', $this->tab_name, 'images', Text::_('COM_CONTENT_IMAGES_AND_URLS')); ?>
				<?php echo $this->form->renderField('image_intro', 'images'); ?>
				<?php echo $this->form->renderField('image_intro_alt', 'images'); ?>
				<?php echo $this->form->renderField('image_intro_alt_empty', 'images'); ?>
				<?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
				<?php echo $this->form->renderField('float_intro', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext_alt', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext_alt_empty', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
				<?php echo $this->form->renderField('float_fulltext', 'images'); ?>
				<?php echo $this->form->renderField('urla', 'urls'); ?>
				<?php echo $this->form->renderField('urlatext', 'urls'); ?>				
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getInput('targeta', 'urls'); ?>
					</div>
				</div>
				<?php echo $this->form->renderField('urlb', 'urls'); ?>
				<?php echo $this->form->renderField('urlbtext', 'urls'); ?>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getInput('targetb', 'urls'); ?>
					</div>
				</div>
				<?php echo $this->form->renderField('urlc', 'urls'); ?>
				<?php echo $this->form->renderField('urlctext', 'urls'); ?>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getInput('targetc', 'urls'); ?>
					</div>
				</div>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endif; ?>


			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</fieldset>
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
					<span class="icon-ok"></span><?php echo Text::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
					<span class="icon-cancel"></span><?php echo Text::_('JCANCEL') ?>
				</button>
			</div>
			<?php if ($params->get('save_history', 0) && $this->item->id) : ?>
			<div class="btn-group">
				<?php echo $this->form->getInput('contenthistory'); ?>
			</div>
			<?php endif; ?>
		</div>
	</form>
</div>
