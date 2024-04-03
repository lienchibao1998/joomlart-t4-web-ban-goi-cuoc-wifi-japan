<?php
namespace T4\Document;

class Edit extends Template {
	// var $layout = '/t4/edit';
	var $mode = 'edit';

	protected function loadTypelistData () {
		parent::loadTypelistData();
		// disable optimize
		$this->doc->params->set('system_optimizecss', false);
		$this->doc->params->set('system_optimizejs', false);
		// disable addons
		$this->doc->params->set('system_addons', null);
	}

	protected function renderHead() {
		// load google fonts
		$this->loadGoogleFonts();
	}

	public function getHead() {
		$wam = \T4\Helper\Asset::getWebAssetManager();
		$wam->useStyle('font.awesome6');
		$wam->useStyle('font.awesome5');
		$wam->useStyle('font.awesome4');
		$wam->useStyle('font.iconmoon');
		 $this->wam->useAsset('style','fronend.edit');
		 $this->wam->useAsset('script','fronend.edit');
		return parent::getHead();
	}


	// disable cache for edit
	protected function getCachekey() {
		return null;
	}

}
