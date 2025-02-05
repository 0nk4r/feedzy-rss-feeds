<?php
/**
 * Register elementor widget.
 *
 * @package    feedzy-rss-feeds
 * @subpackage feedzy-rss-feeds/includes/elementor
 */

/**
 * The Widget functionality of the plugin.
 *
 * @package    feedzy-rss-feeds
 * @subpackage feedzy-rss-feeds/includes/elementor
 * @author     Themeisle <friends@themeisle.com>
 */
class Feedzy_Rss_Feeds_Elementor {

	/**
	 * Enqueue scripts.
	 */
	public function feedzy_elementor_widgets_assets() {
		wp_register_style( 'feedzy-elementor', FEEDZY_ABSURL . 'css/feedzy-elementor-widget.css', array(), true );
		wp_enqueue_style( 'feedzy-elementor' );
	}

	/**
	 * Editor frontend before script.
	 */
	public function feedzy_elementor_before_enqueue_scripts() {
		wp_register_style( 'feedzy-rss-feeds-elementor', FEEDZY_ABSURL . 'css/feedzy-rss-feeds.css', array( 'elementor-frontend' ), true, 'all' );
		wp_enqueue_style( 'feedzy-rss-feeds-elementor' );
	}

	/**
	 * Register feedzy widget.
	 *
	 * @return void
	 */
	public function feedzy_elementor_widgets_registered() {
		// We check if the Elementor plugin has been installed / activated.
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			require_once FEEDZY_ABSPATH . '/includes/elementor/widgets/register-widget.php';
			\Elementor\Plugin::instance()->widgets_manager->register( new Feedzy_Register_Widget() );

			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'feedzy_elementor_widgets_assets' ) );
			add_action( 'elementor/widget/before_render_content', array( $this, 'feedzy_elementor_editor_upsell_notice' ) );
		}
	}

	/**
	 * Register datetime-local control.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 * @return void
	 */
	public function feedzy_elementor_register_datetime_local_control( $controls_manager ) {
		require_once FEEDZY_ABSPATH . '/includes/elementor/controls/datetime-local.php';
		require_once FEEDZY_ABSPATH . '/includes/elementor/controls/template-layout.php';
		$controls_manager->register( new \Elementor\Control_Date_Time_Local() );
		$controls_manager->register( new \Elementor\Control_Template_Layout() );
	}

	/**
	 * Widget render before.
	 *
	 * @param object $widget Elementor widget object.
	 * @return void
	 */
	public function feedzy_elementor_editor_upsell_notice( $widget ) {
		if ( 'feedzy-rss-feeds' === $widget->get_name() ) {
			if ( ! feedzy_is_pro() && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				$upsell_url = tsdk_translate_link( tsdk_utmify( FEEDZY_UPSELL_LINK, 'amazonproductadvertising', 'elementoreditor' ), 'query' );
				echo '<div class="fz-el-upsell-notice">';

				$upsell_msg  = '<strong>' . __( 'NEW!', 'feedzy-rss-feeds' ) . '</strong>';
				$upsell_msg .= wp_sprintf(
					// translators: %1$s: opening anchor tag, %2$s: closing anchor tag
					__( 'Enable Amazon Product Advertising feeds to generate affiliate revenue by %1$s upgrading to Feedzy Pro. %2$s', 'feedzy-rss-feeds' ),
					'<a target="_blank" class="upsell_link" href="' . esc_url( $upsell_url ) . '" >',
					'</a><button type="button" class="remove-alert"><span class="dashicons dashicons-no-alt"></span></button>'
				);

				echo wp_kses_post( $upsell_msg );
				echo '<script>';
				echo "jQuery( document ).ready( function() {
	jQuery( document ).on( 'click', '.fz-el-upsell-notice .remove-alert', function() {
		var upSellNotice = jQuery(this).parents( '.fz-el-upsell-notice' );
		upSellNotice.fadeOut( 500,
			function() {
				upSellNotice.remove();
			}
			);
		return false;
	} );
	jQuery( document ).on( 'click', '.fz-el-upsell-notice .upsell_link', function() {
		window.open( jQuery(this).attr( 'href' ), '_blank' ).focus();
	} );
} );";
				echo '</script>';
				echo '</div>';
			}
		}
	}
}
