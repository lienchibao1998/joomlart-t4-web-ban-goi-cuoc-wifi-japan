<?php
namespace T4Admin;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use T4\Helper\Path;
use T4\Helper\Css;
class Admin
{
    public static $template = null;

    public static function init($form, $data)
    {
        if (!self::isT4Template()) {
            return;
        }

        // Define T4 const
        $template = self::getTemplate();

        // upgrade compare new version
        T4Compatible::run($data);
        // Load T4 template params
        Params::load($form, $data);

        // set back template params
        if (!empty($data->params)) {
            self::$template->params = json_encode($data->params);
        }

        // clean draft data
        Draft::clear();

        $doc = Factory::getDocument();
        //create jsLangs
        $langs = array(
            't4save'				=> Text::_("JAPPLY"),
            'customCssSaved'				=> Text::_("T4_CUSTOM_CSS_HAS_SAVED"),
            'patternDelConfirm' => Text::_('TPL_T4_PATTERN_CONFIRM'),
            'OverRideConfirm' => Text::_('T4_OVERRIDE_CONFIRM'),
            'RemoveColConfirm' => Text::_('TPL_T4_COL_REMOVE_CONFIRM'),
            'logoPresent' => Text::_('T4_LAYOUT_LOGO_TEXT'),
            'emptyLayoutPosition' => Text::_('T4_LAYOUT_EMPTY_POSITION'),
            'defaultLayoutPosition' => Text::_('T4_LAYOUT_DEFAULT_POSITION'),

            'layoutConfig' => Text::_('T4_LAYOUT_CONFIG_TITLE'),
            'layoutConfigDesc' => Text::_('T4_LAYOUT_CONFIG_DESC'),
            'layoutUnknownWidth' => Text::_('T4_LAYOUT_UNKN_WIDTH'),
            'layoutPosWidth' => Text::_('T4_LAYOUT_POS_WIDTH'),
            'layoutPosName' => Text::_('T4_LAYOUT_POS_NAME'),

            'layoutCanNotLoad' => Text::_('T4_LAYOUT_LOAD_ERROR'),

            'askCloneLayout' => Text::_('T4_LAYOUT_ASK_ADD_LAYOUT'),
            'correctLayoutName' => Text::_('T4_LAYOUT_ASK_CORRECT_NAME'),
            'askDeleteLayout' => Text::_('T4_LAYOUT_ASK_DEL_LAYOUT'),
            'askDeleteLayoutDesc' => Text::_('T4_LAYOUT_ASK_DEL_LAYOUT_DESC'),
            'askPurgeLayout' => Text::_('T4_LAYOUT_ASK_DEL_LAYOUT'),
            'askPurgeLayoutDesc' => Text::_('T4_LAYOUT_ASK_PURGE_LAYOUT_DESC'),

            'lblDeleteIt' => Text::_('T4_LAYOUT_LABEL_DELETEIT'),
            'lblCloneIt' => Text::_('T4_LAYOUT_LABEL_CLONEIT'),

            'layoutEditPosition' => Text::_('T4_LAYOUT_EDIT_POSITION'),
            'layoutShowPosition' => Text::_('T4_LAYOUT_SHOW_POSITION'),
            'layoutHidePosition' => Text::_('T4_LAYOUT_HIDE_POSITION'),
            'layoutChangeNumpos' => Text::_('T4_LAYOUT_CHANGE_NUMPOS'),
            'layoutDragResize' => Text::_('T4_LAYOUT_DRAG_RESIZE'),
            'layoutHiddenposDesc' => Text::_('T4_LAYOUT_HIDDEN_POS_DESC'),

            'updateFailedGetList' => Text::_('T4_OVERVIEW_FAILED_GETLIST'),
            'updateDownLatest' => Text::_('T4_OVERVIEW_GO_DOWNLOAD'),
            'updateCheckUpdate' => Text::_('T4_OVERVIEW_CHECK_UPDATE'),
            'updateChkComplete' => Text::_('T4_OVERVIEW_CHK_UPDATE_OK'),
            'updateHasNew' => Text::_('T4_OVERVIEW_TPL_NEW'),
            'updateCompare' => Text::_('T4_OVERVIEW_TPL_COMPARE'),
            'switchResponsiveMode' => Text::_('T4_MSG_SWITCH_RESPONSIVE_MODE'),

            'toolImportDataDone' => Text::_('T4_TOOL_IMPORT_DONE'),
            'toolExportNoSelectedGroupsWarning' => Text::_('T4_TOOL_EXPORT_NO_SELECTED_GROUPS_WARNING'),
            'toolImportDataFileError' => Text::_('T4_TOOL_IMPORT_DATA_FILE_ERROR'),
            'toolImportDataFileEmptyWarning' => Text::_('T4_TOOL_IMPORT_DATA_FILE_EMPTY_WARNING'),

            'addonEmptyFieldWaring' => Text::_('T4_ADDONS_EMPTY_FIELD_WARNING'),
            'addonEmptyFieldCssOrJSWaring' => Text::_('T4_ADDONS_EMPTY_CSS_OR_JS_FIELD_WARNING'),
            'addonRemoveConfirm' => Text::_('T4_ADDONS_REMOVE_CONFIRM'),
            'addonRemoveDeleted' => Text::_('T4_ADDONS_DELETED'),
            'addonNameDuplicated' => Text::_('T4_ADDONS_SAVE_DUPLICATED_ERROR'),
            'fontsEmptyFieldCssWaring' => Text::_('T4_CUSTOM_FONT_CSS_MISSED'),
            'fontEmptyFieldFontFileWaring' => Text::_('T4_CUSTOM_FONT_FILE_MISSED'),
            'customColorRemoveConfirm' => Text::_('T4_CUSTOM_COLOR_CONFIRM'),
            'customColordaplicateWaring' => Text::_('T4_CUSTOM_COLOR_DUPLICATED_ERROR'),
            'colorNameNoneWarning' => Text::_('T4_CUSTOM_COLOR_NAME_NONE_ERROR'),
            'colorEmptyFieldWaring' => Text::_('T4_CUSTOM_COLOR_COLOR_FIELD_ERROR'),
            'colorNameEmptyFieldWaring' => Text::_('T4_CUSTOM_COLOR_NAME_FIELD_ERROR'),
            'customColorHasSaved' => Text::_('T4_CUSTOM_COLOR_HAS_SAVED'),
            'customColorRemoveConfirm' => Text::_('T4_CUSTOM_COLOR_CONFIRM_REMOVE'),
            'customColorDeleted' => Text::_('T4_CUSTOM_COLOR_HAS_DELETED'),
            'userColorConfirmEditLabel' => Text::_('T4_CUSTOM_COLOR_CONFIRM_EDIT'),
            'palettesUpdated' => Text::_('T4_COLOR_PALETTES_UPDATED'),
            'typelistConfirmEditlayout' => Text::_('T4_TYPELIST_CONFIRM_EDIT_LAYOUT'),
            'typelistConfirmEdittheme' => Text::_('T4_TYPELIST_CONFIRM_EDIT_THEME'),
            'typelistConfirmEditnavigation' => Text::_('T4_TYPELIST_CONFIRM_EDIT_NAVIGATION'),
            'typelistConfirmEditsite' => Text::_('T4_TYPELIST_CONFIRM_EDIT_SITE'),
            'typelistconfirmlayoutDelete' => Text::_('T4_TYPELIST_CONFIRM_DELETE_LAYOUT'),
            'typelistconfirmlayoutRestore' => Text::_('T4_TYPELIST_CONFIRM_RESTORE_LAYOUT'),
            'typelistconfirmthemeDelete' => Text::_('T4_TYPELIST_CONFIRM_DELETE_THEME'),
            'typelistconfirmthemeRestore' => Text::_('T4_TYPELIST_CONFIRM_RESTORE_THEME'),
            'typelistconfirmnavigationDelete' => Text::_('T4_TYPELIST_CONFIRM_DELETE_NAVIGATION'),
            'typelistconfirmnavigationRestore' => Text::_('T4_TYPELIST_CONFIRM_RESTORE_NAVIGATION'),
            'typelistconfirmsiteDelete' => Text::_('T4_TYPELIST_CONFIRM_DELETE_SITE'),
            'typelistconfirmsiteRestore' => Text::_('T4_TYPELIST_CONFIRM_RESTORE_SITE'),
            'typelistconfirmlayoutDeleted' => Text::_('T4_TYPELIST_CONFIRM_DELETED_LAYOUT'),
            'typelistconfirmlayoutRestored' => Text::_('T4_TYPELIST_CONFIRM_RESTORED_LAYOUT'),
            'typelistconfirmthemeDeleted' => Text::_('T4_TYPELIST_CONFIRM_DELETED_THEME'),
            'typelistconfirmthemeRestored' => Text::_('T4_TYPELIST_CONFIRM_RESTORED_THEME'),
            'typelistconfirmnavigationDeleted' => Text::_('T4_TYPELIST_CONFIRM_DELETED_NAVIGATION'),
            'typelistconfirmnavigationRestored' => Text::_('T4_TYPELIST_CONFIRM_RESTORED_NAVIGATION'),
            'typelistconfirmsiteDeleted' => Text::_('T4_TYPELIST_CONFIRM_DELETED_SITE'),
            'typelistconfirmsiteRestored' => Text::_('T4_TYPELIST_CONFIRM_RESTORED_SITE'),
            'megamenuExtraClass' => Text::_('T4_NAVIGATION_MEGA_EXTRA_CLASS'),
            'megamenuSubmenuWidth' => Text::_('T4_NAVIGATION_SUB_MENU_WIDTH'),
            'megamenuAlignment' => Text::_('T4_NAVIGATION_ALIGNMENT'),
            'megamenuSectionSelectItems' => Text::_('T4_NAVIGATION_MEGA_BUILD_SELECT_ITEMS'),
            'megamenuSectionAllItems' => Text::_('T4_NAVIGATION_MEGA_BUILD_ALL_ITEMS'),
            'colorPalettesConfirmRestore' => Text::_('T4_LAYOUT_PALETTES_CONFIRM_RESTORE'),
            'colorPalettesRestore' => Text::_('T4_LAYOUT_PALETTES_RESTORE'),
            'colorPalettesConfirmDelete' => Text::_('T4_LAYOUT_PALETTES_CONFIRM_DEL'),
            'colorPalettesDelete' => Text::_('T4_LAYOUT_PALETTES_DEL'),
            'butonCloseConfirm' => Text::_('T4_BTN_CLOSE_CONFIRM'),
            't4LayoutRowConfirmDel' => Text::_('T4_LAYOUT_CONFIRM_ROW_DEL'),
            'typelistItemDeleted' => Text::_('T4_TYPE_LIST_DELETED'),
            'typelistCloneSaved' => Text::_('T4_TYPE_LIST_CLONE_SAVE'),
            'palettesRemnoveClone' => Text::_('T4_PALETTES_REMOVE_CLONE'),
            't4LayoutRowDeleted' => Text::_('T4_LAYOUT_ROW_DELETED'),
            'T4BlockNameNone' => Text::_('T4_LAYOUT_BLOCK_NAME_NONE'),
            'T4LayoutSaveBlock' => Text::_('T4_LAYOUT_BLOCK_HAS_SAVED'),
            'T4AddonsHasUpdated' => Text::_('T4_ADDONS_HAS_UPDATED'),
            'T4AddonsHasAdded' => Text::_('T4_ADDONS_HAS_ADDED'),
            'T4fontCustomAdded' => Text::_('T4_CUSTOM_FONT_HAS_ADDED'),
            'T4fontCustomRemoveConfirm' => Text::_('T4_CUSTOM_FONT_CONFIRM_REMOVE'),
            'T4fontCustomRemoved' => Text::_('T4_CUSTOM_FONT_HAS_REMOVED'),
            'T4TypeListSaved' => Text::_('T4_TYPE_LIST_SAVED'),
            'T4loadGoogleFontConfirm' => Text::_('T4_DONT_LOAD_GOOGLE_FONT_CONFIRM'),
            'ExportDataSuccessfuly' => Text::_('T4_TOOL_EXPORT_SUCCESS'),
        );

        // Add loading class when rendering admin layout
        $script = "document.documentElement.classList.add('t4admin-loading');\n";
        $script .= "window.addEventListener('load', function() {setTimeout(function(){document.documentElement.classList.remove('t4admin-loading')}, 1000)})";
        $script .= "; var T4Admin = window.T4Admin || {}; ";
        $script .= " T4Admin.langs = ". json_encode($langs) . "; ";
        $script .= " T4Admin.t4devmode = '" .( Factory::getConfig()->get('devmode') ? 1 : 0 ). "'; ";
        $script .= " T4Admin.jversion = '" . \T4\Helper\J3J4::major() . "'; ";
        $doc->addScriptDeclaration($script);

        // Init js
        $assets_uri = T4PATH_ADMIN_URI . '/assets';
        $doc->addStyleSheet($assets_uri . '/css/dark_theme.css');
        $doc->addStyleSheet($assets_uri . '/css/t4-code.css');
        $doc->addStyleSheet($assets_uri . '/css/t4-ie.css', array('version' => 'auto', 'relative' => true));//, 'conditional' => 'IE'
        //$doc->addStyleSheet($assets_uri . '/css/animate.css');

         // enable jquery.ui
        $wam = \T4\Helper\Asset::getWebAssetManager();
        $wam->useStyle('chosen');
        $wam->useScript('chosen');
        $wam->useStyle('minicolors');
        $wam->useScript('minicolors');
        $wam->useScript('jquery-migrate');
        $doc->addScript($assets_uri . '/js/jquery-ui.min.js');
        $doc->addScript($assets_uri . '/js/overwrite-settings.js');
        // Preview
        $doc->addScript($assets_uri . '/js/preview.js', ['version' => 'auto']);
        $cssRoot = Css::renderRoot($data) . Path::getFileContent('css/tpl/theme.tpl.css');
        $previewjs = "var cssTplStyle = " . json_encode($cssRoot) . ";";
        $previewjs .= "var cssTplPalette = " . json_encode(Path::getFileContent('css/tpl/pattern.tpl.css')) . ";";
        $doc->addScriptDeclaration($previewjs);

        $editorLoader = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js';
        $doc->addScript($editorLoader, [], ['defer' => true]);
        $doc->addScript($assets_uri . '/js/T4CodeEditor.js');
    }

    public static function initj3()
    {
        if (\T4\Helper\J3J4::major() < 4) {
            \JLoader::registerNamespace('Joomla\CMS', T4PATH . '/src/joomla3/src', false, true, 'psr4');
        }
    }


    public static function isT4Template($template = null)
    {
        if (!$template) {
            $template = self::getTemplate();
        }
        if ($template) {
            // parse xml
            $filePath = JPATH_ROOT . '/templates/' . $template . '/templateDetails.xml';

            if (!is_file($filePath)) {
                return false;
            }
            $xml = simplexml_load_file($filePath);
            // check t4
            $base = isset($xml->t4) && isset($xml->t4->basetheme) ? trim(strtolower($xml->t4->basetheme)) : null;

            // not an T4 template, ignore
            if (!$base) {
                return false;
            }

            // validate base
            $path = T4PATH_THEMES . '/' . $base;

            if (!is_dir($path)) {
                return false;
            }

            // define const
            if (!defined('T4PATH_BASE')) {
                define('T4PATH_BASE', $path);
                define('T4PATH_BASE_URI', T4PATH_THEMES_URI . '/' . $base);
            }

            return true;
        }

        return false;
    }

    public static function getTemplate($params = false)
    {
        if (self::$template === '') {
            return null;
        }

        if (self::$template === null) {
            $id = Factory::getApplication()->input->getInt('id');
            $db = Factory::getDbo();

            $query = $db->getQuery(true);
            $query->select(array('*'));
            $query->from($db->quoteName('#__template_styles'));
            $query->where($db->quoteName('client_id') . ' = 0');
            $query->where($db->quoteName('id') . ' = ' . $db->quote($id));

            $db->setQuery($query);

            $tpl = $db->loadObject();
            if (!$tpl || !self::isT4Template($tpl->template)) {
                self::$template = '';
                return null;
            }

            self::$template = $tpl;

            // define template const
            $tpl_path = '/templates/' . $tpl->template;
            define('T4PATH_TPL', JPATH_ROOT . $tpl_path);
            define('T4PATH_TPL_URI', Uri::root(true) . $tpl_path);
            // define local const
            define('T4PATH_LOCAL', T4PATH_TPL . '/local');
            define('T4PATH_LOCAL_URI', T4PATH_TPL_URI . '/local');
        }
        return $params ? self::$template : self::$template->template;
    }
    protected static function initT4AdminJs($path)
    {
        $doc = Factory::getDocument();
    }
    public static function initOffline($data)
    {
       $site_params = json_decode(Path::getFileContent('etc/site/'.$data->params->get('typelist-site'). '.json'),true);
       if(!empty($site_params)){
        $data->site_params = New Registry($site_params);
       }
    }
}
