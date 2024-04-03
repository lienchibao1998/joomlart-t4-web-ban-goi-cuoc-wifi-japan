<?php
defined('_JEXEC') or die;
/**
 * summary
 */
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

class JACustomField
{
    public static function getField($fields,$fieldname)
    {
    	foreach ($fields as $field) {
    		if ($field->value && $field->name == $fieldname){
				return FieldsHelper::render('com_content.article', 'field.render', array('field' => $field));
			}
    	}
    }
    public static function getList($fields){
    	foreach($fields as $field) {
	        $itemCustomFields[$field->name]['title'] = $field->title;	
	        $itemCustomFields[$field->name]['value'] = $field->rawvalue;
            if($field->type == 'url' && empty($field->rawvalue)) $field->rawvalue = '#'; 
            $itemCustomFields[$field->name]['rawvalue'] = $field->rawvalue;
			if ($field->value){
				$itemCustomFields[$field->name]['value'] = $field->value;	
			}
		}
		return $itemCustomFields;
    }
    public static function getAllTags($items)
    {
    	$tags = [];
    	foreach ($items as $item) {
    		if(!$item->tags->itemTags){
                continue;
            }

			foreach ($item->tags->itemTags as $tag) {
                if (isset($tags[$tag->id])) {
                    continue;
                }

	    		$tags[$tag->id]= $tag;
	    	}
    	}

    	return $tags;
    }

    public static function getTag($tags, $module_id)
    {
    	if(empty($tags)) return "";
    	$return = "";

    	foreach ($tags as $tag) {
    		$return .= " mod-" . $module_id . '-' . $tag->id;
    	}

    	return $return;
    }

    public static function itemSort($arr){
        foreach ($arr as $key => $val) {
            $featured[$key] = $val->featured;
        }
        array_multisort($featured, SORT_DESC, $arr);
        return $arr;
    }
}


?>