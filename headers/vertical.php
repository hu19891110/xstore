<?php 
    $ht = etheme_get_header_type();
    $color = etheme_get_header_color();

?>

<div class="header-wrapper header-<?php echo esc_attr( $ht ); ?> header-color-<?php echo esc_attr( $color ); ?>">
    <header class="header main-header header-bg-block">
        <div class="container-wrapper">

            <div class="header-logo"><?php etheme_logo(); ?></div>

            <div class="menu-wrapper"> 
			    <p class="hamburger-icon">
			        <span></span>
			    </p>
			    <?php etheme_get_main_menu(); ?>
			</div>

			 <div class="navbar-toggle">
                    <span class="sr-only"><?php esc_html_e('Menu', 'xstore'); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>

			<div class="navbar-header">
               	<?php if(etheme_woocommerce_installed() && current_theme_supports('woocommerce') && !etheme_get_option('just_catalog') && etheme_get_option('cart_widget')): ?>
                    <?php etheme_top_cart(); ?>
                <?php endif ;?>
            </div>

        </div>
    </header>
</div>