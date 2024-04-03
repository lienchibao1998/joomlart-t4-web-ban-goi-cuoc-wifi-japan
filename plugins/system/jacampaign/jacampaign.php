<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
/**
 *
 * @author JoomlArt
 *
 */
class plgSystemJacampaign extends JPlugin
{
     /**
     *
     * Construct JA Offer
     * @param object $subject
     * @param object $config
     */
    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
        $this->plugin = JPluginHelper::getPlugin('system', 'jacampaign');
        $this->plgParams = new JRegistry();
        $this->plgParams->loadString($this->plugin->params);
    }
    public function onAfterInitialise(){
        if (version_compare(JVERSION, '4.0', 'ge')) {
            // joomla 4 will missing language. so we load language.
            $lang = JFactory::getLanguage();
            $extension = 'plg_system_jacampaign';
            $base_dir = JPATH_ADMINISTRATOR;
            $language_tag = 'en-GB';
            $reload = true;
            $lang->load($extension, $base_dir, $language_tag, $reload);
        }
        $mainframe = JFactory::getApplication();
        if('com_users' == $mainframe->input->getCMD('option') && !$mainframe->isClient('administrator')) {
            if(version_compare(JVERSION, '4.0', "ge")){
                require_once(dirname(__FILE__) . '/helpers/com_users_registration_model_j4.php');
            }else{
                require_once(dirname(__FILE__) . '/helpers/com_users_registration_modelOverride.php');
            }
            
        }
    }
    public function onAfterRoute() {
        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $language = JFactory::getLanguage();
        $language->load('tpl_ja_campaign', JPATH_SITE , $language->getTag(), true);

        

        if('com_content' == $mainframe->input->getCMD('option') && 'form' == $mainframe->input->getCMD('view') && !$mainframe->isClient('administrator')){
            if(!$user->id){
                $mainframe->redirect(JRoute::_('index.php?option=com_users&view=login',false));
            }
        }
    }

    public function onContentBeforeSave($context, $row, $isNew)
    { 
        $app = JFactory::getApplication();
        if ($app->getName() != 'site') {
            return true;
        }
        // Check we are handling the frontend edit form.
        if ($context !== 'com_content.form')
        {
            return true;
        }
        if($isNew){

            return true;
        }

        $row->state = '0';

        $config = JFactory::getConfig();
        $db = JFactory::getDbo();
        // Get all admin users
        $query = $db->getQuery(true)
                ->clear()
                ->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('sendEmail') . ' = 1')
                ->where($db->quoteName('block') . ' = 0');

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        if (empty($rows))
        {
            return true;
        }

        
        $user = JFactory::getUser();

        $jcfields = FieldsHelper::getFields('com_content.article',  $row, True);

        $offerVal = "";
        foreach ($jcfields as $jcfield) {
            if($jcfield->name == 'offer-details'){
                $offerVal = $jcfield->value;
            }
            
        }
        $result = true;
        $SubTitle = $this->params->get('email_to_admin_subject');
        $email_Body = $this->params->get('email_to_admin_body');
        $email_Body = str_replace('{title}', $row->title, $email_Body);
        $email_Body = str_replace('{offer}', $offerVal, $email_Body);
        //sending email to admin and coordinator
        foreach ($rows as $row)
        {
            if ($row->id != $user->id)
            {
                //sendding to user submit article
                $result = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $row->email, $SubTitle, $email_Body,true);
                
            }
        }

        return $result;
    }
    public function onContentAfterSave($context, $row, $isNew)
    {
        // Check we are handling the frontend edit form.
        if ($context !== 'com_content.form')
        {
            return true;
        }

        // Check this is a new article.
        if (!$isNew)
        {
            return true;
        }

        $config = JFactory::getConfig();
        $db = JFactory::getDbo();
        // Get all admin users
        $query = $db->getQuery(true)
                ->clear()
                ->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
                ->from($db->quoteName('#__users'))
                ->where($db->quoteName('sendEmail') . ' = 1')
                ->where($db->quoteName('block') . ' = 0');

        $db->setQuery($query);
        $userLists = $db->loadObjectList();
        
        if (empty($userLists))
        {
            return true;
        }

        
        $user = JFactory::getUser();

        $result = true;
        $emailSubject = $this->params->get('email_submitoffer_subject');
        $emailBody = $this->params->get('email_submitoffer_body');
        $emailBody = str_replace('{title}', $row->title, $emailBody);
        $jcfields = FieldsHelper::getFields('com_content.article',  $row, True);

        $offerVal = "";
        foreach ($jcfields as $jcfield) {
            if($jcfield->name == 'offer-details'){
                $offerVal = $jcfield->value;
            }
            
        }

        //sendding to user submit article
        $result = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $user->email, $emailSubject, $emailBody,true);
        
        $emailAdminSub = $this->params->get('email_to_admin_subject');
        $emailAdminBody = $this->params->get('email_to_admin_body');
        $emailAdminBody = str_replace('{title}', $row->title, $emailAdminBody);
        $emailAdminBody = str_replace('{offer}', $offerVal, $emailAdminBody);
        
        //sending email to admin and coordinator
        foreach ($userLists as $userList)
        {
            if ($userList->id != $user->id)
            {
            
                //sendding to user submit article
                $result = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $userList->email, $emailAdminSub, $emailAdminBody,true);
            }
        }

        return $result;
    }
    public function onBeforeCompileHead()
    {
        //only going to run these in the frontend for now
        $app = JFactory::getApplication();
        if ($app->isClient('administrator')) {
            return;
        }
        $modalshow = $this->plgParams->get('modal_popup');
        if(!$modalshow) {
            return true;
        }
        $document = JFactory::getDocument();
        $document->addScript(JUri::root() . "plugins/system/jacampaign/assets/js/script.js");
    }

    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        if ($app->getName() != 'site') {
            return true;
        }

        $modalshow = $this->plgParams->get('modal_popup');
        if(!$modalshow) {
            return true;
        }
        $modalTitle = $this->plgParams->get('modal_title');
        $modalbody = $this->plgParams->get('modal_description');
        $modal_duration = $this->plgParams->get('modal_duration');
        if(is_null($modalTitle) && is_null($modalbody)) return true;

        if(is_null($modalTitle)){
            $btnClose = '<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>';
        }else{
            $btnClose = '<div class="modal-header">
                          <h5 class="modal-title" id="popupModalLabel">'.$modalTitle.'</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>';
        }
        $buffer = JFactory::getApplication()->getBody();
            $insert = '<div class="modal fade social-modal" id="popupModal" tabindex="-1" role="dialog" data-duration="'.$modal_duration.'" aria-labelledby="popupModalLabel" aria-hidden="true" style="color:#000;">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        '.$btnClose.'
                        <div class="modal-body">
                          <div class="description">
                            '.$modalbody.'
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>';  
        $buffer= str_ireplace('</body>',$insert."\n</body>",$buffer);

        JFactory::getApplication()->setBody($buffer);

        return true;
    }
}