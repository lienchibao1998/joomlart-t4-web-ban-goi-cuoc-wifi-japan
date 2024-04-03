<?php

function acym_getTimeOffsetCMS()
{
    static $timeoffset = null;
    if ($timeoffset === null) {

        $dateC = JFactory::getDate(
            'now',
            acym_getCMSConfig('offset')
        );
        $timeoffset = $dateC->getOffsetFromGMT(true) * 3600;
    }

    return $timeoffset;
}

function acym_dateTimeCMS($time)
{
    return JHTML::_('date', $time, 'Y-m-d H:i:s', null);
}

function acym_getDateTimeFormat($default = 'ACYM_DATE_FORMAT_LC2')
{
    return acym_translation($default);
}
