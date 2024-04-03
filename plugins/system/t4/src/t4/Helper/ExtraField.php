<?php
namespace T4\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Categories\Categories;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Table\Table;

class ExtraField {

	public static function extendForm($form, $data) {

		$form_name = $form->getName();
		// Extend extra field
		$template = TemplateStyle::getDefault();

		if ($template) {
			// parse xml
			$filePath = JPATH_SITE . '/templates/' . $template . '/templateDetails.xml';
			$base = null;
			if (is_file ($filePath)) {
				$xml = simplexml_load_file($filePath);
				// check t4
				if (isset($xml->t4) && isset($xml->t4->basetheme)) {
					$base = trim(strtolower($xml->t4->basetheme));
				}
			}

			// not an T4 template, ignore
			if (!$base) return;
			// validate base
			$path = T4PATH_THEMES . '/' . $base;
			if (!is_dir($path)) return;

			// define const
			if(!defined('T4PATH_BASE')) define('T4PATH_BASE', T4PATH_THEMES . '/' . $base);
			if(!defined('T4PATH_BASE_URI')) define('T4PATH_BASE_URI', T4PATH_THEMES_URI . '/' . $base);

			// make it compatible with AMM
			if ($form_name == 'com_advancedmodules.module') $form_name = 'com_modules.module';

			$tplpath  = JPATH_ROOT . '/templates/' . $template;
			$formpath = $tplpath . '/etc/form/';
			Form::addFormPath($formpath);

			$extended = $formpath . $form_name . '.xml';
			if (is_file($extended)) {
				Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
				$form->loadFile($form_name, false);
			}

			// load extra fields for specified module in format com_modules.module.module_name.xml
			if ($form_name == 'com_modules.module') {
				$module = isset($data->module) ? $data->module : '';
				if (!$module) {
					$jform = Factory::getApplication()->input->get ("jform", null, 'array');
					$module = $jform['module'];
				}
				$extended = $formpath . $module . '.xml';
				if (is_file($extended)) {
					Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
					$form->loadFile($extended, false);
				}
			}
			// load extra fields for specified module override edit on frontend with format com_configs.modules.module_name.xml
			if ($form_name == 'com_config.modules') {
				$jinput = Factory::getApplication()->input;
				$mod_id = $jinput->get ("id", null, 'string');
				$module = ModuleHelper::getModuleById($mod_id);
				if ($module->title) {
					
					$extended = $formpath . 'com_modules.module.xml';
					$extended2 = $formpath . 'mod_'.$module->name . '.xml';
					if (is_file($extended)) {
						Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
						$form->loadFile($extended, false);
					}
					if (is_file($extended2)) {
						Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
						$form->loadFile($extended2, false);
					}

				}
			}

			
			//extend form folder
			$extended_form = $tplpath . '/etc/form/' . $form_name . '.xml';
			if (is_file($extended_form)) {
				Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
				if (isset($data->attribs) && is_string($data->attribs))
	    	{
	    		$data->attribs = json_decode($data->attribs, true);
	    	}
				$form->loadFile($extended_form, false);

			}
			//extend extra fields
			self::contentExtraFields($form, $data, $tplpath,$template);
			
			//extend params on t4 plg
			self::onMenuCompareForm($form, $data);
		}
		self::onUserCompareForm($form, $data);

		// Extended by T4
		$extended = T4PATH_ADMIN . '/form/' . $form_name . '.xml';
		if (is_file($extended)) {
			Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
			if (isset($data->attribs) && is_string($data->attribs)) 
			{
				$data->attribs = json_decode($data->attribs, true);
			}

			switch ($form_name) {
				case 'com_menus.item':
					$systemLinks = ['heading', 'alias', 'separator', 'url'];

					if ($data->type && !in_array($data->type, $systemLinks)) {
						$form->loadFile($extended, false);
					}

					break;
				
				default:
					$form->loadFile($extended, false);
					break;
			}
		}

	}

	public static function contentExtraFields($form, $data, $tplpath,$template){
		//load languages
		Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
		if ($form->getName() == 'com_categories.categorycom_content' || $form->getName() == 'com_content.article') {

			// check for extrafields overwrite
			$path = $tplpath . '/etc/extrafields';
			if (!is_dir ($path)) return ;
			$files = Folder::files($path, '.xml');
			if (!$files || !count($files)){
				return ;
			}
			$extras = array();
			foreach ($files as $file) {
				$extras[] = File::stripExt($file);
			}

			if (count($extras)) {

				if ($form->getName() == 'com_categories.categorycom_content'){
					
				

					$_xml =
						'<?xml version="1.0"?>
						<form>
							<fields name="params">
								<fieldset name="t4_extrafields_params" label="T4_EXTRA_FIELDS_GROUP_LABEL" description="T4_EXTRA_FIELDS_GROUP_DESC">
									<field name="t4_extrafields" type="list" default="" show_none="true" label="T4_EXTRA_FIELDS_LABEL" description="T4_EXTRA_FIELDS_DESC">
										<option value="">JNONE</option>';
									
									foreach ($extras as $extra) {
										$_xml .= '<option value="' . $extra . '">' . ucfirst($extra) . '</option>';
									}

									$_xml .= '
									</field>
								</fieldset>
							</fields>
						</form>
						';
					$xml = simplexml_load_string($_xml);
					$form->load ($xml, false);

				} else {
					
					$app   = Factory::getApplication();
					$input = $app->input;
					$fdata = empty($data) ? $input->post->get('jform', array(), 'array') : (is_object($data) ? $data->getProperties() : $data);
					
					if (isset($data->attribs) && is_string($data->attribs))
	      	{
	      		$data->attribs = json_decode($data->attribs, true);
	      	}

					if(!empty($fdata['catid']) && is_array($fdata['catid'])) { // create new
						$catid = end($fdata['catid']);
					} else { // edit
						$catid = ($fdata['catid']);
					}

					if($catid){
						$categories = Categories::getInstance('Content', array('countItems' => 0 ));
						$category = $categories->get($catid);
						$params = $category->params;
						if(!$params instanceof Registry) {
							$params = new Registry;
							$params->loadString($category->params);
						}

						if($params instanceof Registry){
							$extrafile = $path . '/' . $params->get('t4_extrafields') . '.xml';
							if(is_file($extrafile)){
								Form::addFormPath($path);
								Factory::getLanguage()->load('tpl_' . $template, JPATH_SITE);
								$form->loadFile($params->get('t4_extrafields'), false);
							}
						}
					}
				}
			}
		}
	}
	public static function onContentBeforeSave($context, $data, $isNew)
	{
		if(isset($data->attribs)){
			$contentTable = Table::getInstance('Content', 'JTable',array());
			$contentTable->load($data->id);
			$oldAttribs = new Registry($contentTable->attribs);
			$attribs = new Registry($data->attribs);
			$oldAttribs->merge($attribs);
			$data->attribs = $oldAttribs->toString();
		}
	}
	public static function onMenuCompareForm($form,$data)
	{
		$formName = $form->getName();
		if($formName == 'com_menus.item'){
			if(empty($data->request)){
				return;
			}
			$component = $data->request['option'];
			$view = $data->request['view'];
			$layout = isset($data->request['layout']) ? $data->request['layout'] : "default";
			$template = TemplateStyle::getDefault();
			$xmlFile = T4PATH_BASE . '/html/' .$component .'/'.$view.'/'.$layout.'.xml';
			if(is_file($xmlFile)){
				if ($form->loadFile($xmlFile, true, '/metadata') == false)
				{
					throw new \Exception(Text::_('JERROR_LOADFILE_FAILED'));
				}
			}
		}
		
	}
	public static function onUserCompareForm($form, $data)
	{
		
		// Check we are manipulating a valid form.
		$name = $form->getName();

		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile')))
		{
			return true;
		}
		//check required plugin user profile
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('enabled')
		->from('#__extensions')
		->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
		->where($db->quoteName('element') . ' = ' . $db->quote('profile'));
		if(!$db->setQuery($query)->loadResult()){
			return true;
		}
		// Add the registration fields to the form.
		Form::addFormPath(T4PATH_BASE . '/params');
		Factory::getLanguage()->load('plg_system_' . T4_PLUGIN, JPATH_ADMINISTRATOR);
		$form->loadFile('user');
	}
}