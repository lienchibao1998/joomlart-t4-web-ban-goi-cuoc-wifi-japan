<?php

namespace AcyMailing\Controllers;

use AcyMailing\Libraries\acymController;
use AcyMailing\Controllers\Mails\Listing;
use AcyMailing\Controllers\Mails\Edition;
use AcyMailing\Controllers\Mails\Automation;

class MailsController extends acymController
{
    use Listing;
    use Edition;
    use Automation;

    public function __construct()
    {
        parent::__construct();
        $type = acym_getVar('string', 'type');
        $this->setBreadcrumb($type);
        acym_header('X-XSS-Protection:0');
    }

    protected function setBreadcrumb($type)
    {
        $mailClass = $this->currentClass;
        switch ($type) {
            case $mailClass::TYPE_AUTOMATION:
                $breadcrumbTitle = 'ACYM_AUTOMATION';
                $breadcrumbUrl = acym_completeLink('automation');
                break;
            case $mailClass::TYPE_FOLLOWUP:
                $breadcrumbTitle = 'ACYM_EMAILS';
                $breadcrumbUrl = acym_completeLink('mails');
                break;
            default:
                $breadcrumbTitle = 'ACYM_TEMPLATES';
                $breadcrumbUrl = acym_completeLink('mails');
        }

        $this->breadcrumb[acym_translation($breadcrumbTitle)] = $breadcrumbUrl;
    }


}

