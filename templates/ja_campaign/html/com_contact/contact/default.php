<?php

/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Contact\Site\Helper\RouteHelper;

if (!class_exists('ContentHelperRoute')) {
	if (version_compare(JVERSION, '4', 'ge')) {
		abstract class ContentHelperRoute extends \Joomla\Component\Content\Site\Helper\RouteHelper
		{
		};
	} else {
		JLoader::register('ContentHelperRoute', $com_path . '/helpers/route.php');
	}
}

$tparams = $this->item->params;
?>

<div class="com-contact contact <?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Person">
	<?php if ($tparams->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($tparams->get('page_heading')); ?>
		</h1>
	<?php endif; ?>

	<?php if ($this->item->name && $tparams->get('show_name')) : ?>
		<div class="page-header">
			<h2>
				<?php if ($this->item->published == 0) : ?>
					<span class="badge badge-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<span class="contact-name" itemprop="name"><?php echo $this->item->name; ?></span>
			</h2>
		</div>
	<?php endif; ?>

	<div class="request-offer">
		<div class="row">
			<div class="col-12 col-md-5">
				<div class="alert alert-info">
					<?php echo $this->item->misc; ?>
				</div>

				<?php if ($this->params->get('show_info', 1)) : ?>
					<?php if ($this->item->image && $tparams->get('show_image')) : ?>
					<div class="contact-image mb-4">
						<?php echo HTMLHelper::_('image', $this->item->image, htmlspecialchars($this->item->name,  ENT_QUOTES, 'UTF-8'), array('itemprop' => 'image')); ?>
					</div>
					<?php endif; ?>

					<?php if ($this->item->con_position && $tparams->get('show_position')) : ?>
						<div class="contact-position">
							<dl class="contact-position dl-horizontal">
								<dt><?php echo Text::_('COM_CONTACT_POSITION'); ?>:</dt>
								<dd itemprop="jobTitle">
									<?php echo $this->item->con_position; ?>
								</dd>
							</dl>
						</div>
					<?php endif; ?>

					<?php echo $this->loadTemplate('address'); ?>

					<?php if ($tparams->get('allow_vcard')) : ?>
						<?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
						<a href="<?php echo Route::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->item->id . '&amp;format=vcf'); ?>">
						<?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
					<?php endif; ?>
					
				<?php endif; ?>
				<!-- // Show info -->

				<!-- Show links -->
				<?php if ($tparams->get('show_links')) : ?>
					<div class="contact-link">
						<?php echo $this->loadTemplate('links'); ?>
					</div>
				<?php endif; ?>
				<!-- // Show links -->

				<!-- Show articles -->
				<?php if ($tparams->get('show_articles') && $this->item->user_id && $this->item->articles) : ?>
					<div class="contact-articles">
						<?php echo '<h3>' . Text::_('JGLOBAL_ARTICLES') . '</h3>'; ?>
						<?php echo $this->loadTemplate('articles'); ?>
					</div>
				<?php endif; ?>
				<!-- // Show articles -->

				<!-- Show profile -->
				<?php if ($tparams->get('show_profile') && $this->item->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
					<div class="contact-profile">
						<?php echo '<h3>' . Text::_('COM_CONTACT_PROFILE') . '</h3>'; ?>
						<?php echo $this->loadTemplate('profile'); ?>
					</div>
				<?php endif; ?>
				<!-- // Show profile -->

				<!-- Custom field -->
				<?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
					<div class="contact-custom-field">
						<?php echo $this->loadTemplate('user_custom_fields'); ?>
					</div>
				<?php endif; ?>
				<!-- // Custom field -->
			</div>

			<div class="col-12 col-md-7">
				<div class="request-form">
					<?php echo $this->loadTemplate('form'); ?>
				</div>
			</div>
		</div>

	</div>

	<?php echo $this->item->event->afterDisplayContent; ?>
</div>