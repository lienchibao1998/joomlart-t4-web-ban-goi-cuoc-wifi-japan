<?php

namespace AcyMailing\Controllers\Configuration;

trait License
{
    public function unlinkLicense()
    {
        $config = acym_getVar('array', 'config', []);
        $licenseKey = empty($config['license_key']) ? $this->config->get('license_key') : $config['license_key'];

        $resultUnlinkLicenseOnUpdateMe = $this->unlinkLicenseOnUpdateMe($licenseKey);

        if ($resultUnlinkLicenseOnUpdateMe['success'] === true) {
            $this->config->save(['license_key' => '']);
        }

        if (!empty($resultUnlinkLicenseOnUpdateMe['message'])) {
            $this->displayMessage($resultUnlinkLicenseOnUpdateMe['message']);
        }

        $this->listing();

        return true;
    }

    public function attachLicense()
    {
        $config = acym_getVar('array', 'config', []);
        $licenseKey = $config['license_key'];

        if (empty($licenseKey)) {
            $this->displayMessage(acym_translation('ACYM_PLEASE_SET_A_LICENSE_KEY'));
            $this->listing();

            return true;
        }

        $this->config->save(['license_key' => $licenseKey]);

        $resultAttachLicenseOnUpdateMe = $this->attachLicenseOnUpdateMe();

        if ($resultAttachLicenseOnUpdateMe['success'] === false) {
            $this->config->save(['license_key' => '']);
        }

        if (!empty($resultAttachLicenseOnUpdateMe['message'])) {
            $this->displayMessage($resultAttachLicenseOnUpdateMe['message']);
        }

        $this->listing();

        return true;
    }

    public function attachLicenseOnUpdateMe($licenseKey = null)
    {

        if (is_null($licenseKey)) {
            $licenseKey = $this->config->get('license_key', '');
        }

        $return = [
            'message' => '',
            'success' => false,
        ];

        if (empty($licenseKey)) {
            $return['message'] = 'LICENSE_NOT_FOUND';

            return $return;
        }

        $url = ACYM_UPDATEMEURL.'license&task=attachWebsiteKey';

        $fields = [
            'domain' => ACYM_LIVE,
            'license_key' => $licenseKey,
        ];

        $resultAttach = acym_makeCurlCall($url, $fields);

        acym_checkVersion();

        if (empty($resultAttach) || !empty($resultAttach['error'])) {
            $return['message'] = empty($resultAttach['error']) ? '' : $resultAttach['error'];

            return $return;
        }

        $return['message'] = $resultAttach['message'];
        if ($resultAttach['type'] == 'error') {

            return $return;
        }

        $return['success'] = true;

        acym_trigger('onAcymAttachLicense', [&$licenseKey]);

        return $return;
    }

    private function unlinkLicenseOnUpdateMe($licenseKey = null)
    {
        if (is_null($licenseKey)) {
            $licenseKey = $this->config->get('license_key', '');
        }

        $level = $this->config->get('level', '');

        $return = [
            'message' => '',
            'success' => false,
        ];

        if (empty($licenseKey)) {
            $return['message'] = 'LICENSE_NOT_FOUND';

            return $return;
        }

        $this->deactivateCron(false, $licenseKey);

        $url = ACYM_UPDATEMEURL.'license&task=unlinkWebsiteFromLicense';

        $fields = [
            'domain' => ACYM_LIVE,
            'license_key' => $licenseKey,
            'level' => $level,
            'component' => ACYM_COMPONENT_NAME_API,
        ];

        $resultUnlink = acym_makeCurlCall($url, $fields);

        acym_checkVersion();

        if (empty($resultUnlink) || !empty($resultUnlink['error'])) {
            $return['message'] = empty($resultUnlink['error']) ? '' : $resultUnlink['error'];

            return $return;
        }

        if ($resultUnlink['type'] === 'error') {
            if ($resultUnlink['message'] == 'LICENSE_NOT_FOUND' || $resultUnlink['message'] == 'LICENSES_DONT_MATCH') {
                $return['message'] = 'UNLINK_SUCCESSFUL';
                $return['success'] = true;

                return $return;
            }
        }

        if ($resultUnlink['type'] === 'info') {
            $return['success'] = true;
        }

        $return['message'] = $resultUnlink['message'];

        acym_trigger('onAcymDetachLicense');

        return $return;
    }

    public function activateCron($licenseKey = null)
    {
        $result = $this->modifyCron('activateCron', $licenseKey);
        if ($result !== false && $this->displayMessage($result['message'])) $this->config->save(['active_cron' => 1]);
        $this->listing();

        return true;
    }

    public function deactivateCron($listing = true, $licenseKey = null)
    {
        $result = $this->modifyCron('deactivateCron', $licenseKey);
        if ($result !== false && $this->displayMessage($result['message'])) $this->config->save(['active_cron' => 0]);
        if ($listing) $this->listing();

        return true;
    }

    public function modifyCron($functionToCall, $licenseKey = null)
    {
        if (is_null($licenseKey)) {
            $config = acym_getVar('array', 'config', []);
            $licenseKey = empty($config['license_key']) ? '' : $config['license_key'];
        }

        if (empty($licenseKey)) {
            $this->displayMessage('LICENSE_NOT_FOUND');

            return false;
        }

        $url = ACYM_UPDATEMEURL.'launcher&task='.$functionToCall;

        $fields = [
            'domain' => ACYM_LIVE,
            'license_key' => $licenseKey,
            'cms' => ACYM_CMS,
            'frequency' => 900,
            'level' => $this->config->get('level', ''),
            'url_version' => 'secured',
        ];

        $result = acym_makeCurlCall($url, $fields);


        if (empty($result) || !empty($result['error'])) {
            $this->displayMessage(empty($result['error']) ? '' : $result['error']);

            return false;
        }

        if ($result['type'] == 'error') {
            $this->displayMessage($result['message']);

            return false;
        }

        return $result;
    }

    public function call($task, $allowedTasks = [])
    {
        $allowedTasks[] = 'markNotificationRead';
        $allowedTasks[] = 'removeNotification';
        $allowedTasks[] = 'getAjax';
        $allowedTasks[] = 'addNotification';

        parent::call($task, $allowedTasks);
    }
}
