<?php

namespace AcyMailing\Controllers\Campaigns;

use AcyMailing\Classes\CampaignClass;
use AcyMailing\Classes\FollowupClass;
use AcyMailing\Classes\MailClass;
use stdClass;


trait Actions
{
    public function duplicate()
    {
        $campaignsSelected = acym_getVar('int', 'elements_checked');

        $campaignClass = new CampaignClass();
        $mailClass = new MailClass();
        $campaignId = 0;

        foreach ($campaignsSelected as $campaignSelected) {
            $campaign = $campaignClass->getOneById($campaignSelected);

            unset($campaign->id);
            unset($campaign->sending_date);
            $campaign->draft = 1;
            $campaign->sent = 0;
            $campaign->active = 0;
            if (!empty($campaign->sending_params['resendTarget'])) {
                unset($campaign->sending_params['resendTarget']);
            }

            $mail = $mailClass->getOneById($campaign->mail_id);
            $oldMailId = $mail->id;
            unset($mail->id);
            $mail->creation_date = acym_date('now', 'Y-m-d H:i:s', false);
            $mail->name .= '_copy';
            $idNewMail = $mailClass->save($mail);

            $translations = $mailClass->getTranslationsById($oldMailId, true);
            foreach ($translations as $oneTranslation) {
                unset($oneTranslation->id);
                $oneTranslation->creation_date = acym_date('now', 'Y-m-d H:i:s', false);
                $oneTranslation->name .= '_copy';
                $oneTranslation->parent_id = $idNewMail;
                $mailClass->save($oneTranslation);
            }

            $campaign->mail_id = $idNewMail;
            $campaignId = $campaignClass->save($campaign);

            $allLists = $campaignClass->getListsByMailId($oldMailId);

            $campaignClass->manageListsToCampaign($allLists, $idNewMail);
        }

        acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_DUPLICATED_SUCCESS'));

        if (count($campaignsSelected) == 1 && acym_getVar('string', 'step', '') == 'summary') {
            acym_setVar('id', $campaignId);
            $this->editEmail();
        } else {
            $this->listing();
        }
    }

    public function duplicateFollowup()
    {
        $followupsSelected = acym_getVar('int', 'elements_checked');

        $followupClass = new FollowupClass();
        $mailClass = new MailClass();

        foreach ($followupsSelected as $oneFollowup) {
            $followUp = $followupClass->getOneByIdWithMails($oneFollowup);
            $followupEmails = $followUp->mails;

            unset($followUp->id, $followUp->creation_date, $followUp->list_id, $followUp->mails);
            $followUp->name .= '_copy';
            $followUp->active = 0;
            $newfollowUpId = $followupClass->save($followUp);
            if (empty($newfollowUpId)) {
                acym_enqueueMessage(acym_translation('ACYM_FOLLOWUP_DUPLICATE_ERROR'), 'error');
                $this->listing();

                return;
            }
            $followUp->id = $newfollowUpId;

            foreach ($followupEmails as $oneEmail) {
                $mail = $mailClass->getOneById($oneEmail->id);
                unset($mail->id, $mail->creation_date, $mail->creator_id, $mail->autosave);
                $newMailId = $mailClass->save($mail);
                if (empty($newMailId)) {
                    acym_enqueueMessage(acym_translation('ACYM_FOLLOWUP_DUPLICATE_ERROR'), 'error');
                    continue;
                }
                $followupData = [
                    'id' => $newfollowUpId,
                    'delay' => $oneEmail->delay,
                    'delay_unit' => $oneEmail->delay_unit,
                ];
                $followupClass->saveDelaySettings($followupData, $newMailId);
            }
        }
        $this->listing();
    }

    public function unpause_campaign()
    {
        $id = acym_getVar('int', 'id', 0);
        if (empty($id)) {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_NOT_FOUND'), 'error');
            $this->listing();

            return;
        }

        acym_redirect(acym_completeLink('queue', false, true).'&task=playPauseSending&acym__queue__play_pause__active__new_value=1&acym__queue__play_pause__campaign_id='.$id);
    }

    public function stopSending()
    {
        $this->_stopAction('stopSendingCampaignId');
    }

    public function stopScheduled()
    {
        $this->_stopAction('stopScheduledCampaignId');
    }

    private function _stopAction($action)
    {
        acym_checkToken();

        $campaignID = acym_getVar('int', $action);
        $campaignClass = new CampaignClass();

        if (!empty($campaignID)) {
            $campaign = new stdClass();
            $campaign->id = $campaignID;
            $campaign->active = 0;
            $campaign->draft = 1;

            $campaignId = $campaignClass->save($campaign);
            if (empty($campaignId)) {
                acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_CANT_BE_SAVED'), 'error');
            } else {
                acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'));
            }
        } else {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_CANT_BE_SAVED'), 'error');
        }
        $this->listing();
    }

    public function confirmCampaign()
    {
        $this->updateOpenAcymailerPopup();
        $campaignId = acym_getVar('int', 'id');
        $campaignSendingDate = acym_getVar('string', 'sending_date');
        $resendTarget = acym_getVar('cmd', 'resend_target', '');
        $campaignClass = new CampaignClass();

        $campaign = new stdClass();
        $campaign->id = $campaignId;
        $campaign->draft = 0;
        $campaign->active = 1;
        $campaign->sent = 0;

        if (!empty($resendTarget)) {
            $currentCampaign = $campaignClass->getOneById($campaignId);
            $currentCampaign->sending_params['resendTarget'] = $resendTarget;
            $campaign->sending_params = $currentCampaign->sending_params;
            acym_trigger('onAcymResendCampaign', [$currentCampaign->mail_id]);
        }

        $resultSave = $campaignClass->save($campaign);

        if ($resultSave) {
            acym_enqueueMessage(acym_translationSprintf('ACYM_CONFIRMED_CAMPAIGN', acym_date($campaignSendingDate, 'j F Y H:i')));
        } else {
            acym_enqueueMessage(acym_translation('ACYM_CANT_CONFIRM_CAMPAIGN').' : '.end($campaignClass->errors), 'error');
        }

        $this->listing();
    }

    public function activeAutoCampaign()
    {
        $this->updateOpenAcymailerPopup();
        $campaignId = acym_getVar('int', 'id');
        $campaignClass = new CampaignClass();

        $campaign = new stdClass();
        $campaign->id = $campaignId;
        $campaign->draft = 0;
        $campaign->active = 1;
        $campaign->sent = 0;

        $resultSave = $campaignClass->save($campaign);

        if ($resultSave) {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_IS_ACTIVE'));
        } else {
            acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVING'), 'error');
        }

        $this->listing();
    }

    public function saveAsDraftCampaign()
    {
        $campaignId = acym_getVar('int', 'id');
        $campaignClass = new CampaignClass();

        $campaign = new stdClass();
        $campaign->id = $campaignId;
        $campaign->draft = 1;
        $campaign->active = 0;

        $resultSave = $campaignClass->save($campaign);

        if ($resultSave) {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_SUCCESSFULLY_SAVE_AS_DRAFT'));
        } else {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_CANT_BE_SAVED').' : '.end($campaignClass->errors), 'error');
        }

        $this->listing();
    }

    public function toggleActivateColumnCampaign()
    {

        $campaignId = acym_getVar('int', 'id');
        $campaignClass = new CampaignClass();

        $campaign = $campaignClass->getOneById($campaignId);
        if (empty($campaign)) {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_CANT_BE_SAVED').' : '.end($campaignClass->errors), 'error');
            $this->listing();

            return;
        }

        $campaign->active = empty($campaign->active) ? 1 : 0;

        if ($campaign->active === 0 && $campaign->sending_type === $campaignClass::SENDING_TYPE_AUTO) {
            $campaign->next_trigger = null;
        }

        $resultSave = $campaignClass->save($campaign);

        if ($resultSave) {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_SUCCESSFULLY_SAVE_AS_DRAFT'));
        } else {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_CANT_BE_SAVED').' : '.end($campaignClass->errors), 'error');
        }

        $this->listing();
    }

    public function addQueue()
    {
        $this->updateOpenAcymailerPopup();
        acym_checkToken();

        $campaignID = acym_getVar('int', 'id', 0);

        if (empty($campaignID)) {
            acym_enqueueMessage(acym_translation('ACYM_CAMPAIGN_NOT_FOUND'), 'error');
        } else {
            $campaignClass = new CampaignClass();
            $campaign = $campaignClass->getOneByIdWithMail($campaignID);

            $resendTarget = acym_getVar('cmd', 'resend_target', '');
            if (!empty($resendTarget)) {
                $currentCampaign = $campaignClass->getOneById($campaignID);
                $currentCampaign->sending_params['resendTarget'] = $resendTarget;
                $campaignClass->save($currentCampaign);
                acym_trigger('onAcymResendCampaign', [$currentCampaign->mail_id]);
            }

            $status = $campaignClass->send($campaignID);

            if ($status) {
                acym_enqueueMessage(acym_translationSprintf('ACYM_CAMPAIGN_ADDED_TO_QUEUE', $campaign->name), 'info');
            } else {
                if (empty($campaignClass->errors)) {
                    acym_enqueueMessage(acym_translationSprintf('ACYM_ERROR_QUEUE_CAMPAIGN', $campaign->name), 'error');
                } else {
                    acym_enqueueMessage($campaignClass->errors, 'error');
                }
            }
        }

        $this->_redirectAfterQueued();
    }

    private function _redirectAfterQueued()
    {
        if (acym_isAdmin() && (!acym_level(ACYM_ESSENTIAL) || $this->config->get('cron_last', 0) < (time() - 43200))) {
            acym_redirect(acym_completeLink('queue&task=campaigns', false, true));
        } else {
            $this->listing();
        }
    }

    private function updateOpenAcymailerPopup()
    {
        if (acym_isAdmin() && $this->config->get('mailer_method') === 'acymailer' && $this->config->get('acymailer_popup', '0') === '0') {
            $this->config->save(['acymailer_popup' => '1']);
        }
    }
}
