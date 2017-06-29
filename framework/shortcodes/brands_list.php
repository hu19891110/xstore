<?php  if ( ! defined('ETHEME_FW')) exit('No direct script access allowed');

// **********************************************************************//
// ! Brands
// **********************************************************************//

function etheme_brands_list_shortcode($atts) {
    extract( shortcode_atts( array(
        'columns'           => '',
        'hide_a_z'          => false,
        'alignment'         => 'left',
        'capital_letter'    => false,
        'display_type'      => '',
        'brand_title'       => false,
        'brand_image'       => false,
        'brand_desc'        => false,
        'tooltip'           => false,
        'hide_empty'        => false,
        'show_product_counts' => false,
        'class'      => ''
    ), $atts ) );

    $args = array(
        'hide_empty' => $hide_empty
    );

    $product_brands = get_terms( 'brand', $args );

    $data='';
    foreach ( $product_brands as $brand ) {

        $firstLetter = strtoupper(substr($brand->name, 0, 1));

        $brands = (object)array(
            "id" => $brand->term_id,
            "name" => $brand->name,
            "desc" => $brand->description,
            "count" => $brand->count
        );
        $data[$firstLetter][] = $brands;
    }

    ob_start();
?>
    <div class="container brands-list">
        <?php if ($hide_a_z) { ?>
            <ul class="portfolio-filters">
                <li><a href="#" data-filter="*" class="filter-btn active">All</a></li>
                <?php foreach ($data as $letter => $value) { ?>
                    <li><a href="#" data-filter=".<?php echo $letter; ?>" class="filter-btn"><?php echo $letter; ?></a></li>
                <?php } ?>
            </ul>
    <?php }
        $portfolio = ($hide_a_z) ? 'portfolio' : ''; ?>
<div class="<?php echo $portfolio; ?> brand-list">
    <?php if ($hide_a_z) { ?>
        <div class="grid-sizer"></div>
    <?php } ?>
    <?php foreach ($data as $letter => $value) {
        $col = '';
        switch ($columns) {
            case 1:
                $col = 'col-md-12 col-sm-12 col-xs-12';
                break;
            case 2:
                $col = 'col-md-6 col-sm-6 col-xs-12';
                break;
            case 3:
                $col = 'col-md-4 col-sm-6 col-xs-12';
                break;
            case 4:
                $col = 'col-md-3 col-sm-3 col-xs-12';
                break;
            default:

        } ?>

        <div class="portfolio-item <?php echo $col;?> <?php echo $letter; ?>">
            <?php if ( $capital_letter ) { ?>
                <div class="firstLetter text-<?php echo $alignment; ?>"><?php echo $letter; ?></div>
            <?php }
            foreach ($value as $brand) { ?>
                <div class="work-item text-<?php echo $alignment; ?>">
                    <?php
                    $thumbnail_id = absint( get_woocommerce_term_meta( $brand->id, 'thumbnail_id', true ) );
                    $image = wp_get_attachment_image_url( $thumbnail_id, array(50,50) );
                    if ( $brand_image ) { ?>
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo $brand->name; ?>" class="brand-img">
                    <?php } ?>
                    <div class="vertical-align full">
                        <a href="<?php echo esc_url( get_term_link( $brand->id ) ); ?>" class="brand-desc">
                        <?php if ( $brand_title ) : ?>
                            <h4 class="title">
                                <?php
                                    echo $brand->name;
                                    if ( $show_product_counts ){ ?>
                                        <span class="colorGrey">
                                                <?php echo esc_html('('.$brand->count.')')?>
                                            </span>
                                    <?php } ?>
                                    <?php if ($tooltip) { ?>
                                        <div class="brand-tooltip">
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo $brand->name; ?>" class="brand-img-tooltip">
                                            <div class="sub-title colorGrey"><?php echo $brand->desc; ?></div>
                                        </div>
                                <?php } ?>
                            </h4>
                        <?php endif; ?>
                        </a>
                        <?php if ( $brand_desc ) { ?>
                            <div class="sub-title colorGrey"><?php echo $brand->desc; ?></div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
    </div>
<?php
    return ob_get_clean();
}

// **********************************************************************//
// ! Register New Element: scslug
// **********************************************************************//
add_action( 'init', 'etheme_register_etheme_brands_list');
if(!function_exists('etheme_register_etheme_brands_list')) {
    function etheme_register_etheme_brands_list() {
        if(!function_exists('vc_map') || ! etheme_get_option( 'enable_brands' )) return;


        $params = array(
            'name' => '[8theme] Brands List',
            'base' => 'etheme_brands_list',
//            'icon' => 'icon-wpb-etheme',
//            'icon' => ETHEME_CODE_IMAGES . 'vc/el-categories.png',
            'category' => 'Eight Theme',
            'params' => array_merge(array(
                array(
                    'type' => 'autocomplete',
                    'heading' => esc_html__( 'Brands', 'xstore' ),
                    'param_name' => 'ids',
                    'settings' => array(
                        'multiple' => true,
                        'sortable' => true,
                    ),
                    'save_always' => true,
                    'description' => esc_html__( 'List of product brands', 'xstore' ),
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Extra Class", 'xstore'),
                    "param_name" => "class"
                )
            ), etheme_get_brands_list_params()
            )
        );

        vc_map($params);
    }
}


