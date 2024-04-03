<?php

/**
T4 Overide
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$fieldsets = $this->form->getFieldsets();

if (isset($fieldsets['core'])) {
	unset($fieldsets['core']);
}

if (isset($fieldsets['params'])) {
	unset($fieldsets['params']);
}

$tmp          = $this->data->jcfields ?? array();
$customFields = array();

foreach ($tmp as $customField) {
	$customFields[$customField->name] = $customField;
}

?>
<?php foreach ($fieldsets as $group => $fieldset) : ?>
	<?php $fields = $this->form->getFieldset($group); ?>
	<?php if (count($fields)) : ?>
		<fieldset id="users-profile-custom-<?php echo $group; ?>" class="com-users-profile__custom users-profile-custom-<?php echo $group; ?>">
			<?php if (isset($fieldset->label) && ($legend = trim(Text::_($fieldset->label))) !== '') : ?>
				<legend><?php echo $legend; ?></legend>
			<?php endif; ?>
			<?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
				<p><?php echo $this->escape(Text::_($fieldset->description)); ?></p>
			<?php endif; ?>
			<dl class="dl-horizontal row">
				<?php foreach ($fields as $field) : ?>
					<?php if (!$field->hidden && $field->type !== 'Spacer') : ?>
						<dt class="col-sm-3">
							<?php echo $field->title; ?>
						</dt>
						<dd class="col-sm-9">
							<?php if ($field->fieldname === 'user_avatar' && $field->value) : ?>
								<img class="img-thumbnail" src="<?php echo Uri::root() . $field->value; ?>" style="max-height: 50px;">
							<?php elseif ($field->fieldname === 'user_social' && $field->value) : ?>
								<div>
									<?php foreach ((array) $field->value as $value) : ?>
										<?php $_value = (array) $value; ?>
										<a href="<?php echo $_value['social_link'] ?>" target="_Blank" title="<?php echo $_value['social_name'] ?>">
											<span class="<?php echo $_value['social_icon'] ?>" target="_Blank"></span>
										</a>
									<?php endforeach ?>
								</div>
							<?php elseif (array_key_exists($field->fieldname, $customFields)) : ?>
								<?php echo strlen($customFields[$field->fieldname]->value) ? $customFields[$field->fieldname]->value : Text::_('COM_USERS_PROFILE_VALUE_NOT_FOUND'); ?>
							<?php elseif (HTMLHelper::isRegistered('users.' . $field->id)) : ?>
								<?php echo HTMLHelper::_('users.' . $field->id, $field->value); ?>
							<?php elseif (HTMLHelper::isRegistered('users.' . $field->fieldname)) : ?>
								<?php echo HTMLHelper::_('users.' . $field->fieldname, $field->value); ?>
							<?php elseif (HTMLHelper::isRegistered('users.' . $field->type)) : ?>
								<?php echo HTMLHelper::_('users.' . $field->type, $field->value); ?>
							<?php else : ?>
								<?php echo HTMLHelper::_('users.value', $field->value); ?>
							<?php endif; ?>
						</dd>
					<?php endif; ?>
				<?php endforeach; ?>
			</dl>
		</fieldset>
	<?php endif; ?>
<?php endforeach; ?>