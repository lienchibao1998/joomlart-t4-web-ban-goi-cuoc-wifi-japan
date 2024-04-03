<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2005 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\HTML;

\defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Uri\Uri;

// Make alias of original FileLayout
\T4\Helper\Joomla::makeAlias(JPATH_LIBRARIES . '/src/HTML/HTMLHelper.php', 'HTMLHelper', '_HTMLHelper');


/**
 * Base class for a Joomla View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  2.5.5
 */
abstract class HTMLHelper extends _HTMLHelper
{
  
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

    if (!strpos($url, '?'))
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