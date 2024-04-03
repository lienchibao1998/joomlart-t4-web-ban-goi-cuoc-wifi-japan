<?php
namespace T4\MVC\Router;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class T4
{

	/**
	 * Caching of processed URIs
	 *
	 * @var    array
	 * @since  3.3
	 */
	protected $cache = array();
	protected $sefs = array();

	/**
	 * Available languages by language codes.
	 *
	 * @var    array
	 * @since  2.5
	 */
	protected $lang_codes;

	/**
	 * The current language code.
	 *
	 * @var    string
	 * @since  3.4.2
	 */
	protected $current_lang;

	/**
	 * The default language code.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $default_lang;

	public $config;
	public $app;
	public $menu;
	public $db;
	/**
	 * The routing mode.
	 *
	 * @var    boolean
	 * @since  2.5
	 */
	protected $mode_sef;

	/**
	* Content Component router constructor
	*
	* @param   JApplicationCms  $app   The application object
	* @param   JMenu            $menu  The menu object to work with
	*/
	/**
	 * Users Component router constructor
	 *
	 * @param   SiteApplication  $app   The application object
	 * @param   AbstractMenu     $menu  The menu object to work with
	 */
	public function __construct(SiteApplication $app = null, AbstractMenu $menu = null)
	{
		$this->app  = $app ?: CMSApplication::getInstance('site');
		$conf = new \JConfig();
		$this->config = new Registry($conf);
		$this->menu = $menu ?: $this->app->getMenu();
		$this->db 	= Factory::getDbo();
		$this->mode_sef     = $this->app->get('sef', 0);
		$this->lang_codes = LanguageHelper::getLanguages('lang_code');
		$this->default_lang = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');	

	}

	/**
	 * Add parse rule to router.
	 *
	 * @param   Router  &$router  Router object.
	 * @param   Uri     &$uri     Uri object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function parseRule(&$router, &$uri)
	{

		$vars = array();
		$cacheId = $uri->getPath();
		if (!empty($this->cache)) {
				$cachedQueryAndItemId = $this->cache->get('parse: '.$cacheId);

				if (!empty($cachedQueryAndItemId)) {
						$uri->setPath('');
						$uri->setQuery($cachedQueryAndItemId[0]);
						if ($cachedQueryAndItemId[1]) {
								Factory::getApplication()->input->set('Itemid', $cachedQueryAndItemId[1]);
						} else {
								Factory::getApplication()->input->set('Itemid', null);
						}
						return $vars;
				}
		}
		// Are we in SEF mode or not?
		if ($this->mode_sef)
		{
			$path = $uri->getPath();
			$parts = explode('/', $path);
			$sef = StringHelper::strtolower($parts[0]);
			$uid = null;
			foreach ($parts as $part) {
				$menuAlias = $this->getMenuByAlias($part);
				if(!empty($menuAlias)) break;
			}
			// Did we find the current and existing language yet?
			$lang_code = $this->getLanguageCookie();
			if ($lang_code) {
					$vars['lang'] = $lang_code;
			}

			// matchingroute  authors/username
			// authors/admin
			if(preg_match("/\/author\/(.*?)(\/|\?|\&|$)/", $uri->toString(array('path', 'query', 'fragment')),$matches)){
				if(!empty($matches[1])){
					$uid = intval($this->getUserId($matches[1]));
				}else {
					return $vars;
				}
				$vars['option'] = 'com_content';
				$vars['view'] = 'author';
				$vars['layout'] = 'author';
				$vars['id'] = $uid;
				$vars['u_id'] = $uid;
				$uri->setVar('option','com_content');
				$uri->setVar('layout','author');
				$uri->setVar('view','author');
				$uri->setVar('u_id',$uid);
				$uri->setVar('id',$uid);
				$itemid = !empty($uri->getQuery(true)['Itemid']) ? $uri->getQuery(true)['Itemid'] : "";
				$menuAuthors = $this->getMenuAuthorsId();

				if(!empty($menuAuthors)){
					$itemid = $menuAuthors->id;
				}
				$vars['Itemid'] = $itemid;

				$newQuery = "option=com_content&view=author&layout=author&id=".$uid."&Itemid=".$itemid;
				$oldQuery = $uri->getQuery(false);
				if (!empty($oldQuery)) {
					$newQuery = $newQuery.'&'.$oldQuery;
				}

				//Remove Itemid from the query
				$newQuery = preg_replace('#Itemid=[^&]*&#', '', $newQuery);
				$newQuery = preg_replace('#&?Itemid=.*#', '', $newQuery);
				$uri->setVar('Itemid',$itemid);
				if ($itemid) {
						$vars['Itemid'] = $itemid;
					$this->menu->setActive($itemid);
				}else{
					$vars['Itemid'] = null;
				}
				$uri->setPath('');
			}elseif (!empty($menuAlias) && preg_match("/index.php\?option=com_content&view=author&layout=list(.*)/", $menuAlias->link,$matches2)) {
				if(!empty( $matches2) &&  $matches2[1] != ''){
					$listTemp = explode("=", $matches2[1]);
					$list = array();
					foreach ($listTemp as $k) {
						$list[] = $k;
					}
					// $uri->setVar('gid',$list);
				}
			}
		}

		if (!empty($this->cache)) {
			$this->cache->store(array($uri->getQuery(false), $itemid), 'parse: '.$cacheId);
		}
	}
	public function preprocessBuildRule(&$router, &$uri)
	{
		if($uri->getVar('view',"") == 'author' && $uri->getVar('option','') == 'com_content'){
			if($uri->getVar('layout',"") == 'list' ) $uri->delVar('gid');
			$menu = $this->getMenuAuthorsId();

			if ($menu) {
				$uri->setVar('Itemid', $menu->id);
			}
		}
	}
	/**
	 * Add build rule to router.
	 *
	 * @param   Router  &$router  Router object.
	 * @param   Uri     &$uri     Uri object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function buildRule(&$router, &$uri)
	{
		$cacheId = md5(serialize($uri));
		if (!empty($this->cache)) {
			$cachedPath = $this->cache->get('build:'.$cacheId);
			if (!empty($cachedPath)) {
				$uri->setPath($cachedPath[0]);
				return;
			}
		}
		$query = $uri->getQuery(true);
		if(!empty($query['layout']) && $query['layout'] == 'author' && !empty($query['u_id'])){
			$query['id'] = $query['u_id'];
		}
		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $this->menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $this->menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}
		if (isset($query['view']))
		{
			$view = $query['view'];
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return;
		}
		if($view == 'author' && $query['option'] == "com_content"){
			

			// if (!$menuItemGiven)
			// {
			// 	$segments[] = $view;
			// }
			$itemid = $menuItem->id;
			unset($query['view']);
			if (isset($query['layout']))
			{
				$layout = $query['layout'];
				$segments[] = $layout;
				$this->sefs['layout'] = $layout;
				unset($query['layout']);
			}
			if($this->sefs['layout'] == 'list'){
				unset($query['gid']);
				unset($query['option']);
				unset($query['view']);
			}elseif($this->sefs['layout'] == 'author'){
				if (isset($query['id']))
				{
					$uid = $query['id'];

					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$dbQuery =$this->db->getQuery(true)
							->select('username')
							->from('#__users')
							->where('id=' . (int) $uid);
						$this->db->setQuery($dbQuery);
						$username = $this->db->loadResult();
						$segments[] = $username;
						unset($query['id']);
					}
				}
				$this->sefs['Itemid'] = $menuItem->id;
				// Did we find the current and existing language yet?
				$lang_code = $this->getLanguageCookie();
				$path = $menuItem->route;
				$uri->setPath($path . "/". implode("/", $segments));
				$uri->delVar('option');
				$uri->delVar('view');
				$uri->delVar('layout');
				$uri->delVar('id');
				$uri->delVar('u_id');
				$uri->delVar('Itemid');
				if (!$this->app->get('sef_rewrite'))
				{
					$uri->setPath('index.php/' . $uri->getPath());
				}
				if (!empty($this->cache)) {
					$this->cache->store(array($uri->getPath(), $uri->getQuery(false)), 'build:'.$cacheId);
				}
			}
		}
	}
	/**
	 * postprocess build rule for SEF URLs
	 *
	 * @param   Router  &$router  Router object.
	 * @param   Uri     &$uri     Uri object.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function postprocessSEFBuildRule(&$router, &$uri)
	{
		if($uri->getVar('view',"") == 'author' && $uri->getVar('option','') == 'com_content'){
			if($uri->getVar('layout',"") == 'author' ){
				$uri->delVar('id');
				$uri->delVar('layout');
				$uri->delVar('view');
				$uri->delVar('option');
			}
		}
	}

	public function getMenuAuthorsId()
	{
		$menus = $this->menu->getItems(['component'], ['com_content']);

		foreach ($menus as $menu) {
			if (preg_match('/view=author&layout=list/', $menu->link)) {
				return $menu;
			}
		}
	}
	public function getMenuByAlias($alias){
		$q = $this->db->getQuery(true)
		->select('*')
		->from('#__menu')
		->where('alias ='.$this->db->quote($alias))
		->where('published=1');
		return $this->db->setQuery($q)->loadObject();
	}
	public function getUserId($username = ''){
		if($this->app->get('sef_suffix')){
			$username = str_replace('.html',"",$username);
		}
		$q = $this->db->getQuery(true)
		->select('id')
		->from('#__users')
		->where('username = '. $this->db->quote($username));
		return $this->db->setQuery($q)->loadResult();
	}

	/**
	 * Get the language cookie
	 *
	 * @return  string
	 *
	 * @since   3.4.2
	 */
	private function getLanguageCookie()
	{
		$plugin = PluginHelper::getPlugin('system', 'languagefilter');
		if(empty($plugin)) return "";
		$params = new Registry($plugin->params);
		// Is is set to use a year language cookie in plugin params, get the user language from the cookie.
		if ((int) $params->get('lang_cookie', 0) === 1)
		{
			$languageCode = $this->app->input->cookie->get(ApplicationHelper::getHash('language'));
		}
		// Else get the user language from the session.
		else
		{
			$languageCode = $this->app->getSession()->get('plg_system_languagefilter.language');
		}
		// Let's be sure we got a valid language code. Fallback to null.
		if (!array_key_exists($languageCode, $this->lang_codes))
		{
			$languageCode = null;
		}
		$langArr = $languageCode ? explode("-", $languageCode) : array('en');
		return $langArr[0];
	}
}
