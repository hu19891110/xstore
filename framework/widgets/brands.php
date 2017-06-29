<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************//
// ! Products Widget
// **********************************************************************//
if( ! class_exists( 'WC_Widget' ) ) return;
class ETheme_Brands_Widget extends WC_Widget {
    /**
     * Current Brand.
     *
     * @var bool
     */
     public $current_cat;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'sidebar-widget etheme etheme_widget_brands';
        $this->widget_description = __( 'A list or dropdown of product brands.', 'xstore' );
        $this->widget_id          = 'etheme_widget_brands';
        $this->widget_name        = __( '8themes brand list', 'xstore' );
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => esc_html__( 'Filter by brand', 'xstore' ),
                'label' => __( 'Title', 'xstore' )
            ),
            'displayType' => array(
                'type'  => 'select',
                'std'   => 'name',
                'label' => __( 'Display type:', 'xstore' ),
                'options' => array(
                    'name' => __( 'Name', 'xstore' ),
                    'image'  => __( 'Image', 'xstore' )
                )
            ),
            'dropdown' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Show as dropdown (Only for Display type: Name)', 'xstore' )
            ),
            'count' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Show product counts', 'xstore' )
            ),
            'hide_empty' => array(
                'type'  => 'checkbox',
                'std'   => 0,
                'label' => __( 'Hide empty brands', 'xstore' )
            )
        );

        parent::__construct();
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        $count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
        $title              = isset( $instance['title'] ) ? $instance['title'] : $this->settings['title']['std'];
        $dropdown           = isset( $instance['dropdown'] ) ? $instance['dropdown'] : $this->settings['dropdown']['std'];
        $displayType        = isset( $instance['displayType'] ) ? $instance['displayType'] : $this->settings['displayType']['std'];
        $hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];

        // Setup Current Category
        $this->current_cat   = false;
        $hide_empty = ($hide_empty == 1) ? true : false;
        $args = array(
            'taxonomy' => 'brand',
            'hide_empty' => $hide_empty,
        );
        $terms = get_terms($args);

        // Dropdown
        echo '<div class="sidebar-widget etheme etheme_widget_brands">
                <h4 class="widget-title"><span>'.$title.'</span></h4>
                ';

        if ( $dropdown ) { ?>
                <select name="product_brand" class="dropdown_product_brand">
                    <option value="" selected="selected">Select a brand</option>
                    <?php foreach ($terms as $brand) {

                        $stock = $brand->count;

                        if ( $hide_empty && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
                            $stock  = etheme_stock_taxonomy( $brand->term_id, 'brand' );
                            if ( $stock < 1 ) continue;
                        }

                        $countProd = ($count == 1) ? "({$stock})" : '';
                        ?>
                        <option class="level-0" value="<?php echo esc_html($brand->name); ?>"><?php echo esc_html($brand->name .' '. $countProd); ?></option>
                    <?php } ?>
                </select>
            <?php
            wc_enqueue_js( "
				jQuery( '.dropdown_product_brand' ).change( function() {
					if ( jQuery(this).val() != '' ) {
						var this_page = '';
						var home_url  = '" . esc_js( home_url( '/' ) ) . "';
						if ( home_url.indexOf( '?' ) > 0 ) {
							this_page = home_url + '&brand=' + jQuery(this).val();
						} else {
							this_page = home_url + '?brand=' + jQuery(this).val();
						}
						location.href = this_page;
					}
				});
			" );
        // List
        } else {
            echo '<ul>';
            if(! is_wp_error( $terms ) && count($terms) > 0 ) {
                foreach ( $terms as $brand ) {

                    $stock = $brand->count;

                    if ( $hide_empty && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

                        $stock  = etheme_stock_taxonomy( $brand->term_id, 'brand' );
                        if ( $stock < 1 ) continue;
                    }

                    $thumbnail_id = absint(get_woocommerce_term_meta($brand->term_id, 'thumbnail_id', true)); ?>
                    <li class="cat-item"> <?php
                        $countProd = ($count == 1) ? "({$stock})" : '';
                        if ( $displayType == 'name' ) { ?>
                            <a href="<?php echo get_term_link($brand); ?>">
                                <?php echo esc_html($brand->name); ?>
                                <span class="count"><?php echo esc_html($countProd); ?></span>
                            </a>
                        <?php } elseif( $displayType == 'image' ) {
                            $brandImg = wp_get_attachment_image($thumbnail_id, array(100,50) );
                            if (!empty( $brandImg )) { ?>
                                <a href="<?php echo get_term_link($brand); ?>">
                                    <?php echo $brandImg; ?>
                                    <span class="count"><?php echo esc_html($countProd); ?></span>
                                </a>
                            <?php }
                        } ?>
                    </li>
                <?php }
            }
            echo '</ul>';
        }
        echo '</div>';
    }
}
