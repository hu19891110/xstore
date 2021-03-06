<?php 
    global $layout, $class, $image_class, $infor_class;
?>

<div class="col-md-3 product-summary-fixed">
    <div class="fixed-product-block">
        <div class="fixed-content">
            <?php if(!etheme_get_option('product_name_signle')):  ?>
                <h4 class="title"><?php esc_html_e('Product Information', 'xstore'); ?></h4>
            <?php endif;
                etheme_product_cats();
                woocommerce_template_single_title();
                woocommerce_template_single_rating();
                echo '<hr class="divider short">';
                woocommerce_template_single_excerpt();
                etheme_size_guide();
                if(etheme_get_option('share_icons')): ?>
                    <div class="product-share">
                        <?php echo do_shortcode('[share title="'.__('Share Social', 'xstore').'" text="'.get_the_title().'"]'); ?>
                    </div>
                <?php endif;
             ?>
         </div>
     </div>
</div>

<div class="<?php echo esc_attr( $image_class ); ?> product-images">
    <?php
        /**
         * woocommerce_before_single_product_summary hook
         *
         * @hooked woocommerce_show_product_sale_flash - 10
         * @hooked woocommerce_show_product_images - 20
         */
        do_action( 'woocommerce_before_single_product_summary' );
    ?>
</div><!-- Product images/ END -->

<div class="<?php echo esc_attr( $infor_class ); ?> product-information">
    <div class="product-information-inner fixed-product-block">
        <div class="fixed-content">
            <?php
                /**
                 * woocommerce_single_product_summary hook
                 *
                 * @hooked woocommerce_template_single_title - 5 
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 */
                do_action( 'woocommerce_single_product_summary' );
            ?>
           
            <?php if(etheme_get_option('share_icons') && $layout != 'fixed'): ?>
                <div class="product-share">
                    <?php echo do_shortcode('[share title="'.__('Share Social', 'xstore').'" text="'.get_the_title().'"]'); ?>
                </div>
            <?php endif; ?>

            <?php if(etheme_get_option('product_posts_links')): ?>
                <?php etheme_project_links(array()); ?>
            <?php endif; ?>
        </div>
    </div>
</div><!-- Product information/ END -->