<?php

namespace AcyMailing\Helpers;

use AcyMailing\Classes\AutomationClass;
use AcyMailing\Classes\CampaignClass;
use AcyMailing\Classes\FollowupClass;
use AcyMailing\Classes\MailboxClass;
use AcyMailing\Classes\QueueClass;
use AcyMailing\Classes\UserClass;
use AcyMailing\Classes\UserStatClass;
use AcyMailing\Libraries\acymObject;

class CronHelper extends acymObject
{
    var $report = false;
    var $messages = [];
    var $detailMessages = [];

    var $processed = false;

    var $executed = false;

    var $mainmessage = '';

    var $errorDetected = false;

    var $skip = [];

    var $emailtypes = [];

    var $startQueue = 0;

    var $externalSendingActivated = false;
    var $externalSendingRepeat = false;
    var $externalSendingNotFinished = false;

    public function __construct()
    {
        parent::__construct();
        $this->startQueue = acym_getVar('int', 'startqueue', 0);

        acym_trigger('onAcymProcessQueueExternalSendingCampaign', [&$this->externalSendingActivated]);

        $this->externalSendingRepeat = !empty(acym_getVar('int', 'external_sending_repeat', 0));
        if (!empty($this->startQueue) || !empty($this->externalSendingRepeat)) {
            $this->skip = [
                'schedule',
                'cleanqueue',
                'bounce',
                'automation',
                'campaign',
                'specific',
                'followup',
                'delete_history',
                'clean_data_external_sending_method',
                'clean_export_changes',
                'mailbox_action',
            ];
        }
    }

    public function cron()
    {

        if (acym_getVar('int', 'unlink', 0) === 1) {
            $callLicenseKey = acym_getVar('string', 'licenseKey');
            $configLicenseKey = $this->config->get('license_key');

            if (!empty($configLicenseKey) && $configLicenseKey === $callLicenseKey) {
                $this->config->save(['license_key' => '', 'active_cron' => 0]);
            }

            exit;
        }

        $time = time();

        $firstMessage = acym_translationSprintf('ACYM_CRON_TRIGGERED', acym_date('now', 'd F Y H:i'));
        $this->messages[] = $firstMessage;
        if ($this->report) {
            acym_display($firstMessage, 'info');
        }

        if (empty($this->startQueue)) {
            if ($this->config->get('cron_next') > $time) {
                if ($this->config->get('cron_next') > ($time + $this->config->get('cron_frequency'))) {
                    $newConfig = new \stdClass();
                    $newConfig->cron_next = $time + $this->config->get('cron_frequency');
                    $this->config->save($newConfig);
                }

                $nottime = acym_translationSprintf('ACYM_CRON_NEXT', acym_date($this->config->get('cron_next'), 'd F Y H:i'));
                $this->messages[] = $nottime;
                if ($this->report) {
                    acym_display($nottime, 'info');
                }

                return false;
            }

            $newConfig = new \stdClass();
            $newConfig->cron_last = $time;
            $newConfig->cron_fromip = acym_getIP();
            $newConfig->cron_next = $this->config->get('cron_next') + $this->config->get('cron_frequency');

            if ($newConfig->cron_next <= $time || $newConfig->cron_next > $time + $this->config->get('cron_frequency')) {
                $newConfig->cron_next = $time + $this->config->get('cron_frequency');
            }

            $this->config->save($newConfig);
        }

        $this->executed = true;


        if (!in_array('schedule', $this->skip)) {
            $queueClass = new QueueClass();
            $nbScheduled = $queueClass->scheduleReady();
            if ($nbScheduled) {
                $this->messages[] = acym_translationSprintf('ACYM_NB_SCHEDULED', $nbScheduled);
                $this->detailMessages = array_merge($this->detailMessages, $queueClass->messages);
                $this->processed = true;
            }
        }

        if (!in_array('cleanqueue', $this->skip)) {
            $deletedNb = $queueClass->cleanQueue();

            if (!empty($deletedNb)) {
                $this->messages[] = acym_translationSprintf('ACYM_EMAILS_REMOVED_QUEUE_CLEAN', $deletedNb);
                $this->processed = true;
            }
        }

        $fromHour = $this->config->get('queue_send_from_hour', '00');
        $fromMinute = $this->config->get('queue_send_from_minute', '00');
        $toHour = $this->config->get('queue_send_to_hour', '23');
        $toMinute = $this->config->get('queue_send_to_minute', '59');
        if ($fromHour != '00' || $fromMinute != '00' || $toHour != '23' || $toMinute != '59') {
            $dayBasedOnCMSTimezone = acym_date('now', 'Y-m-d');
            $fromBasedOnCMSTimezoneAtSpecifiedHour = acym_getTimeFromCMSDate($dayBasedOnCMSTimezone.' '.$fromHour.':'.$fromMinute);
            $toBasedOnCMSTimezoneAtSpecifiedHour = acym_getTimeFromCMSDate($dayBasedOnCMSTimezone.' '.$toHour.':'.$toMinute);
            $time = time();
            if ($fromBasedOnCMSTimezoneAtSpecifiedHour > $toBasedOnCMSTimezoneAtSpecifiedHour) {
                if ($time > $fromBasedOnCMSTimezoneAtSpecifiedHour) {
                    $toBasedOnCMSTimezoneAtSpecifiedHour = acym_getTimeFromCMSDate(acym_date('tomorrow', 'Y-m-d').' '.$toHour.':'.$toMinute);
                } elseif ($time < $toBasedOnCMSTimezoneAtSpecifiedHour) {
                    $fromBasedOnCMSTimezoneAtSpecifiedHour = acym_getTimeFromCMSDate(acym_date('yesterday', 'Y-m-d').' '.$fromHour.':'.$fromMinute);
                }
            }
            if ($time < $fromBasedOnCMSTimezoneAtSpecifiedHour || $time > $toBasedOnCMSTimezoneAtSpecifiedHour) {
                $this->skip[] = 'send';
            }
        }
        $dayOfWeek = acym_date('now', 'N');
        if ($this->config->get('queue_stop_weekend', 0) && $dayOfWeek >= 6) {
            $this->skip[] = 'send';
        }
        if ($this->config->get('queue_type') != 'manual' && !in_array('send', $this->skip)) {
            $this->multiCron();
            $queueHelper = new QueueHelper();
            $queueHelper->send_limit = (int)$this->config->get('queue_nbmail_auto');
            $queueHelper->report = false;
            $queueHelper->emailtypes = $this->emailtypes;
            $queueHelper->process();
            if (!empty($queueHelper->messages)) {
                $this->detailMessages = array_merge($this->detailMessages, $queueHelper->messages);
            }
            if (!empty($queueHelper->nbprocess)) {
                $this->processed = true;

                if (!$queueHelper->finish && $this->externalSendingActivated) {
                    $this->externalSendingNotFinished = true;
                }
            }
            $this->mainmessage = acym_translationSprintf('ACYM_CRON_PROCESS', $queueHelper->nbprocess, $queueHelper->successSend, $queueHelper->errorSend);
            $this->messages[] = $this->mainmessage;

            if (!empty($queueHelper->errorSend)) {
                $this->errorDetected = true;
            }
            if (!empty($queueHelper->stoptime) && time() > $queueHelper->stoptime) {
                return true;
            }
        }

        if (!in_array('bounce', $this->skip) && acym_level(ACYM_ENTERPRISE) && $this->config->get('auto_bounce', 0) && $time > (int)$this->config->get(
                'auto_bounce_next',
                0
            ) && (empty($queueHelper->stoptime) || time() < $queueHelper->stoptime - 5)) {
            $newConfig = new \stdClass();
            $newConfig->auto_bounce_next = $time + (int)$this->config->get('auto_bounce_frequency', 0);
            $newConfig->auto_bounce_last = $time;
            $this->config->save($newConfig);
            $bounceHelper = new BounceHelper();
            $bounceHelper->report = false;
            $queueHelper = new QueueHelper();
            $bounceHelper->stoptime = $queueHelper->stoptime;
            $newConfig = new \stdClass();
            if ($bounceHelper->init() && $bounceHelper->connect()) {
                $nbMessages = $bounceHelper->getNBMessages();
                $nbMessagesReport = acym_translationSprintf('ACYM_NB_MAIL_MAILBOX', $nbMessages);
                $this->messages[] = $nbMessagesReport;
                $newConfig->auto_bounce_report = $nbMessagesReport;
                $this->detailMessages[] = $nbMessagesReport;
                if (!empty($nbMessages)) {
                    $bounceHelper->handleMessages();
                    $this->processed = true;
                }
                $this->detailMessages = array_merge($this->detailMessages, $bounceHelper->messages);
            } else {
                $bounceErrors = $bounceHelper->getErrors();
                $newConfig->auto_bounce_report = implode('<br />', $bounceErrors);
                if (!empty($bounceErrors[0])) {
                    $bounceErrors[0] = acym_translation('ACYM_BOUNCE_HANDLING').' : '.$bounceErrors[0];
                }
                $this->messages = array_merge($this->messages, $bounceErrors);
                $this->processed = true;
                $this->errorDetected = true;
            }
            $this->config->save($newConfig);

            if (!empty($queueHelper->stoptime) && time() > $queueHelper->stoptime) {
                return true;
            }
        }

        if (!in_array('automation', $this->skip) && acym_level(ACYM_ENTERPRISE)) {
            $automationClass = new AutomationClass();
            $automationClass->trigger('classic');

            $userStatusCheckTriggers = [];
            acym_trigger('onAcymDefineUserStatusCheckTriggers', [&$userStatusCheckTriggers]);
            $automationClass->trigger($userStatusCheckTriggers);

            if (!empty($automationClass->report)) {
                if ($automationClass->didAnAction) $this->processed = true;
                $this->messages = array_merge($this->messages, $automationClass->report);
            }

            if (!empty($queueHelper->stoptime) && time() > $queueHelper->stoptime) {
                return true;
            }
        }

        if (!in_array('campaign', $this->skip) && acym_level(ACYM_ENTERPRISE)) {
            $campaignClass = new CampaignClass();
            $campaignClass->triggerAutoCampaign();
            if (!empty($campaignClass->messages)) {
                $this->messages = array_merge($this->messages, $campaignClass->messages);
                $this->processed = true;
            }
        }

        if (!in_array('specific', $this->skip)) {
            $campaignClass = new CampaignClass();
            $specialTypes = [];
            acym_trigger('getCampaignTypes', [&$specialTypes]);
            $specialMail = $campaignClass->getCampaignsByTypes($specialTypes, true);
            acym_trigger('filterSpecificMailsToSend', [&$specialMail, $time]);
            foreach ($specialMail as $onespecialMail) {
                $campaignClass->send($onespecialMail->id);
            }
        }

        if (!in_array('followup', $this->skip)) {
            $followupClass = new FollowupClass();
            $followups = $followupClass->getFollowupDailyBases();

            $dailyHour = $this->config->get('daily_hour', '12');
            $dailyMinute = $this->config->get('daily_minute', '00');
            $dayBasedOnCMSTimezone = acym_date('now', 'Y-m-d');
            $dayBasedOnCMSTimezoneAtSpecifiedHour = acym_getTimeFromCMSDate($dayBasedOnCMSTimezone.' '.$dailyHour.':'.$dailyMinute);
            $time = time();

            foreach ($followups as $followup) {
                $lastTrigger = empty($followup->last_trigger) ? '' : acym_date($followup->last_trigger, 'Y-m-d');
                if ($time >= $dayBasedOnCMSTimezoneAtSpecifiedHour && $lastTrigger != $dayBasedOnCMSTimezone) {
                    if (!empty($followup->condition)) $followup->condition = json_decode($followup->condition, true);
                    acym_trigger('onAcymFollowupDailyBasesNeedToBeTriggered', [$followup]);
                }
            }
        }

        if (!in_array('mailbox_action', $this->skip) && acym_level(ACYM_ENTERPRISE)) {
            $mailboxActionClass = new MailboxClass();
            $mailboxes = $mailboxActionClass->getAllActiveReadyWithActions();

            if (!empty($mailboxes)) {
                $mailboxHelper = new MailboxHelper();
                $mailboxHelper->report = false;
                foreach ($mailboxes as $oneMailboxAction) {
                    $this->processed = true;

                    if (!$mailboxHelper->isConnectionValid($oneMailboxAction, false)) {
                        $this->messages[] = acym_translationSprintf('ACYM_CONNECTION_FAILED_MAILBOX_X', $oneMailboxAction->id.'-'.$oneMailboxAction->name);
                        $this->errorDetected = true;
                        continue;
                    }

                    $nbMessages = $mailboxHelper->getNBMessages();
                    if (!$nbMessages) {
                        $this->messages[] = acym_translation('ACYM_NO_MESSAGE_IN_MAILBOX_X', $oneMailboxAction->id.'-'.$oneMailboxAction->name);
                        $mailboxHelper->close();
                        continue;
                    }

                    $mailboxHelper->handleAction();
                    $mailboxHelper->close();


                    $oneMailboxAction->nextdate = time() + $oneMailboxAction->frequency;
                    unset($oneMailboxAction->conditions);
                    unset($oneMailboxAction->actions);
                    $mailboxActionClass->save($oneMailboxAction);
                }
            }
        }

        if (!in_array('delete_history', $this->skip) && $this->isDailyCron()) {
            $userStatClass = new UserStatClass();
            $userClass = new UserClass();

            $userDetailedStatsDeleted = $userStatClass->deleteDetailedStatsPeriod();
            $userHistoryDeleted = $userClass->deleteHistoryPeriod();
            if (!empty($userDetailedStatsDeleted['message'])) {
                $this->messages[] = $userDetailedStatsDeleted['message'];
                $this->processed = true;
            }
            if (!empty($userHistoryDeleted['message'])) {
                $this->messages[] = $userHistoryDeleted['message'];
                $this->processed = true;
            }
        }

        if (!in_array('clean_data_external_sending_method', $this->skip) && $this->isDailyCron()) {
            acym_trigger('onAcymCleanDataExternalSendingMethod');
        }

        if (!in_array('clean_export_changes', $this->skip) && $this->isDailyCron()) {
            $exportHelper = new ExportHelper();
            $exportHelper->cleanExportChangesFile();
        }

        if ($this->isDailyCron()) {
            $this->config->save(['cron_last_daily' => $time]);
        }

        if ($this->externalSendingNotFinished) acym_makeCurlCall(acym_frontendLink('cron&external_sending_repeat=1'), [], [], true);

        return true;
    }

    public function report()
    {
        $sendreport = $this->config->get('cron_sendreport');
        $mailer = new MailerHelper();

        if (($sendreport == 2 && $this->processed) || $sendreport == 1 || ($sendreport == 3 && $this->errorDetected)) {
            $mailer->report = false;
            $mailer->autoAddUser = true;
            $mailer->addParam('report', implode('<br />', $this->messages));
            $mailer->addParam('mainreport', $this->mainmessage);
            $mailer->addParam('detailreport', implode('<br />', $this->detailMessages));

            $receiverString = $this->config->get('cron_sendto');
            $receivers = [];
            if (substr_count($receiverString, '@') > 1) {
                $receivers = explode(' ', trim(preg_replace('# +#', ' ', str_replace([';', ','], ' ', $receiverString))));
            } else {
                $receivers[] = trim($receiverString);
            }

            if (!empty($receivers)) {
                foreach ($receivers as $oneReceiver) {
                    $mailer->sendOne('acy_report', $oneReceiver);
                }
            }
        }

        if (!$this->executed) {
            return;
        }
        $this->saveReport();

        $newConfig = new \stdClass();
        $newConfig->cron_report = implode("\n", $this->messages);
        if (strlen($newConfig->cron_report) > 800) {
            $newConfig->cron_report = substr($newConfig->cron_report, 0, 795).'...';
        }
        $this->config->save($newConfig);
    }

    public function saveReport()
    {
        $saveReport = $this->config->get('cron_savereport');
        if (!(($saveReport == 2 && $this->processed) || $saveReport == 1 || ($saveReport == 3 && $this->errorDetected))) return;

        $reportPath = $this->config->get('cron_savepath');
        if (empty($saveReport) || empty($reportPath)) return;

        $reportPath = str_replace(['{year}', '{month}'], [date('Y'), date('m')], $reportPath);
        $reportPath = acym_cleanPath(ACYM_ROOT.trim(html_entity_decode($reportPath)));
        acym_createDir(dirname($reportPath), true, true);

        $lr = "\r\n";
        ob_start();
        file_put_contents(
            $reportPath,
            $lr.$lr.'******************** '.acym_getDate(time()).' UTC ********************'.$lr.implode($lr, $this->messages),
            FILE_APPEND
        );

        if ($saveReport == 2 && !empty($this->detailMessages)) {
            file_put_contents(
                $reportPath,
                $lr.'---- Details ----'.$lr.implode($lr, $this->detailMessages),
                FILE_APPEND
            );
        }
        $potentialWarnings = ob_get_clean();

        if (!empty($potentialWarnings)) {
            $this->messages[] = $potentialWarnings;
        }
    }

    private function multiCron()
    {
        if (!empty($this->startQueue) || !acym_level(ACYM_ENTERPRISE)) return;
        $emailsBatches = $this->config->get('queue_batch_auto', 1);
        $emailsBatches = intval($emailsBatches);
        $emailsPerBatches = $this->config->get('queue_nbmail_auto', 70);
        if ($emailsBatches < 2 || empty($emailsPerBatches)) return;

        $urls = [];

        for ($i = 1 ; $i <= $emailsBatches - 1 ; $i++) {
            $urls[] = acym_frontendLink('cron&startqueue='.($emailsPerBatches * $i));
        }

        acym_asyncCurlCall($urls);
    }

    private function isDailyCron()
    {
        $dailyHour = $this->config->get('daily_hour', '12');
        $dailyMinute = $this->config->get('daily_minute', '00');
        $dayBasedOnCMSTimezone = acym_date('now', 'Y-m-d');
        $dayBasedOnCMSTimezoneAtSpecifiedHour = acym_getTimeFromCMSDate($dayBasedOnCMSTimezone.' '.$dailyHour.':'.$dailyMinute);

        $time = time();

        $lastCronDayBasedOnCMSTimezone = acym_date($this->config->get('cron_last_daily', $time - 86400), 'Y-m-d');

        return $time >= $dayBasedOnCMSTimezoneAtSpecifiedHour && $lastCronDayBasedOnCMSTimezone != $dayBasedOnCMSTimezone;
    }

    public function addSkipFromString($skipVar)
    {
        if (empty($skipVar)) return;

        $skipVar = explode(',', $skipVar);

        if (empty($skipVar)) return;

        $this->skip = array_unique(array_merge($this->skip, $skipVar));
    }
}
