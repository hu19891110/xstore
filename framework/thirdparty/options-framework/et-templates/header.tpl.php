<?php
	/**
	 * The template for the panel header area.
	 *
	 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
	 *
	 * @author 	Redux Framework
	 * @package 	ReduxFramework/Templates
     * @version:    3.5.4.18
	 */

$tip_title  = esc_html__('Developer Mode Enabled', 'xstore');

if ($this->parent->dev_mode_forced) {
    $is_debug       = false;
    $is_localhost   = false;
    
    $debug_bit = '';
    if (Redux_Helpers::isWpDebug ()) {
        $is_debug = true;
        $debug_bit = esc_html__('WP_DEBUG is enabled', 'xstore');
    }
    
    $localhost_bit = '';
    if (Redux_Helpers::isLocalHost ()) {
        $is_localhost = true;
        $localhost_bit = esc_html__('you are working in a localhost environment', 'xstore');
    }
    
    $conjunction_bit = '';
    if ($is_localhost && $is_debug) {
        $conjunction_bit = ' ' . esc_html__('and', 'xstore') . ' ';
    }
    
    $tip_msg    = esc_html__('Redux has enabled developer mode because', 'xstore') . ' ' . $debug_bit . $conjunction_bit . $localhost_bit . '.';
} else {
    $tip_msg    = esc_html__('If you are not a developer, your theme/plugin author shipped with developer mode enabled. Contact them directly to fix it.', 'xstore');
}

?>
<div id="redux-header">
	<?php if ( ! empty( $this->parent->args['display_name'] ) ) { ?>
		<div class="display_header">

			<?php if ( isset( $this->parent->args['dev_mode'] ) && $this->parent->args['dev_mode'] ) { ?>
                            <div class="redux-dev-mode-notice-container redux-dev-qtip" qtip-title="<?php echo $tip_title; ?>" qtip-content="<?php echo $tip_msg; ?>">
				<span class="redux-dev-mode-notice"><?php esc_html_e( 'Developer Mode Enabled', 'xstore' ); ?></span>
                            </div>
                        <?php } ?>

			<h2><?php echo $this->parent->args['display_name']; ?>

			<?php if ( ! empty( $this->parent->args['display_version'] ) ) { ?>
				<span class="redux-theme-version">Version <?php echo $this->parent->args['display_version']; ?></span>
                        <?php } ?>
            </h2>
		</div>
        <?php } ?>

	<a onclick="window.location.href='http://8theme.com'" class="et-developer-link">Visit our 8theme website</a>
	
	<div class="clear"></div>
</div>

<?php etheme_support_chat(); ?>