<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

//group export | import
$groups = ['typelist-site','typelist-navigation','typelist-theme','typelist-layout','system','other'];

//add assets
Factory::getDocument()->addScript(T4PATH_ADMIN_URI . '/assets/js/tools.js');
?>

<div class="tool-css">
    <h4><?php echo Text::_('T4_ADVANCED_TOOLS_CSS_TITLE') ?></h4>
    <p class="description">
      <?php echo Text::_('T4_ADVANCED_TOOLS_CSS_DESC') ?>
    </p>
    <span class="t4-btn btn-action btn-primary" data-action="tool.css"><i class="fal fa-file-edit"></i><?php echo Text::_('T4_ADVANCED_TOOLS_CSS_LABEL') ?></span>
</div>
<div class="t4-css-editor-modal" style="display:none;">
    <div class="t4-modal-overlay"></div>
    <div class="t4-modal t4-css-editor" data-target="#">
        <div class="t4-modal-header">
            <span class="t4-modal-header-title"><i class="fal fa-cog"></i>Css Editor</span>
            <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
        </div>
        <div class="t4-modal-inner t4-css-editor-inner">
            <div class="t4-modal-content tab-pane">
                <div id="t4_code_css" name="t4_css" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
        <div class="t4-modal-footer">
            <button class="btn btn-secondary btn-xs t4-settings-cancel" type="button"><span class="fal fa-times"></span> <?php echo Text::_('JCANCEL');?></button>
            <button class="btn btn-success btn-xs t4-css-editor-apply" type="button" data-flag="css-editors"><span class="fal fa-check"></span> <?php echo Text::_('JAPPLY');?></button>
        </div>
    </div>
</div>

<div class="tool-css">
    <h4><?php echo Text::_('T4_ADVANCED_TOOLS_SCSS_TITLE') ?></h4>
    <p class="description">
        <?php echo Text::_('T4_ADVANCED_TOOLS_SCSS_DESC') ?>
    </p>
    <span class="t4-btn btn-action btn-primary" data-action="tool.scss"><i class="fal fa-file-edit"></i><?php echo Text::_('T4_ADVANCED_TOOLS_SCSS_LABEL') ?></span>
</div>
<div id="t4-tool-scss-modal" style="display:none;">
	<div class="t4-modal-overlay"></div>
    <div class="t4-modal t4-css-editor" data-target="#">
        <div class="t4-modal-header">
            <span class="t4-modal-header-title"><i class="fal fa-cog"></i>SCSS Tools</span>
            <a href="#" class="action-t4-modal-close"><span class="fal fa-times"></span></a>
        </div>
        <div class="t4-modal-inner t4-css-editor-inner">
            <div class="t4-modal-content tab-pane">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#t4-scss-variables" aria-controls="variables" role="tab" data-toggle="tab">Variables</a></li>
                        <li role="presentation"><a href="#t4-scss-custom" aria-controls="custom" role="tab" data-toggle="tab">Custom Style</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="t4-scss-variables">
                            <div id="t4-scss-editor-variables"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="t4-scss-custom">
                            <div id="t4-scss-editor-custom"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="t4-modal-footer">
            <button class="btn btn-secondary btn-xs t4-settings-cancel" type="button"><span class="fal fa-times"></span> <span class="btn-text">Close</span></button>
            <button class="btn btn-success btn-xs" data-action="apply" data-flag="css-editors" type="button"><span class="fal fa-check"></span> <span class="btn-text">Save & Compile</span></button>
            <button class="btn btn-danger btn-xs" data-action="clean" data-flag="css-editors" type="button"><span class="fal fa-trash"></span> <span class="btn-text">Remove Local CSS</span></button>
        </div>
    </div>
</div>