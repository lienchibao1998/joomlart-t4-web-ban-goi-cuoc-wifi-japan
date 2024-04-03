<?php
/*------------------------------------------------------------------------
# mod_jt_languageswitcher Module
# ------------------------------------------------------------------------
# author    joomlatema
# copyright Copyright (C) 2022 joomlatema.net. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlatema.net
-------------------------------------------------------------------------*/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root().'/modules/mod_jt_languageswitcher/assets/css/style.css');

$showActive=$params->get('show_activelang')==1;
$itemCount=count($list);
if ($showActive) {$itemCount=$itemCount+1; }

///////////
$separator=" ";
$Flagborder=$params->get('Flagborder');
$BorderThickness = explode($separator, $Flagborder);
$BorderThick= $BorderThickness[0];
$BorderThicknopx= str_replace('px', '', $BorderThick);
$BorderThicknopx=$BorderThicknopx*2;
$FlagBorderAll=$BorderThicknopx*$itemCount;
/////////////
$FlagHeight=$params->get('FlagHeight', 30);
$FlagHeightAll=$FlagHeight*$itemCount;
$FlagPaddingAll=$itemCount*8;

///////////
$LiMinHeight=$FlagHeight+4;
$ContHeight=$FlagPaddingAll+$FlagHeightAll+$FlagBorderAll+10;

$FlagFloat =  $params->get('FlagFloat');
if ($FlagFloat=='right')  {
$FlagMargin = 'left';
}
else   {
$FlagMargin = 'right';
}
$TooltipWidth='fit-content';
$FlagMarginValue = $params->get('FlagMarginValue',5);
 if ($params->get('ShowNameCode')== 0){
 $FlagMarginValue=0;
 $TooltipWidth='40px'; 
 $FlagCss="display:flex;flex-direction:column;align-items: center;";
 }
 else if ($params->get('ShowNameCode')== 1){
  $FlagCss='';
 }

?>
<style type="text/css">
#select-container {height:<?php $ButminHeight=$LiMinHeight+4; echo $ButminHeight;?>px;top:<?php echo $params->get('PositionTop');?>;;}
#select-container:hover {height: <?php echo $ContHeight; ?>px;}
[tooltip]::after {content: attr(tooltip);width:<?php echo $TooltipWidth; ?>;}
#select-container ul li img,#select-container button.active-lang img{width:<?php echo $params->get('FlagWidth', 30);?>px;height:<?php echo $params->get('FlagHeight', 30);?>px;border-radius:<?php echo $params->get('FlagborderRad');?>;border:<?php echo $params->get('Flagborder');?>;box-shadow: 0px 0px 6px rgba(79, 104, 113, 0.3);}
#select-container ul li {min-height:<?php echo $LiMinHeight;?>px;}
#select-container button{height:<?php $ButminHeight=$BorderThicknopx+$LiMinHeight; echo $ButminHeight;?>px;line-height:<?php echo $LiMinHeight;?>px;}
#select-container ul li span.langname-code {height:<?php echo $LiMinHeight;?>px;display: grid;align-content: center;}
#select-container ul.lang-block<?php echo $module->id; ?>{<?php echo $FlagCss;?>}
@media screen and (max-width:767px){
#select-container {height:<?php $ButminHeight=$LiMinHeight+4; echo $ButminHeight;?>px;top:<?php echo $params->get('PositionTopMobile');?>;;}
}
</style>
	
<div class="mod-jt-languageswitcher">
<?php if ($list) : ?>
<div id="select-container">
 <div class="mod-jt-languageswitcher__select">
		<?php foreach ($list as $k => $language) : ?>
			<?php if ($language->active) : ?>
				<button  id="language_btn_<?php echo $module->id; ?>" class="active-lang" aria-haspopup="listbox" aria-labelledby="language_picker_des_<?php echo $module->id; ?> language_btn_<?php echo $module->id; ?>" aria-expanded="false">
					<?php if ($params->get('dropdownimage', 1) && ($language->image)) : ?>
					<span class="img-cover" style="float:<?php echo $params->get('FlagFloat');?>;margin-<?php echo $FlagMargin;?>:<?php echo $FlagMarginValue;?>px;"><img src="<?php echo JURI::root().'/modules/mod_jt_languageswitcher/assets/images/' . $language->image . '.png'; ?>"/></span>
					<?php endif; ?>
					<?php if ($params->get('ShowNameCode')== 1) : ?><span class="langname-code"><?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?></span><?php endif; ?>
				</button>
			<?php endif; ?>
		<?php endforeach; ?>
		<ul role="listbox" aria-labelledby="language_picker_des_<?php echo $module->id; ?>" class="lang-block<?php echo $module->id; ?>">
		<?php foreach ($list as $k => $language) : ?>
			<?php 
			$flow='';
			if ($k === array_key_last($list)){
			      $flow="up";
				  }
				  else
				  {$flow="down";
				  }
				$lbl = '';
				if ($params->get('full_name') === 0)
				{
					$lbl = 'aria-label="' . $language->title_native . '"';
				}
			?> 
			<?php if (!$language->active) : ?>
				<li lang-selection="<?php echo $language->image; ?>" <?php if ($params->get('ShowTooltip', 1)) : ?> tooltip="<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?>"<?php endif; ?>  flow="<?php echo $flow; ?>">
					<a role="option" <?php echo $lbl; ?> href="<?php echo htmlspecialchars_decode(htmlspecialchars($language->link, ENT_QUOTES, 'UTF-8'), ENT_NOQUOTES); ?>">
						<?php if ($params->get('dropdownimage', 1) && ($language->image)) : ?>
						<span class="img-cover" style="float:<?php echo $params->get('FlagFloat');?>;margin-<?php echo $FlagMargin;?>:<?php echo $FlagMarginValue;?>px;"> <img src="<?php echo JURI::root().'/modules/mod_jt_languageswitcher/assets/images/' . $language->image . '.png'; ?>"/></span>
						<?php endif; ?>
						<?php if ($params->get('ShowNameCode')== 1) : ?><span class="langname-code"><?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?></span><?php endif; ?>
					</a>
				</li>
			<?php elseif ($params->get('show_activelang', 1)) : ?>
				<?php $base = Uri::getInstance(); ?>
				<li lang-selection="<?php echo $language->image; ?>" <?php if ($params->get('ShowTooltip', 1)) : ?> tooltip="<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?>"<?php endif; ?> flow="<?php echo $flow; ?>" class="lang-active">
					<a aria-current="true" role="option" <?php echo $lbl; ?> href="<?php echo htmlspecialchars_decode(htmlspecialchars($base, ENT_QUOTES, 'UTF-8'), ENT_NOQUOTES); ?>">
						<?php if ($params->get('dropdownimage', 1) && ($language->image)) : ?>
							<span class="img-cover" style="float:<?php echo $params->get('FlagFloat');?>;margin-<?php echo $FlagMargin;?>:<?php echo $FlagMarginValue;?>px;"> <img src="<?php echo JURI::root().'/modules/mod_jt_languageswitcher/assets/images/' . $language->image . '.png'; ?>"/></span>
						<?php endif; ?>
						<?php if ($params->get('ShowNameCode')== 1) : ?><span class="langname-code"><?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?></span><?php endif; ?>
					</a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>
	</div>
    
  </ul>
</div>
<?php else : ?>
<?php echo "<h5 style='margin:0 15px;'>".JText::_('MOD_JTLANGSW_ERRORMSG'). "</h5>";
?>

<?php endif; ?>
</div>

<script type="text/javascript">

var container = document.getElementById('select-container');
var items = container.getElementsByTagName('ul')[0].getElementsByTagName('li');
var selectedItem = items[0];

hideSelected();

function onSelect(item) {
  showUnselected();
  selectedItem.innerHTML = item.innerHTML;
  selectedItem.setAttribute('lang-selection', item.getAttribute('lang-selection'));
  selectedItem.setAttribute('tooltip', item.getAttribute('tooltip'));
  hideSelected();
  unwrapSelector();
}

function unwrapSelector() {
  container.style.pointerEvents = "none";
  setTimeout(() => container.style.pointerEvents = "auto", 200);
}

function showUnselected() {
  let selectedLangCode = selectedItem.getAttribute('lang-selection');

  for (let i = 1; i < items.length; i++) {
    if (items[i].getAttribute('lang-selection') == selectedLangCode) {
      items[i].style.opacity = '1';
      items[i].style.display = '';
      break;
    }
  }
}

function hideSelected() {
  let selectedLangCode = selectedItem.getAttribute('lang-selection');

  for (let i = 1; i < items.length; i++) {
    if (items[i].getAttribute('lang-selection') == selectedLangCode) {
      items[i].style.opacity = '0';
      setTimeout(() => items[i].style.display = 'none', 200);
      break;
    }
  }
}</script>