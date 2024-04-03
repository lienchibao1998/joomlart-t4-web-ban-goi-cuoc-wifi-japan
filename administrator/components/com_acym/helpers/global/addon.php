<?php

use AcyMailing\Classes\PluginClass;

global $acymPlugins;
global $acymAddonsForSettings;

function acym_trigger($method, $args = [], $plugin = null, $callbackOnePlugin = null)
{
    if (!in_array(acym_getPrefix().'acym_configuration', acym_getTableList())) return null;

    if (in_array($method, ['replaceContent', 'replaceUserInformation']) && !empty($args[0]->language)) {
        $previousLanguage = acym_setLanguage($args[0]->language);
        acym_loadLanguage($args[0]->language);
    }

    global $acymPlugins;
    global $acymAddonsForSettings;
    if (empty($acymPlugins)) acym_loadPlugins();

    $result = [];
    $listAddons = $acymPlugins;
    if ($method == 'onAcymAddSettings') {
        $listAddons = $acymAddonsForSettings;
    }

    foreach ($listAddons as $class => $onePlugin) {
        if (is_callable($callbackOnePlugin)) $callbackOnePlugin($onePlugin);
        if (!method_exists($onePlugin, $method)) continue;
        if (!empty($plugin) && $class != $plugin) continue;

        try {
            $value = call_user_func_array([$onePlugin, $method], $args);
            if (isset($value)) $result[] = $value;
            if (!empty($onePlugin->errors)) {
                $onePlugin->errorCallback();
            }
        } catch (Exception $e) {
            acym_logError('An error occurred when triggering the method '.$method.': '.$e->getMessage());
        }
    }

    if (!empty($previousLanguage)) {
        acym_setLanguage($previousLanguage);
        acym_loadLanguage($previousLanguage);
    }

    return $result;
}

function acym_checkPluginsVersion()
{
    $pluginClass = new PluginClass();
    $pluginsInstalled = $pluginClass->getMatchingElements();
    $pluginsInstalled = $pluginsInstalled['elements'];
    if (empty($pluginsInstalled)) return true;

    $url = ACYM_UPDATEMEURL.'integrationv6&task=getAllPlugin&cms='.ACYM_CMS;

    $res = acym_fileGetContent($url);
    $pluginsAvailable = json_decode($res, true);
    if (empty($pluginsAvailable)) return true;

    foreach ($pluginsInstalled as $key => $pluginInstalled) {
        foreach ($pluginsAvailable as $pluginAvailable) {
            if (str_replace('.zip', '', $pluginAvailable['file_name']) == $pluginInstalled->folder_name && !version_compare(
                    $pluginInstalled->version,
                    $pluginAvailable['version'],
                    '>='
                )) {
                $pluginsInstalled[$key]->uptodate = 0;
                $pluginsInstalled[$key]->latest_version = $pluginAvailable['version'];
                $pluginClass->save($pluginsInstalled[$key]);
            }
        }
    }

    return true;
}
