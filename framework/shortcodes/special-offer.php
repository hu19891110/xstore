<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Best offer
// **********************************************************************// 

function etheme_offer_shortcode($atts, $content) {
    global $woocommerce_loop;

    if ( ! class_exists('Woocommerce') ) return false;

    $output = '';

    $atts = shortcode_atts(array(
        'post_type'  => 'ids',
        'include'  => '',
        'custom_query'  => '',
        'taxonomies'  => '',
        'items_per_page'  => 10,
        'columns' => 3,
        'hover' => '',
        'banner_double' => '',
        'orderby'  => 'date',
        'order'  => 'DESC',
        'meta_key'  => '',
        'exclude'  => '',
        'class'  => '',
        'product_view' => '',
        'product_view_color' => '',
        'css' => '',
        'img_size' => 'medium',
        'dis_type' => '',
        // ! slider args
        'slider_autoplay' => false,
        'slider_speed' => 10000,
        'pagination_type' => 'hide',
        'default_color' => '#e6e6e6',
        'active_color' => '#b3a089',
        'hide_fo' => '',
        'hide_buttons' => false,
    ), $atts);



    extract($atts);

    if ( ! in_array( $img_size, array(  'thumbnail', 'medium', 'large', 'full' ) ) ) {
      $size = explode( 'x', $img_size );
    } else {
      $size = $img_size;
    }

    $paged = (get_query_var('page')) ? get_query_var('page') : 1;

    $args = array(
      'post_type' => 'product',
      'status' => 'published',
      'paged' => $paged,  
      'posts_per_page' => $items_per_page
    );

    if($post_type == 'ids' && $include != '') {
      $args['post__in'] = explode(',', $include);
      $orderby = 'post__in';
    }

    if(!empty( $exclude ) ) {
      $args['post__not_in'] = explode(',', $exclude);
    }


    if(!empty( $taxonomies )) {
      $terms = get_terms( array('product_cat', 'product_tag'), array(
        'orderby' => 'name',
        'include' => $taxonomies
      ));

      if( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $args['tax_query'] = array('relation' => 'OR');
        foreach ($terms as $key => $term) {
          $args['tax_query'][] = array(
                'taxonomy' => $term->taxonomy,        //(string) - Taxonomy.
                'field' => 'slug',                    //(string) - Select taxonomy term by ('id' or 'slug')
                'terms' => array( $term->slug ),      //(int/string/array) - Taxonomy term(s).
                'include_children' => true,           //(bool) - Whether or not to include children for hierarchical taxonomies. Defaults to true.
                'operator' => 'IN'  
          );
        }
      }
    }

    if(!empty( $order )) {
      $args['order'] = $order;
    }

    if(!empty( $meta_key )) {
      $args['meta_key'] = $meta_key;
    }

    if(!empty( $orderby )) {
      $args['orderby'] = $orderby;
    }

    $output = '';

    $box_id = rand( 1000,10000 );

    ob_start();

    $products = new WP_Query( $args );

    $class = $title_output = $images_class = '';

    $shop_url = get_permalink(woocommerce_get_page_id('shop'));

    $woocommerce_loop['columns'] = $columns;
    //$woocommerce_loop['size'] = 'shop_catalog_alt';

    if( ! empty($css) && function_exists( 'vc_shortcode_custom_css_class' )) {
        $images_class = vc_shortcode_custom_css_class( $css );
        $images_style = explode('{', $css);
        $images_style = '[data-class="' . $images_class . '"] .product-content-image img {' . $images_style[1];
        $css = '<style>' . $images_style . '</style>';
    }

    if( $banner_double ) {
      $columns = $columns / 2;
    }

    if ( $products->have_posts() ) : ?>
      <div class="et-offer slider-<?php echo $box_id;?>" data-class="<?php echo esc_attr($images_class); ?>">
        <?php $i=0; while ( $products->have_posts() ) : $products->the_post(); ?>
            <div <?php post_class(); ?>>
                <div class="content-product">
                  <?php etheme_loader(); ?>
                  <?php
                    /**
                     * woocommerce_before_shop_loop_item hook.
                     *
                     * @hooked woocommerce_template_loop_product_link_open - 10
                     */
                    do_action( 'woocommerce_before_shop_loop_item' );

                    /**
                     * woocommerce_before_shop_loop_item_title hook.
                     *
                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                     */
                    do_action( 'woocommerce_before_shop_loop_item_title' );
                  ?>

                    <?php
                        etheme_product_cats();
                    ?>  
                    <p class="product-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </p>

                    <?php woocommerce_template_loop_rating(); ?>

                    <div class="product-image-wrapper hover-effect-<?php echo esc_attr( $hover ); ?>">

                      <?php etheme_product_availability(); ?>

                      <a class="product-content-image" href="<?php the_permalink(); ?>" data-images="<?php echo etheme_get_image_list( $size ); ?>">
                        <?php if( $hover == 'swap' ) etheme_get_second_image( $size ); ?>
                        <?php echo etheme_get_image( get_post_thumbnail_id(), $size ); ?>
                      </a>

                      <footer class="footer-product">
                          <?php if (etheme_get_option('quick_view')): ?>
                              <span class="show-quickly" data-prodid="<?php echo get_the_ID();?>"><?php esc_html_e('Quick View', 'xstore') ?></span>
                          <?php endif ?>
                          <?php //if (etheme_get_option('product_page_addtocart')) {
                              do_action( 'woocommerce_after_shop_loop_item' );
                          //}?>
                          <?php echo etheme_wishlist_btn(__('Wishlist', 'xstore')); ?>
                      </footer>
                    </div>

                    <div class="product-details">

                        <?php woocommerce_template_loop_price(); ?>

                        <?php etheme_product_countdown(); ?>
                    </div>
              </div><!-- .content-product -->
            </div>
        <?php endwhile; // end of the loop. ?>
        <?php
          echo $css;
          unset($woocommerce_loop['columns']); 
          unset($woocommerce_loop['isotope']); 
          unset($woocommerce_loop['size']); 
          unset($woocommerce_loop['product_view']); 
          unset($woocommerce_loop['product_view_color']); 
        ?>
      </div>

      <?php if ( $dis_type == 'slider' ): ?>
          <script type="text/javascript">
              (function() {
                  var options = {
                      items: 1,
                      autoPlay: <?php echo ( ( $slider_autoplay == "yes" ) ? $slider_speed : "false" ); ?>,
                      pagination: <?php echo ( ( $pagination_type == "hide" ) ? "false" : "true")  ?>,
                      navigation: <?php echo ( ( $hide_buttons == "yes" ) ? "false" : "true" ); ?>,
                      navigationText:false,
                      rewindNav: <?php echo ( ( $slider_autoplay == "yes") ? "true" : "false" ); ?>,
                      itemsCustom: [[0, 1], [479, 1], [619, 1], [768, 1],  [1200, 1], [1600, 1]]
                  };

                  jQuery( ".slider-<?php echo $box_id ?>" ).owlCarousel( options );

                  var owl = jQuery( ".slider-<?php echo $box_id ?>" ).data( "owlCarousel" );
                  
                  jQuery( window ).bind( "vc_js", function() {
                      owl.reinit( options );
                      jQuery( ".slider-<?php echo $box_id; ?> .owl-pagination" ).addClass( "pagination-type-<?php echo $pagination_type; ?> hide-for-<?php echo $hide_fo ?>" );
                  } );
              })();
          </script>
        
          <?php if ( $pagination_type != 'hide' && $default_color != '#e6e6e6' && $active_color !='#b3a089' ) : ?>
              <style>
                  .slider-<?php echo $box_id; ?> .owl-pagination .owl-page{
                      background-color: <?php echo $default_color; ?>;
                  }
                  .slider-<?php echo $box_id; ?>.owl-carousel .owl-pagination .owl-page:hover{
                      background-color: <?php echo $active_color; ?>;
                  }
                  .slider-<?php echo $box_id; ?> .owl-pagination .owl-page.active{
                      background-color: <?php echo $active_color; ?>;
                  }
              </style>
          <?php endif; ?>
      <?php endif; ?>

    <?php endif;

    wp_reset_postdata();

    $output = ob_get_clean();
      
    return $output;
}

// **********************************************************************// 
// ! Register New Element: Best offer
// **********************************************************************//
add_action( 'init', 'etheme_register_offer');
if(!function_exists('etheme_register_offer')) {
	function etheme_register_offer() {
		if(!function_exists('vc_map')) return;

      add_filter( 'vc_autocomplete_et_offer_include_callback',
        'vc_include_field_search', 10, 1 ); // Get suggestion(find). Must return an array
      add_filter( 'vc_autocomplete_et_offer_include_render',
        'vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)

      // Narrow data taxonomies
      add_filter( 'vc_autocomplete_et_offer_taxonomies_callback',
        'vc_autocomplete_taxonomies_field_search', 10, 1 );
      add_filter( 'vc_autocomplete_et_offer_taxonomies_render',
        'vc_autocomplete_taxonomies_field_render', 10, 1 );

      // Narrow data taxonomies for exclude_filter
      add_filter( 'vc_autocomplete_et_offer_exclude_filter_callback',
        'vc_autocomplete_taxonomies_field_search', 10, 1 );
      add_filter( 'vc_autocomplete_et_offer_exclude_filter_render',
        'vc_autocomplete_taxonomies_field_render', 10, 1 );

      add_filter( 'vc_autocomplete_et_offer_exclude_callback',
        'vc_exclude_field_search', 10, 1 ); // Get suggestion(find). Must return an array
      add_filter( 'vc_autocomplete_et_offer_exclude_render',
    'vc_exclude_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)


      $post_types_list = array();
      $post_types_list[] = array( 'product', esc_html__( 'Product', 'xstore' ) );
      //$post_types_list[] = array( 'custom', esc_html__( 'Custom query', 'xstore' ) );
      $post_types_list[] = array( 'ids', esc_html__( 'List of IDs', 'xstore' ) );

	    $params = array(
	      'name' => '[8THEME] Best offer',
	      'base' => 'et_offer',
	      'icon' => 'icon-wpb-etheme',
	      'category' => 'Eight Theme',
        'content_element' => true,
        'icon' => ETHEME_CODE_IMAGES . 'vc/el-lookbook.png',
	      'params' => array(
          array(
            'type' => 'autocomplete',
            'heading' => esc_html__( 'Product', 'xstore' ),
            'param_name' => 'include',
            'settings' => array(
              'multiple' => false,
              'sortable' => true,
              'groups' => true,
            ),
          ),
          array(
            'type' => 'textfield',
            'heading' => esc_html__('Image size', 'xstore' ),
            'param_name' => 'img_size',
            'description' => esc_html__('Enter image size. Example in pixels: 200x100 (Width x Height).', 'xstore'),
          ),
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Display type', 'xstore' ),
            'param_name' => 'dis_type',
            'value' => array(
              __( 'Default', 'xstore' ) => 'default',
              __( 'Slider', 'xstore' ) => 'slider',
            ),
          ),
          array(
            "type" => "textfield",
            "heading" => esc_html__("Slider speed", 'xstore'),
            "param_name" => "slider_speed",
            'dependency' => array(
              'element' => 'dis_type',
              'value' => array( 'slider' ),
            ),
            "group" => esc_html__('Slider settings', 'xstore')
          ),
          array(
            "type" => "checkbox",
            "heading" => esc_html__("Slider autoplay", 'xstore'),
            "param_name" => "slider_autoplay",
            'dependency' => array(
              'element' => 'dis_type',
              'value' => array( 'slider' ),
            ),
            "group" => esc_html__('Slider settings', 'xstore'),
            'value' => array( esc_html__( 'Yes, please', 'xstore' ) => 'yes' )
          ),
          array(
            "type" => "checkbox",
            "heading" => esc_html__("Hide prev/next buttons", 'xstore'),
            "param_name" => "hide_buttons",
            'dependency' => array(
              'element' => 'dis_type',
              'value' => array( 'slider' ),
            ),
            "group" => esc_html__('Slider settings', 'xstore'),
            'value' => array( esc_html__( 'Yes, please', 'xstore' ) => 'yes' )
          ),
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Pagination type', 'xstore' ),
            'param_name' => 'pagination_type',
            'dependency' => array(
              'element' => 'dis_type',
              'value' => array( 'slider' ),
            ),
            'group' => esc_html__('Slider settings', 'xstore'),
            'value' => array(
              __( 'Hide', 'xstore' ) => 'hide',
              __( 'Bullets', 'xstore' ) => 'bullets',
              __( 'Lines', 'xstore' ) => 'lines',
            ),
          ),
          array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Hide pagination only for', 'xstore' ),
            'param_name' => 'hide_fo',
            'dependency' => array(
              'element' => 'pagination_type',
              'value' => array( 'bullets', 'lines' ),
            ),
            'group' => esc_html__('Slider settings', 'xstore'),
            'value' => array(
              '' => '',
              __( 'Mobile', 'xstore' ) => 'mobile',
              __( 'Desktop', 'xstore' ) => 'desktop',
            ),
          ),
          array(
            "type" => "colorpicker",
            "heading" => __( "Pagination default color", "xstore" ),
            "param_name" => "default_color",
            'dependency' => array(
              'element' => 'pagination_type',
              'value' => array( 'bullets', 'lines' ),
            ),
            "group" => esc_html__('Slider settings', 'xstore'),
            "value" => '#e6e6e6',
          ),
          array(
            "type" => "colorpicker",
            "heading" => __( "Pagination active color", "xstore" ),
            "param_name" => "active_color",
            'dependency' => array(
              'element' => 'pagination_type',
              'value' => array( 'bullets', 'lines' ),
            ),
            "group" => esc_html__('Slider settings', 'xstore'),
            "value" => '#b3a089',
          ),
	      ),   
	    );  
	    vc_map($params);
	}
}