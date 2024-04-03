<?php
/**
T4 Overide
 */


defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

$usersConfig = ComponentHelper::getParams('com_users');

?>
<div class="login-wrap">
	<div class="frm-wrap login">
		<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<?php
			$loginDesc = $this->params->get('login_description') != null
			? $this->params->get('login_description') : ''; ?>
		
		<?php
			if (($this->params->get('logindescription_show') == 1
				&& str_replace(' ', '', $loginDesc) != '')
				|| $this->params->get('login_image') != '') : ?>

		<div class="login-description">
		<?php endif; ?>

			<?php if ($this->params->get('logindescription_show') == 1) : ?>
				<?php echo $this->params->get('login_description'); ?>
			<?php endif; ?>

			<?php if ($this->params->get('login_image') != '') : ?>
				<?php $alt = empty($this->params->get('login_image_alt')) && empty($this->params->get('login_image_alt_empty'))
				? ''
				: 'alt="' . htmlspecialchars($this->params->get('login_image_alt'), ENT_COMPAT, 'UTF-8') . '"'; ?>
				<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="com-users-login__image login-image" <?php echo $alt; ?>>
			<?php endif; ?>

		<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $loginDesc) != '') || $this->params->get('login_image') != '') : ?>
		</div>
		<?php endif; ?>

		<form id="com-users-login__form" action="<?php echo Route::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="com-users-login__form frm-login-form form-validate">

			<fieldset>
				<?php echo $this->form->renderFieldset('credentials', ['class' => 'login__input']); ?>
				<?php if ($this->tfa) : ?>
					<?php echo $this->form->renderField('secretkey', null, null, ['class' => 'login__secretkey']); ?>
				<?php endif; ?>

				<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
					<div  class="login-remember">
						<div class="form-check">
							<input id="remember" type="checkbox" name="remember" class="form-check-input inputbox" value="yes">	
							<label class="form-check-label" for="remember">
								<?php echo Text::_('COM_USERS_LOGIN_REMEMBER_ME'); ?>
							</label>
						</div>
					</div>
				<?php endif; ?>

				<!-- Extra buttons exist on Joomla 4 -->
				<?php if(property_exists($this,'extraButtons')):?>
					<?php foreach ($this->extraButtons as $button):
					$dataAttributeKeys = array_filter(array_keys($button), function ($key) {
						return substr($key, 0, 5) == 'data-';
					});
					?>
						<div class="login-submit control-group">
							<div class="controls">
								<button type="button"
										class="btn btn-secondary w-100 <?php echo $button['class'] ?? '' ?>"
										<?php foreach ($dataAttributeKeys as $key): ?>
										<?php echo $key ?>="<?php echo $button[$key] ?>"
										<?php endforeach; ?>
										<?php if ($button['onclick']): ?>
										onclick="<?php echo $button['onclick'] ?>"
										<?php endif; ?>
										title="<?php echo Text::_($button['label']) ?>"
										id="<?php echo $button['id'] ?>"
								>
									<?php if (!empty($button['icon'])): ?>
										<span class="<?php echo $button['icon'] ?>"></span>
									<?php elseif (!empty($button['image'])): ?>
										<?php echo HTMLHelper::_('image', $button['image'], Text::_($button['tooltip'] ?? ''), [
											'class' => 'icon',
										], true) ?>
									<?php elseif (!empty($button['svg'])): ?>
										<?php echo $button['svg']; ?>
									<?php endif; ?>
									<?php echo Text::_($button['label']) ?>
								</button>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif;?>
				<!-- // Extra buttons exist on Joomla 4 -->

				<div class="login-submit control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">
							<?php echo Text::_('JLOGIN'); ?>
						</button>
					</div>
				</div>

				<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
				<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

	<div class="other-links">
		<ul>
			<li><a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
				<?php echo Text::_('COM_USERS_LOGIN_RESET'); ?>
			</a></li>
			<li><a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
				<?php echo Text::_('COM_USERS_LOGIN_REMIND'); ?>
			</a></li>
			<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li><a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo Text::_('COM_USERS_LOGIN_REGISTER'); ?>
				</a></li>
			<?php endif; ?>
			</ul>
	</div>
</div>