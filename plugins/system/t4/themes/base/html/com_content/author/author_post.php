<?php
/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);
$params->set('show_author',0);
// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

?>

<?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
<div class="item-content">
	<!-- item title -->
	<?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>
	<!-- item author -->
	<?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'position' => 'above'));  ?>
	<div class="intro-txt">
		<?php echo HTMLHelper::_('string.truncate',$this->item->introtext,200,false,false); ?>
	</div>	
</div>

