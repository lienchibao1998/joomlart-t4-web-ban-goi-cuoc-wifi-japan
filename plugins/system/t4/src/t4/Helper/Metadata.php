<?php
namespace T4\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry as JRegistry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

class Metadata {
	public static $metaNr = 0;
	public static function renderTag($name, $content, $type = 1)
	{
		$document 				= Factory::getDocument();
		// Encoded html tags can still be rendered, decode and strip tags first.
		$value                  = trim(strip_tags(html_entity_decode($content)));

		// OG tag
		if ($type == 1) {
			$document->setMetadata(htmlspecialchars($name, ENT_COMPAT, 'UTF-8'), htmlspecialchars($value, ENT_COMPAT, 'UTF-8'));
		} else {
			$attributes = '';
			if ($name == 'og:image') {
				$attributes = ' itemprop="image"';
			}
			$document->addCustomTag('<meta property="'.htmlspecialchars($name, ENT_COMPAT, 'UTF-8').'"'.$attributes.' content="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />');
		}
	}
	public static function renderOpenGraph($context, $item, $params)
	{
		if(Factory::getApplication()->isClient('administrator')) return;
		if(!$params) return;

		// load global data from file
		$global = new JRegistry();
		$global->loadString(Path::getFileContent('etc/global.json'));
		if(!$global->get('system_opengraph',"")) return;
		
		$app = Factory::getApplication();
		$doc = Factory::getDocument();
		$config = Factory::getConfig();
		$option = $app->input->getCmd('option','');
		if(!$params instanceof JRegistry){
			$params = new JRegistry($params);
		}
		$uri = Uri::getInstance();
		$og_url = $uri->toString();

		$og_title = $params->get('og_title', '');
		$og_desc = $params->get('og_desc','');
		$og_image = $params->get('og_image','');
		$views = $app->input->getCmd('view','');
		if($views == 'tag') return;
		if(self::$metaNr > 0) return;

		if(in_array('com_content',explode('.', $context))){
			if($views == 'category' && self::$metaNr == 0){
				if(!$og_title) $og_title = $doc->title;
				if(!$og_desc){
					$og_desc = HTMLHelper::_('string.truncate', trim(strip_tags($item->metadesc ?: (!empty($item->description) ? $item->description : ""))), 220);
				}
				if(!$og_image){
					$og_image = $params->get('image');
				}
				self::$metaNr = 1;
			}elseif($views == 'featured' && self::$metaNr == 0){
				if(!$og_title) $og_title = $doc->title;
				if(!$og_desc){
					$og_desc = $doc->description;
				}
				self::$metaNr = 1;
			}else{
				if(!$og_title) $og_title = $item->title ?: $doc->title;
				if(!$og_desc){
					$og_desc = HTMLHelper::_('string.truncate', trim(strip_tags($item->metadesc ?: ($item->introtext ?: ($item->fulltext ?: '')))), 220);
				}
				if(!$og_image){
					$images  = json_decode($item->images ?: '{}');
					$og_image = $images->image_intro ?: ($images->image_fulltext ?: "");
					if(!$og_image) $og_image = self::findImage($item->text ?: $item->introtext." ".$item->fulltext);
				}

			}
		}elseif ($context === 'com_contact.contact') {
			if(!$og_title) $og_title = $item->name;
			if(!$og_desc){
				$og_desc = HTMLHelper::_('string.truncate', trim(strip_tags($item->metadesc ?: ($item->misc ?: ''))), 220);
			}
			if(!$og_image){
				$og_image = $item->image;
			}
		}else{
			if(!$og_title && !$og_desc) return;
		}
		$img_url = self::cleanImageURL($og_image);
		//check more space for each word
		if($og_desc){
			$og_desc_arr = explode("  ",$og_desc);
			$og_desc_arr = array_filter(array_map(
				function($a){ 
					if(!is_null(str_replace("\r\n","",$a))){
						return str_replace("\r\n","",$a);
					};
				},
			$og_desc_arr));
			$og_desc = implode(" ",$og_desc_arr);
		}else{
			$og_desc = $doc->description ?: '';
		}
		
		self::renderTag('title', $og_title);
		self::renderTag('description', trim($og_desc));	
		self::renderTag('og:title', $og_title, 2);
		self::renderTag('og:description', trim($og_desc), 2);
		if($img_url->url) self::renderTag('og:image', self::fullImageURL($img_url->url), 2);
		self::renderTag('og:url', $og_url, 2);
		self::renderTag('twitter:title', $og_title, 2);
		self::renderTag('twitter:description', trim($og_desc), 2);
		if($img_url->url) self::renderTag('twitter:image', self::fullImageURL($img_url->url), 2);
		self::renderTag('twitter:url', $og_url, 2);

	}
	public static function findImage($content)
	{
		if(gettype($content) !== 'string') return "";
		$img_url = '';
		preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $src);
		if (isset($src[1]) && $src[1] != '') {
			$img_url = $src[1];
		}
		return $img_url;
	}
	public static function fullImageURL($image)
	{
		$linkImg 				= $image;

		$absU = 0;
		// Test if this link is absolute http:// then do not change it
		$pos1 			= strpos($image, 'http://');
		if ($pos1 === false) {
		} else {
			$absU = 1;
		}
		// Test if this link is absolute https:// then do not change it
		$pos2 			= strpos($image, 'https://');
		if ($pos2 === false) {
		} else {
			$absU = 1;
		}


		if ($absU == 1) {
			$linkImg = $image;
		} else {
			$linkImg = Uri::base(false).$image;
			if ($image[0] == '/') {
				$myURI = new \Joomla\Uri\Uri(Uri::base(false));
				$myURI->setPath($image);
				$linkImg = $myURI->toString();
			}
		}
		return $linkImg;
	}
	/**
   * Gets a URL, cleans the Joomla specific params and returns an object
   *
   * @param    string  $url  The relative or absolute URL to use for the src attribute.
   *
   * @return   object
   * @example  {
   *             url: 'string',
   *             attributes: [
   *               width:  integer,
   *               height: integer,
   *             ]
   *           }
   *
   * @since    4.0.0
   */
  public static function cleanImageURL($url)
  {
    $obj = new \stdClass;

    $obj->attributes = [
      'width'  => 0,
      'height' => 0,
    ];

    if ($url && !strpos($url, '?'))
    {
      $obj->url = $url;

      return $obj;
    }

    $mediaUri = new Uri($url);

    // Old image URL format
    if ($mediaUri->hasVar('joomla_image_height'))
    {
      $height = (int) $mediaUri->getVar('joomla_image_height');
      $width  = (int) $mediaUri->getVar('joomla_image_width');

      $mediaUri->delVar('joomla_image_height');
      $mediaUri->delVar('joomla_image_width');
    }
    else
    {
      // New Image URL format
      $fragmentUri = new Uri($mediaUri->getFragment());
      $width       = (int) $fragmentUri->getVar('width', 0);
      $height      = (int) $fragmentUri->getVar('height', 0);
    }

    if ($width > 0)
    {
      $obj->attributes['width'] = $width;
    }

    if ($height > 0)
    {
      $obj->attributes['height'] = $height;
    }

    $mediaUri->setFragment('');
    $obj->url = $mediaUri->toString();

    return $obj;
  }
}
