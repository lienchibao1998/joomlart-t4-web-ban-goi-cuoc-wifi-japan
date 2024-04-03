<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.ja_campain
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<title><?php echo Text::_('TPL_T4_ENABLED_T4_ERROR_TITLE') ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/jpages.css" type="text/css" />
</head>

<body class="t4-error-page">
  <div class="t4-error-msg">
    <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/info-circle-light.svg" alt="Info icon" />
  	<h1><?php echo Text::_('TPL_T4_ENABLED_T4_ERROR_TITLE') ?></h1>
  	<p class="error-message"><?php echo Text::_('TPL_T4_ENABLED_T4_ERROR_DESC') ?></p>

    <div class="cta-wrap">
      <h3>Resources</h3>
      <a href="#" title="<?php echo Text::_('TPL_T4_DOWNLOAD') ?>"><?php echo Text::_('TPL_T4_DOWNLOAD') ?></a>
      <a href="#" title="<?php echo Text::_('TPL_T4_DOCUMENTATION') ?>"><?php echo Text::_('TPL_T4_DOCUMENTATION') ?></a>
      <a href="#" title="<?php echo Text::_('TPL_T4_SUPPORT') ?>"><?php echo Text::_('TPL_T4_SUPPORT') ?></a>
    </div>
  </div>
</body>
</html>
