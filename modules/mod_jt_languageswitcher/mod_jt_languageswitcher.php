<?php
/*------------------------------------------------------------------------
# mod_jt_languageswitcher Module
# ------------------------------------------------------------------------
# author    joomlatema
# copyright Copyright (C) 2022 joomlatema.net. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlatema.net
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Languages\Site\Helper\LanguagesHelper;

$headerText = $params->get('HeaderText');
$footerText = $params->get('FooterText');
$list       = LanguagesHelper::getList($params);

require ModuleHelper::getLayoutPath('mod_jt_languageswitcher', $params->get('layout', 'default'));
