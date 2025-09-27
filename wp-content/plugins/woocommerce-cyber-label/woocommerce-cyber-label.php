<?php
/**
 * Plugin Name: WooCommerce Cyber Label
 * Description: Adds a "Cyber" badge to selected WooCommerce products on catalog and single product pages.
 * Version: 1.0.0
 * Author: Demo Developer
 * Text Domain: woocommerce-cyber-label
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WooCommerce_Cyber_Label {

    const META_KEY = '_wc_cyber_label_enabled';

    public function __construct() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_product_field' ) );
        add_action( 'woocommerce_admin_process_product_object', array( $this, 'save_product_field' ) );
        add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'render_catalog_label' ), 5 );
        add_action( 'woocommerce_single_product_summary', array( $this, 'render_single_label' ), 4 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'woocommerce-cyber-label', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function add_product_field() {
        echo '<div class="options_group">';

        woocommerce_wp_checkbox( array(
            'id'          => self::META_KEY,
            'label'       => __( 'Mostrar etiqueta "Cyber"', 'woocommerce-cyber-label' ),
            'description' => __( 'Añade una etiqueta destacada "Cyber" en el catálogo y la página del producto.', 'woocommerce-cyber-label' ),
        ) );

        echo '</div>';
    }

    public function save_product_field( $product ) {
        $enabled = isset( $_POST[ self::META_KEY ] ) ? 'yes' : 'no';
        $product->update_meta_data( self::META_KEY, $enabled );
    }

    protected function is_label_enabled( $product ) {
        if ( ! $product instanceof WC_Product ) {
            $product = wc_get_product( $product );
        }

        if ( ! $product ) {
            return false;
        }

        return 'yes' === $product->get_meta( self::META_KEY );
    }

    public function render_catalog_label() {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        if ( ! $this->is_label_enabled( $product ) ) {
            return;
        }

        echo $this->get_label_markup();
    }

    public function render_single_label() {
        global $product;

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        if ( ! $this->is_label_enabled( $product ) ) {
            return;
        }

        echo $this->get_label_markup();
    }

    protected function get_label_markup() {
        ob_start();
        ?>
        <span class="wc-cyber-label"><?php esc_html_e( 'Cyber', 'woocommerce-cyber-label' ); ?></span>
        <?php
        return ob_get_clean();
    }

    public function enqueue_assets() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        $style_path = plugin_dir_path( __FILE__ ) . 'assets/css/style.css';

        if ( file_exists( $style_path ) ) {
            wp_enqueue_style(
                'woocommerce-cyber-label',
                plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
                array(),
                filemtime( $style_path )
            );
        }
    }
}

new WooCommerce_Cyber_Label();
