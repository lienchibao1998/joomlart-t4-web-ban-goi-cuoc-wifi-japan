<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="com-users-profile profile">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<?php if (Factory::getUser()->id == $this->data->id) : ?>
		<ul class="com-users-profile__edit btn-toolbar">
			<li class="btn-group">
				<a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
					<span class="icon-user"></span> <?php echo Text::_('COM_USERS_EDIT_PROFILE'); ?>
				</a>
			</li>
		</ul>
	<?php endif; ?>

	<?php echo $this->loadTemplate('core'); ?>
	<?php echo $this->loadTemplate('params'); ?>
	<?php echo $this->loadTemplate('custom'); ?>
</div>
