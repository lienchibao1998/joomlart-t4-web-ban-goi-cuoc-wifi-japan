<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Site\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;

// Make alias of original FileLayout
\T4\Helper\Joomla::makeAlias(JPATH_SITE . '/components/com_content/src/Service/Router.php', 'Router', '_Router');
/**
 * Routing class of com_content
 *
 * @since  3.3
 */
class Router extends _Router
{
	protected $db;
	/**
	 * Content Component router constructor
	 *
	 * @param   SiteApplication           $app              The application object
	 * @param   AbstractMenu              $menu             The menu object to work with
	 * @param   CategoryFactoryInterface  $categoryFactory  The category object
	 * @param   DatabaseInterface         $db               The database object
	 */
	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$this->db = $db;
		$author = new RouterViewConfiguration('author');
		$author->setKey('id')->setNestable()->addLayout('list');
		$this->registerView($author);
		
		parent::__construct($app, $menu, $categoryFactory,  $db);
	}

	/**
	 * Method to get the segment(s) for an article
	 *
	 * @param   string  $id     ID of the user to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getAuthorSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			$dbquery = $this->db->getQuery(true);
			$dbquery->select($dbquery->quoteName('name'))
				->from($dbquery->quoteName('#__users'))
				->where('id = ' . $dbquery->quote($id));
			$this->db->setQuery($dbquery);

			$id .= ':' . str_replace("-"," ",$this->db->loadResult() ?? '');
		}

		if ($this->noIDs)
		{
			list($void, $segment) = explode(':', $id, 2);

			return array($void => $segment);
		}

		return array((int) $id => $id);
	}
	/**
	 * Method to get the segment(s) for an article
	 *
	 * @param   string  $segment  Segment of the user to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getAuthorId($segment, $query)
	{
		if ($this->noIDs)
		{
			$dbquery = $this->db->getQuery(true);
			$dbquery->select($dbquery->quoteName('id'))
				->from($dbquery->quoteName('#__users'))
				->where('name = ' . $dbquery->quote(str_replace("-"," ",$segment)));
			$this->db->setQuery($dbquery);

			return (int) $this->db->loadResult();
		}

		return (int) $segment;
	}

}
