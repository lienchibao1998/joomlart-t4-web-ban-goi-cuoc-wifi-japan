<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_jt_login
 *
 * @copyright   (C) 2005 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Login\Site\Helper\LoginHelper;

$params->def('greeting', 1);

// HTML IDs
$formId           = 'login-form-' . $module->id;
$type             = LoginHelper::getType();
$return           = LoginHelper::getReturnUrl($params, $type);
$registerLink     = LoginHelper::getRegistrationUrl($params);
$twofactormethods = AuthenticationHelper::getTwoFactorMethods();
$extraButtons     = AuthenticationHelper::getLoginButtons($formId);
$user             = Factory::getUser();
$layout           = $params->get('layout', 'default');
///////

$document = JFactory::getDocument(); 
$modulePath = JURI::base(true).'/modules/mod_jt_login/';
$document->addStyleSheet($modulePath.'src/css/style.css');
// Logged users must load the logout sublayout
if (!$user->guest)
{
	$layout .= '_logout';
}

require ModuleHelper::getLayoutPath('mod_jt_login', $layout);
?>