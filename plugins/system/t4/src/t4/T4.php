<?php

namespace T4;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

class T4
{

	/** @var T4\Document\Template $doc */
	protected $doc = null;

	public function __construct()
	{
	}

	public static function getInstance($doc = null)
	{
		static $t4 = null;
		if (!$t4) {
			$t4 = new self();
		}
		if ($doc) $t4->setDocument($doc);
		return $t4;
	}

	public static function fixContentRoute()
	{
		if (version_compare(JVERSION, 4, 'ge')) {
			$loader = require JPATH_LIBRARIES . '/vendor/autoload.php';

			$classMap = $loader->getClassMap();
			$classMap['Joomla\Component\Content\Site\Service\Router'] = T4PATH . '/src/t4/MVC/Router/Content/Router.php';

			$loader->addClassMap($classMap);
		}
	}
	
	/**
	 * Magic method to proxy T4 method calls to T4\Document\Template
	 *
	 * @param   string  $name       Name of the function
	 * @param   array   $arguments  Array of arguments for the function
	 *
	 * @return  mixed
	 *
	 * @since   1.7.0
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->getDocument(), $name), $arguments);
	}

	public function init()
	{
		// Check base theme
		$template = Factory::getApplication()->getTemplate();
		// parse xml
		$filePath = JPATH_THEMES . '/' . $template . '/templateDetails.xml';
		$base = null;
		$bs5 = null;
		if (is_file($filePath)) {
			$xml = $xml = simplexml_load_file($filePath);
			// check t4
			if (isset($xml->t4) && isset($xml->t4->basetheme)) {
				$base = trim(strtolower($xml->t4->basetheme));
			}
		}

		// not an T4 template, ignore
		if (!$base) return;
		// define load bootstrap 4 | 5 on template
		$bs5 = trim(strtolower($xml->t4->bootstrap));
		if ($bs5 == 'bs5') define("T4_BS5", 1);
		// validate base
		$path = T4PATH_THEMES . '/' . $base;
		if (!is_dir($path)) return;

		// define const
		define('T4PATH_BASE', T4PATH_THEMES . '/' . $base);
		define('T4PATH_BASE_URI', T4PATH_THEMES_URI . '/' . $base);

		// define template const
		$tpl_path = '/templates/' . $template;
		define('T4PATH_TPL', JPATH_ROOT . $tpl_path);
		define('T4PATH_TPL_URI', Uri::root(true) . $tpl_path);
		// define local const
		$local_path = '/templates/' . $template . '/local';
		define('T4PATH_LOCAL', T4PATH_TPL . '/local');
		define('T4PATH_LOCAL_URI', T4PATH_TPL_URI . '/local');

		// overwrite original Joomla
		$loader = require JPATH_LIBRARIES . '/vendor/autoload.php';
		// update class maps
		$classMap = $loader->getClassMap();
		$classMap['Joomla\CMS\Layout\FileLayout'] = T4PATH . '/src/joomla/src/Layout/FileLayout.php';
		$classMap['Joomla\CMS\Helper\ModuleHelper'] = T4PATH . '/src/joomla/src/Helper/ModuleHelper.php';
		$classMap['Joomla\CMS\MVC\View\HtmlView'] = T4PATH . '/src/joomla/src/MVC/View/HtmlView.php';

		// override Pagination for J3
		if (Helper\J3J4::major() < 4) {
			$classMap['Joomla\CMS\Pagination\Pagination'] = T4PATH . '/src/joomla3/src/Pagination/Pagination.php';
			\JLoader::registerNamespace('Joomla\CMS', T4PATH . '/src/joomla3/src', false, true, 'psr4');

			// for overwrite html class
			\JLoader::registerPrefix('J', T4PATH . '/src/joomla3/cms', false, true);

			// For com_config
			\JLoader::registerPrefix('Config', T4PATH . '/src/joomla3/config', false, true);

			// Register renderer author
			\JLoader::registerAlias('ContentViewAuthor', '\\T4\\MVC\\View\\Author');
			\JLoader::registerAlias('ContentModelAuthor', '\\T4\\MVC\\Model\\Author');
		} else {
			$classMap['Joomla\Component\Content\Site\View\Author\HtmlView'] = T4PATH . '/src/t4/MVC/View/Author/HtmlView.php';
			$classMap['Joomla\Component\Content\Site\Model\AuthorModel'] = T4PATH . '/src/t4/MVC/Model/AuthorModel.php';
			$classMap['Joomla\CMS\HTML\Helpers\Bootstrap'] = T4PATH . '/src/joomla4/src/HTML/Helpers/Bootstrap.php';
		}

		$loader->addClassMap($classMap);

		// Register renderer
		\JLoader::registerAlias('JDocumentRendererHtmlElement', '\\T4\\Renderer\\Element');
	}

	public function getDocument($doc = null)
	{
		if (!$this->doc) {
			$this->doc = \T4\Document\Template::getInstance($doc);
		}
		return $this->doc;
	}

	public function setDocument($doc)
	{
		// $this->doc = $doc;
		$this->doc = \T4\Document\Template::getInstance($doc);
	}

	public function renderTemplate($doc)
	{
		$doc = $this->getDocument($doc);
		echo $doc->render();
	}

	public function compileHead()
	{
		if (!$this->isT4()) return;
		$this->getDocument()->compileHead();
		Optimizer\Base::run();
	}

	// Build default settings in default template style
	public function buildTemplateParams()
	{
		$app = Factory::getApplication();
		$template = $app->getTemplate(true);
		$defaultTpl = Helper\TemplateStyle::getMaster($template->template);

		if ($template->id != $defaultTpl->id) {
			Helper\TemplateStyle::updateDefaultSettings($defaultTpl, $template);
		}

		Helper\TemplateStyle::initDefault($template);
	}


	public function contentPrepareForm($form, $data)
	{
		if (($this->isSite() && !$this->isT4())) return;
		Helper\ExtraField::extendForm($form, $data);
	}

	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if ($this->isSite() && !$this->isT4()) return;
		$this->loadBSComponent($article);
		return $this->renderOpenGraph($context, $article, $params);
	}
	public function loadBSComponent($article)
	{
		$buffer = $article->text;
		if (version_compare(JVERSION, "4", "ge")) {
			$wam = Factory::getDocument()->getWebAssetManager();
			$buffer = str_replace(array('data-toggle', 'data-title', 'data-dismiss', 'data-trigger', 'data-target', 'data-slide', 'data-ride', 'data-interval'), array('data-bs-toggle', 'data-bs-title', 'data-bs-dismiss', 'data-bs-trigger', 'data-bs-target', 'data-bs-slide', 'data-bs-ride', 'data-bs-interval'), $buffer);
			if (preg_match('/data-bs-toggle="tab"/mi', $buffer, $matches)) {
				$wam->useScript('bootstrap.tab');
			}
			if (preg_match('/data-bs-ride="carousel"/mi', $buffer, $matches)) {
				$wam->useScript('bootstrap.carousel');
			}
			if (preg_match('/data-bs-toggle="collapse"/mi', $buffer, $matches)) {
				$wam->useScript('bootstrap.collapse');
			}
		}
	}

	public function isSite()
	{
		return Factory::getApplication()->isClient('site');
	}

	public function onContentBeforeSave($context, $data, $isNew)
	{
		if (!$this->isT4()) return;
		Helper\ExtraField::onContentBeforeSave($context, $data, $isNew);
	}
	/**
	 * Static function
	 */
	public static function isT4()
	{
		return defined('T4PATH_BASE');
	}
	public static function isCurrentT4()
	{
		$app = Factory::getApplication();
		$tmpId = $app->input->getInt('id', '');
		if (empty($tmpId)) return false;
		return Helper\TemplateStyle::checkCurrentT4template($tmpId);
	}

	// Alias function
	public static function render($doc)
	{
		$t4 = self::getInstance($doc);
		$t4->renderTemplate($doc);
	}
	public static function inEdit()
	{
		$input = Factory::getApplication()->input;
		$inedit = ($input->get('layout') == 'edit' || ($input->get('option') == 'com_config' && $input->get('view') != 'templates'));
		return $inedit;
	}
	/**
	 * Performs the display event.
	 *
	 * @param   string    $context      The context
	 * @param   \stdClass  $item         The item
	 * @param   Registry  $params       The params
	 * @param   integer   $displayType  The type
	 *
	 * @return  string
	 *
	 * @since   3.7.0
	 */
	public function onContentAuthordisplay($context, $item, $params, $displayType)
	{
		if ($context != 'com_content.article' || !$this->isT4()) return "";
		$app = Factory::getApplication();
		$template = $app->getTemplate(true);
		$author  = Helper\Author::render($item, $params, $displayType, $template->params);

		return $author;
	}
	public function renderOpenGraph($context, $item, $params)
	{
		if (!$this->isT4()) return true;
		Helper\Metadata::renderOpenGraph($context, $item, $params);
	}
}
