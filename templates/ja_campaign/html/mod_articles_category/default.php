<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

require_once __DIR__ . '/jcustomField.php';
$tags = JACustomField::getAllTags($list);
$doc = Factory::getDocument();
?>
<a name="all-offers"></a>
<div id="filters" class="tag-filter">
    <button class="button is-checked" data-filter="*">All</button>
    <?php foreach ($tags as $tag) : ?>
        <button class="button" data-filter=".mod-<?php echo $module->id ?>-<?php echo $tag->id ?>">
            <?php echo $tag->title ?>
        </button>
    <?php endforeach; ?>
</div>

<ul class="offer-list mod-list grid align-items-stretch">
    <?php foreach ($list as $item) : ?>

        <?php $jcFields = FieldsHelper::getFields('com_content.article',  $item, True); ?>
        <?php $CFieldList =  JACustomField::getList($jcFields); ?>

        <li class="grid-item item<?php echo JACustomField::getTag($item->tags->itemTags, $module->id); ?>">
            <div class="offer-inner">
                <div class="item-media">
                    <?php echo JACustomField::getField($jcFields, 'offer-image'); ?>
                </div>

                <?php if ($params->get('link_titles') == 1) : ?>
                    <h3 class="item-title">
                        <a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>">
                            <?php echo $item->title; ?>
                        </a>
                    </h3>
                <?php else : ?>
                    <?php echo $item->title; ?>
                <?php endif; ?>

                <div class="item-ct">

                    <?php if ($CFieldList['offer-details'] && $CFieldList['offer-details']['value']) : ?>
                        <div class="offer-details">
                            <!-- <span class="label"><?php echo $CFieldList['offer-details']['title']; ?></span> -->
                            <span class="value"><?php echo $CFieldList['offer-details']['value']; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (($CFieldList['coupon-code'] && $CFieldList['coupon-code']['value']) || !empty($result)) : ?>
                        <div class="offer-coupon">
                            <?php if ($CFieldList['coupon-code'] && $CFieldList['coupon-code']['value']) : ?>
                                <span class="label"><?php echo $CFieldList['coupon-code']['title']; ?>:</span>
                                <span class="value"><?php echo $CFieldList['coupon-code']['value']; ?></span>
                            <?php endif; ?>
                            <?php if (!empty($CFieldList['offer-link']['rawvalue'])) : ?>
                                <a class="btn-link btn-get-offer" href="<?php echo $CFieldList['offer-link']['rawvalue']; ?>" title="<?php echo $item->title; ?>" target="_blank">Get offer <i class="fa fa-chevron-right"></i></a>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>

                    <?php if ($item->displayCategoryTitle) : ?>
                        <span class="mod-articles-category-category">
                            (<?php echo $item->displayCategoryTitle; ?>)
                        </span>
                    <?php endif; ?>

                    <?php if ($CFieldList['brand-short-intro']['value']) : ?>
                        <div class="offer-intro">
                            <span class="value"><?php echo $CFieldList['brand-short-intro']['value']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="item-footer">
                    <?php if ($params->get('show_readmore')) : ?>
                        <div class="mod-articles-category-readmore">
                            <a class="btn <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
                                <?php if ($item->params->get('access-view') == false) : ?>
                                    <?php echo Text::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
                                <?php elseif ($readmore = $item->alternative_readmore) : ?>
                                    <?php echo $readmore; ?>
                                    <?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
                                <?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
                                    <?php echo Text::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
                                <?php else : ?>
                                    <?php echo Text::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
                                    <?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </li>
    <?php endforeach; ?>
</ul>


<script defer type="text/javascript">
    jQuery(document).ready(function($) {
        // init Isotope
        var $grid = $('.grid');
        window.setTimeout(function() {
            var $maxH = 0;
            $('.offer-list li').each(function() {
                var $itemH = $(this).outerHeight(true);
                if ($itemH > $maxH) {
                    $maxH = $itemH;
                }
            });

            if ((window.innerWidth >= 768)) {
                $('.offer-list li').css({
                    'height': $maxH
                });
            }
            $grid.isotope({
                itemSelector: '.item',
                layoutMode: 'fitRows',
                filter: "*"
            });
        }, 0.1);

        // bind filter button click
        $filters = $('#filters').on('click', 'button', function() {
            var $this = $(this);
            var filterValue;
            if ($this.is('.is-checked')) {
                // uncheck
                filterValue = '*';
            } else {
                filterValue = $this.attr('data-filter');
                $filters.find('.is-checked').removeClass('is-checked');
            }
            $this.toggleClass('is-checked');

            $grid.isotope({
                filter: filterValue
            });
        });
    });
</script>