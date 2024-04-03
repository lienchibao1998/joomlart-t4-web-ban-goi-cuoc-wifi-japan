<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jt_login
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
HTMLHelper::_('jquery.framework');
JHtmlBootstrap::renderModal();

$app->getDocument()->getWebAssetManager()
	->useScript('core')
	->useScript('keepalive')
	->useScript('field.passwordview');

Text::script('JSHOWPASSWORD');
Text::script('JHIDEPASSWORD');
$FormType=$params->get('FormType');

$FormClass='';
$FormStyle=$params->get('FormStyle');
		if($FormStyle==2)	{
		$FormClass="style2";}
		if($FormStyle==3)	{
		$FormClass="style3";}
?>
<style type="text/css">
div.jtl-content-login<?php echo $module->id; ?>,div.jtl-content-register<?php echo $module->id; ?> {display:none;position:absolute;top: 100%;margin-top:5px;padding:20px;background:<?php echo $params->get('LoginBg');?>; z-index:99;-webkit-box-shadow: 0 0 7px rgba(0, 0, 0, 0.2);box-shadow: 0 0 7px rgba(0, 0, 0, 0.2); overflow:hidden;}
#jtl button.login-toggle<?php echo $module->id; ?>,#jtl button.register-toggle<?php echo $module->id; ?>{ padding:6px 15px; margin:0px 10px;box-shadow:none;border: 0;border-radius: 3px;transition: all 0.3s linear 0s;}
#loginModal<?php echo $module->id; ?>.modal.fade.show,#registerModal<?php echo $module->id; ?>.modal.fade.show{top:<?php echo $params->get('ModalTopPosition');?>;}
#loginModal<?php echo $module->id; ?> .modal-dialog ,#registerModal<?php echo $module->id; ?> .modal-dialog{max-width:<?php echo $params->get('ModalMaxWidth');?>;;}
#loginModal<?php echo $module->id; ?> .modal-dialog .modal-content,#registerModal<?php echo $module->id; ?> .modal-dialog .modal-content{background:<?php echo $params->get('LoginBg');?>; }
</style>
<div id="jtl" class="<?php echo $FormClass;?>">
<?php if($FormType == 'dropdown') { ?>
<button class="login-toggle<?php echo $module->id; ?> login" href="JavaScript:void(0);"><?php echo $params->get('LoginButText', 'Login');?></button>
<div class="jtl-content-login<?php echo $module->id; ?>"><?php if ($params->get('LoginTitle')!=null):?><<?php echo $params->get('LoginTitleClass');?> class="login-title"><?php echo $params->get('LoginTitle');?></<?php echo $params->get('LoginTitleClass');?>><?php endif; ?>
<form id="login-form-<?php echo $module->id; ?>" class="mod-login" action="<?php echo Route::_('index.php', true); ?>" method="post">
	<?php if ($params->get('pretext')) : ?>
		<div class="mod-login__pretext pretext">
			<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<div class="mod-login__userdata userdata">
		<div class="mod-login__username form-group">
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="tài khoản" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?>">
					<label for="modlgn-username-<?php echo $module->id; ?>" class="visually-hidden"><?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?></label>
					<span class="input-group-text" title="<?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?>">
						<span class="icon-user-icon" aria-hidden="true"></span>
					</span>
				</div>
			<?php else : ?>
				<label for="modlgn-username-<?php echo $module->id; ?>"><?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?></label>
				<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="tài khoản" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?>">
			<?php endif; ?>
		</div>

		<div class="mod-login__password form-group">
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
					<label for="modlgn-passwd-<?php echo $module->id; ?>" class="visually-hidden"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
					<button type="button" class="btn btn-secondary input-password-toggle">
						<span class="icon-eye-icon" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('JSHOWPASSWORD'); ?></span>
					</button>
				</div>
			<?php else : ?>
				<label for="modlgn-passwd-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
			<?php endif; ?>
		</div>
		<?php if (count($twofactormethods) > 1) : ?>
			<div class="mod-login__twofactor form-group">
				<?php if (!$params->get('usetext', 0)) : ?>
					<div class="input-group">
						<span class="input-group-text">
							<span class="icon-star" aria-hidden="true"></span>
						</span>
						<label for="modlgn-secretkey-<?php echo $module->id; ?>" class="visually-hidden"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="one-time-code" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-text">
							<span class="icon-question icon-fw" aria-hidden="true"></span>
						</span>
					</div>
				<?php else : ?>
					<label for="modlgn-secretkey-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
					<div class="input-group">
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="one-time-code" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-text">
							<span class="icon-question icon-fw" aria-hidden="true"></span>
						</span>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="mod-login__remember form-group">
				<div id="form-login-remember-<?php echo $module->id; ?>" class="form-check">
					<label class="form-check-label">
						<input type="checkbox" name="remember" class="form-check-input" value="yes">
						<?php echo Text::_('MOD_JT_LOGIN_REMEMBER_ME'); ?>
					</label>
				</div>
			</div>
		<?php endif; ?>
		<?php foreach($extraButtons as $button):
			$dataAttributeKeys = array_filter(array_keys($button), function ($key) {
				return substr($key, 0, 5) == 'data-';
			});
			?>
			<div class="mod-login__submit form-group">
				<button type="button"
						class="btn btn-secondary w-100 mt-4 <?php echo $button['class'] ?? '' ?>"
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
						<?php echo $button['image']; ?>
					<?php elseif (!empty($button['svg'])): ?>
						<?php echo $button['svg']; ?>
					<?php endif; ?>
					<?php echo Text::_($button['label']) ?>
				</button>
			</div>
		<?php endforeach; ?>

		<div class="mod-login__submit form-group">
			<button type="submit" name="Submit" class="btn btn-primary btn-block"><?php echo Text::_('JLOGIN'); ?></button>
		</div>
<?php if($params->get('ShowForgotPassw') == 1)  : ?>
		<?php
			$usersConfig = ComponentHelper::getParams('com_users'); ?>
			<ul class="mod-login__options list-unstyled">
				<li>		<div class="username-password form-group">			<a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo Text::_('MOD_JT_LOGIN_FORGOT_YOUR_USERNAME'); ?></a> <?php echo Text::_('MOD_JT_LOGIN_OR_LABEL'); ?>
					<a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo Text::_('MOD_JT_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a></div>
				</li>
				<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li><div class="registerlink">
					<a href="<?php echo Route::_($registerLink); ?>">
					<?php echo Text::_('MOD_JT_LOGIN_REGISTER'); ?> <span class="icon-arrow-icon" aria-hidden="true"></span></a>
					</div>
				</li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		<input type="hidden" name="option" value="com_users">
		<input type="hidden" name="task" value="user.login">
		<input type="hidden" name="return" value="<?php echo $return; ?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<?php if ($params->get('posttext')) : ?>
		<div class="mod-login__posttext posttext">
			<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
</div>
<?php if($params->get('ShowRegister') == 1)  : ?>
<button class="register-toggle<?php echo $module->id; ?> register" href="JavaScript:void(0);"><?php echo $params->get('RegisterButText', 'Register');?></button><?php endif; ?>
<div class="jtl-content-register<?php echo $module->id; ?>">
<?php if ($params->get('RegisterTitle')!=null):?><<?php echo $params->get('RegisterTitleClass');?> class="register-title"><?php echo $params->get('RegisterTitle');?></<?php echo $params->get('RegisterTitleClass');?>><?php endif; ?><form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="com-users-registration__form form-validate" enctype="multipart/form-data">
					<div class="jtl-note"><span><?php echo JText::_("MOD_JT_LOGIN_REQUIRED_FIELD"); ?></span></div>
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_NAME' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-name" type="text" name="jform[name]" />
						</div>
					</div>			
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_USERNAME' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-username1" type="text" name="jform[username]"  />
						</div>
					</div>
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_PASSWORD' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-password1" type="password" name="jform[password1]"  />
						</div>
					</div>		
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_VERIFY_PASSWORD' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-password2" type="password" name="jform[password2]"  />
						</div>
					</div>
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_EMAIL' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-email1" type="text" name="jform[email1]" />
						</div>
					</div>
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_VERIFY_EMAIL' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-email2" type="text" name="jform[email2]" />
						</div>
					</div>
					<div class="jtl-buttonsubmit">											 
					<button type="submit" class="com-users-registration__register btn btn-primary btn-block validate">
					<?php echo Text::_('JREGISTER'); ?>
				</button>
				<input type="hidden" name="option" value="com_users">
				<input type="hidden" name="task" value="registration.register">
					<?php echo HTMLHelper::_('form.token'); ?>
					</div>
			</form></div>


<?php }else{ ?><!--modal-->
<button target="_blank"	class="modal-toggle" data-bs-toggle="modal" data-bs-target="#loginModal<?php echo $module->id; ?>"><?php echo $params->get('LoginButText', 'Login');?></button>
<div class="modal fade" id="loginModal<?php echo $module->id; ?>" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog jt-cs">
    <div class="modal-content"><?php if ($params->get('LoginTitle')!=null):?><<?php echo $params->get('LoginTitleClass');?> class="login-title"><?php echo $params->get('LoginTitle');?></<?php echo $params->get('LoginTitleClass');?>><?php endif; ?><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">
<path fill="#231F20" d="M9.016,40.837c0.195,0.195,0.451,0.292,0.707,0.292c0.256,0,0.512-0.098,0.708-0.293l14.292-14.309
	l14.292,14.309c0.195,0.196,0.451,0.293,0.708,0.293c0.256,0,0.512-0.098,0.707-0.292c0.391-0.39,0.391-1.023,0.001-1.414
	L26.153,25.129L40.43,10.836c0.39-0.391,0.39-1.024-0.001-1.414c-0.392-0.391-1.024-0.391-1.414,0.001L24.722,23.732L10.43,9.423
	c-0.391-0.391-1.024-0.391-1.414-0.001c-0.391,0.39-0.391,1.023-0.001,1.414l14.276,14.293L9.015,39.423
	C8.625,39.813,8.625,40.447,9.016,40.837z"/>
</svg>
</button><form id="login-form-<?php echo $module->id; ?>" class="mod-login" action="<?php echo Route::_('index.php', true); ?>" method="post">
	<?php if ($params->get('pretext')) : ?>
		<div class="mod-login__pretext pretext">
			<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<div class="mod-login__userdata userdata">
		<div class="mod-login__username form-group">
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="tài khoản" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?>">
					<label for="modlgn-username-<?php echo $module->id; ?>" class="visually-hidden"><?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?></label>
					<span class="input-group-text" title="<?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?>">
						<span class="icon-user-icon" aria-hidden="true"></span>
					</span>
				</div>
			<?php else : ?>
				<label for="modlgn-username-<?php echo $module->id; ?>"><?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?></label>
				<input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="tài khoản" class="form-control" autocomplete="username" placeholder="<?php echo Text::_('MOD_JT_LOGIN_VALUE_USERNAME'); ?>">
			<?php endif; ?>
		</div>

		<div class="mod-login__password form-group">
			<?php if (!$params->get('usetext', 0)) : ?>
				<div class="input-group">
					<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
					<label for="modlgn-passwd-<?php echo $module->id; ?>" class="visually-hidden"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
					<button type="button" class="btn btn-secondary input-password-toggle">
						<span class="icon-eye-icon" aria-hidden="true"></span>
						<span class="visually-hidden"><?php echo Text::_('JSHOWPASSWORD'); ?></span>
					</button>
				</div>
			<?php else : ?>
				<label for="modlgn-passwd-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
				<input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
			<?php endif; ?>
		</div>
		<?php if (count($twofactormethods) > 1) : ?>
			<div class="mod-login__twofactor form-group">
				<?php if (!$params->get('usetext', 0)) : ?>
					<div class="input-group">
						<span class="input-group-text">
							<span class="icon-star" aria-hidden="true"></span>
						</span>
						<label for="modlgn-secretkey-<?php echo $module->id; ?>" class="visually-hidden"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="one-time-code" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-text">
							<span class="icon-question icon-fw" aria-hidden="true"></span>
						</span>
					</div>
				<?php else : ?>
					<label for="modlgn-secretkey-<?php echo $module->id; ?>"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
					<div class="input-group">
						<input id="modlgn-secretkey-<?php echo $module->id; ?>" autocomplete="one-time-code" type="text" name="secretkey" class="form-control" placeholder="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>">
						<span class="input-group-text">
							<span class="icon-question icon-fw" aria-hidden="true"></span>
						</span>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="mod-login__remember form-group">
				<div id="form-login-remember-<?php echo $module->id; ?>" class="form-check">
					<label class="form-check-label">
						<input type="checkbox" name="remember" class="form-check-input" value="yes">
						<?php echo Text::_('MOD_JT_LOGIN_REMEMBER_ME'); ?>
					</label>
				</div>
			</div>
		<?php endif; ?>
		<?php foreach($extraButtons as $button):
			$dataAttributeKeys = array_filter(array_keys($button), function ($key) {
				return substr($key, 0, 5) == 'data-';
			});
			?>
			<div class="mod-login__submit form-group">
				<button type="button"
						class="btn btn-secondary w-100 mt-4 <?php echo $button['class'] ?? '' ?>"
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
						<?php echo $button['image']; ?>
					<?php elseif (!empty($button['svg'])): ?>
						<?php echo $button['svg']; ?>
					<?php endif; ?>
					<?php echo Text::_($button['label']) ?>
				</button>
			</div>
		<?php endforeach; ?>

		<div class="mod-login__submit form-group">
			<button type="submit" name="Submit" class="btn btn-primary btn-block"><?php echo Text::_('JLOGIN'); ?></button>
		</div>
<?php if($params->get('ShowForgotPassw') == 1)  : ?>
		<?php
			$usersConfig = ComponentHelper::getParams('com_users'); ?>
			<ul class="mod-login__options list-unstyled">
				<li><div class="username-password form-group">	<a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
					<?php echo Text::_('MOD_JT_LOGIN_FORGOT_YOUR_USERNAME'); ?></a> or
					<a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
					<?php echo Text::_('MOD_JT_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
					</div>
				</li>
			
				<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<li><div class="registerlink">
					<a href="<?php echo Route::_($registerLink); ?>">
					<?php echo Text::_('MOD_JT_LOGIN_REGISTER'); ?> <span class="icon-arrow-icon" aria-hidden="true"></span></a>
					</div>
				</li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		<input type="hidden" name="option" value="com_users">
		<input type="hidden" name="task" value="user.login">
		<input type="hidden" name="return" value="<?php echo $return; ?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<?php if ($params->get('posttext')) : ?>
		<div class="mod-login__posttext posttext">
			<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form> 
	</div></div></div>
			<?php if($params->get('ShowRegister') == 1)  : ?>
<button class="modal-toggle register" data-bs-toggle="modal" data-bs-target="#registerModal<?php echo $module->id; ?>"><?php echo $params->get('RegisterButText', 'Register');?></button><?php endif; ?>
<div class="modal fade" id="registerModal<?php echo $module->id; ?>" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog jt-cs">
  
    <div class="modal-content"><?php if ($params->get('RegisterTitle')!=null):?><<?php echo $params->get('RegisterTitleClass');?> class="register-title"><?php echo $params->get('RegisterTitle');?></<?php echo $params->get('RegisterTitleClass');?>><?php endif; ?>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
  <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">
<path fill="#231F20" d="M9.016,40.837c0.195,0.195,0.451,0.292,0.707,0.292c0.256,0,0.512-0.098,0.708-0.293l14.292-14.309
	l14.292,14.309c0.195,0.196,0.451,0.293,0.708,0.293c0.256,0,0.512-0.098,0.707-0.292c0.391-0.39,0.391-1.023,0.001-1.414
	L26.153,25.129L40.43,10.836c0.39-0.391,0.39-1.024-0.001-1.414c-0.392-0.391-1.024-0.391-1.414,0.001L24.722,23.732L10.43,9.423
	c-0.391-0.391-1.024-0.391-1.414-0.001c-0.391,0.39-0.391,1.023-0.001,1.414l14.276,14.293L9.015,39.423
	C8.625,39.813,8.625,40.447,9.016,40.837z"/>
</svg>
</button>
<div class="jtl-content-register-modal"><form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="com-users-registration__form form-validate" enctype="multipart/form-data">
					<div class="jtl-note"><span><?php echo JText::_("MOD_JT_LOGIN_REQUIRED_FIELD"); ?></span></div>
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_NAME' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-name" type="text" name="jform[name]" />
						</div>
					</div>			
					
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_USERNAME' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-username1" type="text" name="jform[username]"  />
						</div>
					</div>
					
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_PASSWORD' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-password1" type="password" name="jform[password1]"  />
						</div>
					</div>		
					
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_VERIFY_PASSWORD' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-password2" type="password" name="jform[password2]"  />
						</div>
					</div>
					
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_EMAIL' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-email1" type="text" name="jform[email1]" />
						</div>
					</div>
					
					<div class="jtl-field">
						<div class="jtl-label"><?php echo JText::_( 'MOD_JT_LOGIN_VERIFY_EMAIL' ); ?></div>
						<div class="form-group">
							<input id="jtl-input-email2" type="text" name="jform[email2]" />
						</div>
					</div>
								
					<div class="jtl-buttonsubmit">											 
					<button type="submit" class="com-users-registration__register btn btn-primary btn-block validate">
					<?php echo Text::_('JREGISTER'); ?>
				</button>
				<input type="hidden" name="option" value="com_users">
				<input type="hidden" name="task" value="registration.register">
					<?php echo HTMLHelper::_('form.token'); ?>
					</div>
			</form></div>
			</div>
			
</div></div>
<?php }?>
</div>


<script type="text/javascript">
jQuery(function() { // Dropdown toggle
jQuery('.login-toggle<?php echo $module->id; ?>').click(function() { jQuery(this).next('.jtl-content-login<?php echo $module->id; ?>').slideToggle();
});
});

jQuery(document).on("click", function(event) {
    var trigger = jQuery(".login-toggle<?php echo $module->id; ?>")[0];
    var dropdown = jQuery(".jtl-content-login<?php echo $module->id; ?>");
    if (dropdown !== event.target && !dropdown.has(event.target).length && trigger !== event.target) {
	  jQuery('.jtl-content-login<?php echo $module->id; ?>').slideUp();
    }
  });
  

jQuery(function() { // Dropdown toggle
jQuery('.register-toggle<?php echo $module->id; ?>').click(function() { jQuery(this).next('.jtl-content-register<?php echo $module->id; ?>').slideToggle();
});
});
jQuery(document).on("click", function(event) {
    var trigger = jQuery(".register-toggle<?php echo $module->id; ?>")[0];
    var dropdown = jQuery(".jtl-content-register<?php echo $module->id; ?>");
    if (dropdown !== event.target && !dropdown.has(event.target).length && trigger !== event.target) {
	  jQuery('.jtl-content-register<?php echo $module->id; ?>').slideUp();
    }
  });
</script>
