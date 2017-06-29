<?php
/**
 * The template for displaying search forms
 *
 */
$ajax['enable'] = etheme_get_option( 'search_ajax' );
$ajax['taxonomy'] = $ajax['name'] = 'product_cat';
$ajax['product'] = etheme_get_option( 'search_ajax_product' );
$ajax['post'] = etheme_get_option( 'search_ajax_post' );
$class 	= '';

if( $ajax['enable'] ) {
	$class .= 'ajax-search-form';
	if ( $ajax['post'] && $ajax['product'] ) {
		$class .= ' all-results-on';
	} elseif ( $ajax['product'] ) {
		$class .= ' product-results-on';
	} elseif ( $ajax['post'] ) {
		$class .= ' post-results-on';
		$ajax['taxonomy'] = 'category';
		$ajax['name'] = 'cat';
	}
}
?>

<?php if(class_exists('Woocommerce')) : ?>
	<form action="<?php echo esc_url( home_url( '/' ) ); ?>" id="searchform" class="<?php echo esc_attr($class); ?>" method="get">
		<div class="input-row">
			<input type="text" value="" placeholder="<?php esc_attr_e( 'Type here...', 'xstore' ); ?>" autocomplete="off" class="form-control" name="s" id="s" />
			<input type="hidden" name="post_type" value="<?php echo ( $ajax['product'] ) ? 'product': 'post' ; ?>" />
			<?php if ( defined( 'ICL_LANGUAGE_CODE' ) && ! defined( 'LOCO_LANG_DIR' ) ) : ?>
				<input type="hidden" name="lang" value="<?php echo ICL_LANGUAGE_CODE; ?>"/>
			<?php endif ?>
			<?php wp_dropdown_categories(array( 'show_option_all' => esc_html__( 'All categories', 'xstore' ) ,'taxonomy' => $ajax['taxonomy'], 'hierarchical' => true, 'name' => $ajax['name'], 'orderby' => 'name', 'value_field' => 'slug')) ?>
			<button type="submit" class="btn filled"><?php esc_html_e( 'Search', 'xstore' ); ?><i class="fa fa-search"></i></button>
		</div>
		<?php if($ajax['enable']): ?>
			<div class="ajax-results-wrapper"><div class="ajax-results"></div></div>
		<?php endif ?>
	</form>
<?php else: ?>
	<?php get_template_part('searchform'); ?>
<?php endif ?>