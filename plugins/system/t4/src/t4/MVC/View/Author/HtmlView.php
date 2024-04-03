<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Site\View\Author;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
/**
 * HTML View class for the Content component
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{

	/**
	 * Layout name
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $_layout = 'list';
	public function __construct($config = array())
	{
		parent::__construct($config);
		// Set the layout
		if (\array_key_exists('layout', $config))
		{
			$this->setLayout($config['layout']);
		}
		else
		{
			$this->setLayout('list');
		}

		$this->baseurl = Uri::base(true);
	}
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{	
		$input = Factory::getApplication()->input;
		$vName = $input->getCmd('view');
		$vLayout = $input->getCmd('layout');
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');
		// load language t4
		Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
		
		if($vLayout == 'author'){
			$this->setLayout('author');
			$this->article_items = $this->get('Items');
			$this->pagination = $this->get('Pagination');
			$this->author = $this->get('Author');
			$this->columns = $this->params->get('num_article_col',3);
			
			PluginHelper::importPlugin('content');
			$offset = $this->state->get('list.offset');
			// Compute the article slugs and prepare introtext (runs content plugins).
			foreach ($this->article_items as $item)
			{
				$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

				$item->parent_slug = $item->parent_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

				// No link for ROOT category
				if ($item->parent_alias === 'root')
				{
					$item->parent_slug = null;
				}

				$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
				$item->event   = new \stdClass;

				// Old plugins: Ensure that text property is available
				if (!isset($item->text))
				{
					$item->text = $item->introtext;
				}
				Factory::getApplication()->triggerEvent('onContentPrepare', array('com_content.author', &$item, &$item->params, $offset));
				$item->event = new \stdClass;
				$results = Factory::getApplication()->triggerEvent('onContentAfterTitle', array('com_content.author', &$item, &$item->params, $offset));
				$item->event->afterDisplayTitle = trim(implode("\n", $results));

				$results = Factory::getApplication()->triggerEvent('onContentBeforeDisplay', array('com_content.author', &$item, &$item->params, $offset));
				$item->event->beforeDisplayContent = trim(implode("\n", $results));

				$results = Factory::getApplication()->triggerEvent('onContentAfterDisplay', array('com_content.author', &$item, &$item->params, $offset));
				$item->event->afterDisplayContent = trim(implode("\n", $results));
				
			}
		}else{
			$this->setLayout('list');
			$this->authors = $this->get('Authors');
			$this->AuhtorPagination = $this->get('authorPagination');
			$this->columns = $this->params->get('num_author_col',3);
		}
		parent::display($tpl);
		
		return $this;
	}
}