<?php
/*
 * Copyright (C) joomla-monster.com
 * Website: http://www.joomla-monster.com
 * Support: info@joomla-monster.com
 *
 * JM Social Icons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JM Social Icons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JM Social Icons. If not, see <http://www.gnu.org/licenses/>.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$count = 0;

?>

<div id="<?php echo $id; ?>" class="jmm-social <?php echo $theme_class . ' ' . $mod_class_suffix; ?>">
	<div class="jmm-social-in view-<?php echo $view; ?>">
				<?php

				if( $intro ) {
					echo '<div class="jmm-intro">' . $intro . '</div>';
				}

				echo '<ul class="jmm-list items-' . $elements . '">';
					foreach($output_data as $item) {

						$count++;

						$url = ( !empty($item->url) ) ? $item->url : false;
						$image = ( !empty($item->image_file) ) ? $item->image_file : false;
						$font = ( !empty($item->icon) ) ? $item->icon : false;
						$name = ( !empty($item->name) ) ? $item->name : false;
						$alt = ( !empty($item->name) ) ? $item->name : '';
						$title = ( !empty($alt) ) ? 'aria-label="' . $alt . '" title="' . $alt . '"' : '';

						$class = ( !empty($name) ) ? preg_replace('/\W+/','',strtolower(strip_tags($name))) : '';

						if( $url && ( $image || $font || $name ) ) {

							if( $image ) {
								$icon = '<span class="jmm-img"><img src="' . $image . '" alt="' . $alt . '"></span>';
							} elseif( $font ) {
								$icon = '<span class="jmm-ico ' . $font . '" aria-hidden="true"></span>';
							} else {
								$icon = '';
							}

							if( $name && $view == 2 ) { //icon + text
								$link = $icon . '<span class="jmm-name">' . $name . '</span>';
							} elseif( $name && $view == 3 ) { //text
								$link = '<span class="jmm-name">' . $name . '</span>';
							} else { //icons
								$link = $icon;
							}

							echo '<li class="jmm-item item-' . $count . ' ' . $class . '"><a class="jmm-link" href="' . $url . '" target="' . $target . '" ' . $title . '>' . $link . '</a></li>';

						}

					}
				echo '</ul>';

				?>
	</div>
</div>
