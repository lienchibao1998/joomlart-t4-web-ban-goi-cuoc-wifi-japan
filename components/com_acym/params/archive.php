<?php

class JFormFieldArchive extends acym_JFormField
{
    var $type = 'archive';

    public function getInput()
    {
        $ds = DIRECTORY_SEPARATOR;
        $helper = rtrim(JPATH_ADMINISTRATOR, $ds).$ds.'components'.$ds.'com_acym'.$ds.'helpers'.$ds.'helper.php';
        if ('Joomla' === 'Joomla' && !include_once $helper) {
            echo 'This extension cannot work without AcyMailing';
        }

        $value = empty($this->value) ? 0 : $this->value;

        return acym_select(
            [
                '5' => '5',
                '10' => '10',
                '15' => '15',
                '20' => '20',
                '30' => '30',
                '50' => '50',
                '100' => '100',
                '200' => '200',
            ],
            $this->name,
            $value
        );
    }
}
