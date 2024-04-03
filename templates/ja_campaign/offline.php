<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$app = Factory::getApplication();

// Add JavaScript Frameworks
HTMLHelper::_('bootstrap.framework');

require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

$twofactormethods = UsersHelper::getTwoFactorMethods();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/jpages.css" type="text/css" />
	<jdoc:include type="head" />
	<?php if ($this->direction == 'rtl') : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/offline_rtl.css" type="text/css" />
	<?php endif; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
	<!-- META FOR IOS & HANDHELD -->
		<style type="text/stylesheet">
			@-webkit-viewport   { width: device-width; }
			@-moz-viewport      { width: device-width; }
			@-ms-viewport       { width: device-width; }
			@-o-viewport        { width: device-width; }
			@viewport           { width: device-width; }
		</style><meta name="HandheldFriendly" content="true"/>
	<meta name="apple-mobile-web-app-capable" content="YES"/>
	<!-- //META FOR IOS & HANDHELD -->
</head>
<body class="offline">
	<div class="page-wrap">

		<div id="frame" class="outline">

			<jdoc:include type="message" />

			<div class="form-wrap">

				<?php 
					if(version_compare(JVERSION, '4', 'ge')) {
						if ($app->get('offline_image')) : ?>
							<img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>" />
						<?php endif; 
					} else {
						if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
							<img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename'), ENT_COMPAT, 'UTF-8'); ?>" />
						<?php endif;
					}
				?>

				<!-- Site name -->
				<h1><?php echo htmlspecialchars($app->get('sitename')); ?></h1>

				<!-- Offline message -->
				<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
					<p class="offline-message"><?php echo $app->get('offline_message'); ?></p>
				<?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', Text::_('JOFFLINE_MESSAGE')) != '') : ?>
					<p class="offline-message"><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
				<?php endif; ?>
				<!-- // Offline message -->

				<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
					<fieldset class="input">
						<p id="form-login-username">
							<label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
							<input name="username" id="username" type="text" class="inputbox" alt="<?php echo Text::_('JGLOBAL_USERNAME'); ?>" size="18" />
						</p>
						<p id="form-login-password">
							<label for="passwd"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
							<input type="password" name="password" class="inputbox" size="18" alt="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
						</p>
						<?php if (count($twofactormethods) > 1) : ?>
							<p id="form-login-secretkey">
								<label for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
								<input type="text" name="secretkey" class="inputbox" size="18" alt="<?php echo Text::_('JGLOBAL_SECRETKEY'); ?>" id="secretkey" />
							</p>
						<?php endif; ?>
						<p id="submit-buton">
							<input type="submit" name="Submit" class="button login" value="<?php echo Text::_('JLOGIN'); ?>" />
						</p>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>" />
						<?php echo HTMLHelper::_('form.token'); ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</body>

</html>
