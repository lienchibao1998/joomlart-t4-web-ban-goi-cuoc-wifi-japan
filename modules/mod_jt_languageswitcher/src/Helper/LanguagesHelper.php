<?php
/*------------------------------------------------------------------------
# mod_jt_languageswitcher Module
# ------------------------------------------------------------------------
# author    joomlatema
# copyright Copyright (C) 2022 joomlatema.net. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlatema.net
-------------------------------------------------------------------------*/
namespace Joomla\Module\Languages\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;

/**
 * Helper for mod_jt_languageswitcher
 *
 * @since  1.6
 */
abstract class LanguagesHelper
{
	/**
	 * Gets a list of available languages
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module params
	 *
	 * @return  array
	 */
	public static function getList(&$params)
	{
		$user		= Factory::getUser();
		$lang		= Factory::getLanguage();
		$languages	= LanguageHelper::getLanguages();
		$app		= Factory::getApplication();
		$menu		= $app->getMenu();
		$active		= $menu->getActive();

		// Get menu home items
		$homes      = [];
		$homes['*'] = $menu->getDefault('*');

		foreach ($languages as $item)
		{
			$default = $menu->getDefault($item->lang_code);

			if ($default && $default->language === $item->lang_code)
			{
				$homes[$item->lang_code] = $default;
			}
		}

		// Load associations
		$assoc = Associations::isEnabled();

		if ($assoc)
		{
			if ($active)
			{
				$associations = MenusHelper::getAssociations($active->id);
			}

			$option = $app->input->get('option');
			$component = $app->bootComponent($option);

			if ($component instanceof AssociationServiceInterface)
			{
				$cassociations = $component->getAssociationsExtension()->getAssociationsForItem();
			}
			else
			{
				// Load component associations
				$class = str_replace('com_', '', $option) . 'HelperAssociation';
				\JLoader::register($class, JPATH_SITE . '/components/' . $option . '/helpers/association.php');

				if (class_exists($class) && \is_callable(array($class, 'getAssociations')))
				{
					$cassociations = \call_user_func(array($class, 'getAssociations'));
				}
			}
		}

		$levels    = $user->getAuthorisedViewLevels();
		$sitelangs = LanguageHelper::getInstalledLanguages(0);
		$multilang = Multilanguage::isEnabled();

		// Filter allowed languages
		foreach ($languages as $i => &$language)
		{
			// Do not display language without frontend UI
			if (!\array_key_exists($language->lang_code, $sitelangs))
			{
				unset($languages[$i]);
			}
			// Do not display language without specific home menu
			elseif (!isset($homes[$language->lang_code]))
			{
				unset($languages[$i]);
			}
			// Do not display language without authorized access level
			elseif (isset($language->access) && $language->access && !\in_array($language->access, $levels))
			{
				unset($languages[$i]);
			}
			else
			{
				$language->active = ($language->lang_code === $lang->getTag());

				// Fetch language rtl
				// If loaded language get from current JLanguage metadata
				if ($language->active)
				{
					$language->rtl = $lang->isRtl();
				}
				// If not loaded language fetch metadata directly for performance
				else
				{
					$languageMetadata = LanguageHelper::getMetadata($language->lang_code);
					$language->rtl    = $languageMetadata['rtl'];
				}

				if ($multilang)
				{
					if (isset($cassociations[$language->lang_code]))
					{
						$language->link = Route::_($cassociations[$language->lang_code]);
					}
					elseif (isset($associations[$language->lang_code]) && $menu->getItem($associations[$language->lang_code]))
					{
						$itemid = $associations[$language->lang_code];
						$language->link = Route::_('index.php?lang=' . $language->sef . '&Itemid=' . $itemid);
					}
					elseif ($active && $active->language === '*')
					{
						$language->link = Route::_('index.php?lang=' . $language->sef . '&Itemid=' . $active->id);
					}
					else
					{
						if ($language->active)
						{
							$language->link = Uri::getInstance()->toString(array('path', 'query'));
						}
						else
						{
							$itemid = isset($homes[$language->lang_code]) ? $homes[$language->lang_code]->id : $homes['*']->id;
							$language->link = Route::_('index.php?lang=' . $language->sef . '&Itemid=' . $itemid);
						}
					}
				}
				else
				{
					$language->link = Route::_('&Itemid=' . $homes['*']->id);
				}
			}
		}

		return $languages;
	}
}
