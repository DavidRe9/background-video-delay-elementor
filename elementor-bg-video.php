<?php
/**
 * Plugin Name: Background Video Delay for Elementor (Improve LCP)
 * Description: Adds a configurable start delay for Elementor background videos (YouTube or MP4) to improve LCP and optimize page loading performance.
 * Version: 1.0.2
 * Author: David Kioshi Leite Kinjo (DavidRe9)
 * Author URI: https://github.com/DavidRe9
 * Plugin URI: https://github.com/DavidRe9/background-video-delay-elementor
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.4
 * Text Domain: background-video-delay-elementor
 * Copyright © 2025 David Kioshi Leite Kinjo (DavidRe9)
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'EBVCF_Plugin' ) ) {

    class EBVCF_Plugin {

        private $option_name = 'ebvcf_rules';

        public function __construct() {
            add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
            add_action( 'admin_init', [ $this, 'register_settings' ] );

            // AJAX (remoção de regra em rascunho)
            add_action( 'wp_ajax_ebvcf_remove_rule', [ $this, 'ajax_remove_rule' ] );

            // Frontend
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );

            // Admin
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        }

        public function add_admin_page() {
            add_menu_page(
                __( 'Background Video Rules', 'background-video-delay-elementor' ),
                __( 'BG Video Rules', 'background-video-delay-elementor' ),
                'manage_options',
                'ebvcf-admin',
                [ $this, 'admin_page_html' ],
                'dashicons-format-video',
                60
            );
        }

        public function register_settings() {
            register_setting( 'ebvcf_group', $this->option_name, [ $this, 'sanitize_rules' ] );
        }

        public function ajax_remove_rule() {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( __( 'Permission denied.', 'background-video-delay-elementor' ) );
            }

            // Reutiliza o nonce do settings_fields (ebvcf_group)
            // settings_fields imprime nonce com ação "{$option_group}-options"
            check_admin_referer( 'ebvcf_group-options' );

            $rules = isset( $_POST['ebvcf_rules'] ) ? (array) $_POST['ebvcf_rules'] : [];
            update_option( $this->option_name, $this->sanitize_rules( $rules ) );
            wp_send_json_success();
        }

        private function youtube_id_from_url( $url ) {
            $url = sanitize_text_field( $url );
            if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $url ) ) {
                return $url;
            }
            if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m ) ) {
                return $m[1];
            }
            $parts = wp_parse_url( $url );
            if ( ! empty( $parts['query'] ) ) {
                parse_str( $parts['query'], $qs );
                if ( ! empty( $qs['v'] ) && preg_match( '/^[A-Za-z0-9_-]{11}$/', $qs['v'] ) ) {
                    return $qs['v'];
                }
            }
            return $url;
        }

        public function sanitize_rules( $input ) {
            $clean = [];
            if ( is_array( $input ) ) {
                foreach ( $input as $rule ) {
                    $c = [];
                    $c['selector']         = isset( $rule['selector'] ) ? sanitize_text_field( $rule['selector'] ) : '';
                    $c['video_id']         = isset( $rule['video_id'] ) ? $this->youtube_id_from_url( $rule['video_id'] ) : '';
                    $c['delay']            = isset( $rule['delay'] ) ? floatval( $rule['delay'] ) : 0;
                    $c['scope']            = ( isset( $rule['scope'] ) && in_array( $rule['scope'], [ 'page', 'site' ], true ) ) ? $rule['scope'] : 'page';
                    $c['page_id']          = isset( $rule['page_id'] ) ? absint( $rule['page_id'] ) : 0;
                    $c['privacy']          = ! empty( $rule['privacy'] ) ? 1 : 0;
                    $c['overlay_color']    = isset( $rule['overlay_color'] ) ? ( sanitize_hex_color( $rule['overlay_color'] ) ?: '#000000' ) : '#000000';
                    $op                    = isset( $rule['overlay_opacity'] ) ? floatval( $rule['overlay_opacity'] ) : 0.4;
                    $c['overlay_opacity']  = max( 0, min( 1, $op ) );
                    $c['fallback_image_id']= isset( $rule['fallback_image_id'] ) ? absint( $rule['fallback_image_id'] ) : 0;
                    $c['fallback_image_url']= isset( $rule['fallback_image_url'] ) ? esc_url_raw( $rule['fallback_image_url'] ) : '';
                    $clean[] = $c;
                }
            }
            return array_values( $clean );
        }

        public function enqueue_admin_assets( $hook ) {
            if ( $hook !== 'toplevel_page_ebvcf-admin' ) {
                return;
            }
            wp_enqueue_media();

            // Script/Admin
            wp_enqueue_script(
                'ebvcf-admin-js',
                plugin_dir_url( __FILE__ ) . 'assets/js/ebvcf-admin.js',
                [ 'jquery' ],
                '1.9',
                true
            );

            // Define ajaxurl corretamente (string) e fornece nonce para AJAX
            $inline  = 'var ajaxurl = ' . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ';';
            $inline .= 'var ebvcfAdmin = { nonce: ' . wp_json_encode( wp_create_nonce( 'ebvcf_group-options' ) ) . ' };';
            wp_add_inline_script( 'ebvcf-admin-js', $inline, 'before' );

            // CSS/Admin
            wp_enqueue_style(
                'ebvcf-admin-css',
                plugin_dir_url( __FILE__ ) . 'assets/css/ebvcf-admin.css',
                [],
                '1.9'
            );
        }

        public function enqueue_frontend_assets() {
            wp_enqueue_script(
                'ebvcf-frontend',
                plugin_dir_url( __FILE__ ) . 'assets/js/ebvcf-script.js',
                [ 'jquery' ],
                '1.9',
                true
            );
            $rules = get_option( $this->option_name, [] );
            wp_add_inline_script( 'ebvcf-frontend', 'const ebvcf_rules = ' . wp_json_encode( array_values( $rules ) ) . ';' );
            wp_enqueue_style(
                'ebvcf-frontend-css',
                plugin_dir_url( __FILE__ ) . 'assets/css/ebvcf-frontend.css',
                [],
                '1.9'
            );
        }

        public function admin_page_html() {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            $rules = get_option( $this->option_name, [] );

            // Garante 1 linha-base para o clone do "Add rule" quando ainda não há regras
            if ( empty( $rules ) ) {
                $rules = [
                    [
                        'selector'           => '',
                        'video_id'           => '',
                        'delay'              => 0,
                        'scope'              => 'page',
                        'page_id'            => 0,
                        'privacy'            => 0,
                        'overlay_color'      => '#000000',
                        'overlay_opacity'    => 0.4,
                        'fallback_image_id'  => 0,
                        'fallback_image_url' => '',
                    ],
                ];
            }
            ?>
            <div class="wrap">
                <h1><?php echo esc_html__( 'Background Video Rules', 'background-video-delay-elementor' ); ?></h1>

                <form id="ebvcf-form" method="post" action="options.php">
                    <?php settings_fields( 'ebvcf_group' ); ?>

                    <table class="widefat fixed" id="rules">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__( 'Page ID CSS', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'Video ID / URL', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'Delay', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'Scope', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'ID from Page', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'YouTube Privacy', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'Overlay', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'Fallback', 'background-video-delay-elementor' ); ?></th>
                                <th><?php echo esc_html__( 'Action', 'background-video-delay-elementor' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $rules as $i => $rule ) : ?>
                            <tr>
                                <td>
                                    <input type="text" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][selector]" value="<?php echo esc_attr( $rule['selector'] ); ?>">
                                </td>
                                <td>
                                    <input type="text" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][video_id]" value="<?php echo esc_attr( $rule['video_id'] ); ?>">
                                </td>
                                <td>
                                    <input type="number" step="0.1" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][delay]" value="<?php echo esc_attr( $rule['delay'] ); ?>" style="width:80px;">
                                </td>
                                <td>
                                    <select name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][scope]">
                                        <option value="page" <?php selected( $rule['scope'], 'page' ); ?>><?php echo esc_html__( 'Page', 'background-video-delay-elementor' ); ?></option>
                                        <option value="site" <?php selected( $rule['scope'], 'site' ); ?>><?php echo esc_html__( 'Site', 'background-video-delay-elementor' ); ?></option>
                                    </select>
                                </td>
                                <td>
                                    <select name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][page_id]">
                                        <option value="0"><?php echo esc_html__( '— Select —', 'background-video-delay-elementor' ); ?></option>
                                        <?php foreach ( get_pages() as $p ) : ?>
                                            <option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( intval( $rule['page_id'] ), $p->ID ); ?>><?php echo esc_html( $p->post_title . ' (#' . $p->ID . ')' ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <label><input type="checkbox" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][privacy]" value="1" <?php checked( ! empty( $rule['privacy'] ) ); ?>> <?php echo esc_html__( 'Enable', 'background-video-delay-elementor' ); ?></label>
                                </td>
                                <td>
                                    <input type="color" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][overlay_color]" value="<?php echo esc_attr( $rule['overlay_color'] ); ?>">
                                    <input type="number" step="0.05" min="0" max="1" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][overlay_opacity]" value="<?php echo esc_attr( $rule['overlay_opacity'] ); ?>" style="width:60px;">
                                </td>
                                <td>
                                    <div class="fallback-image-preview" style="margin-bottom:6px;">
                                        <?php if ( ! empty( $rule['fallback_image_url'] ) ) : ?>
                                            <img src="<?php echo esc_url( $rule['fallback_image_url'] ); ?>" alt="" style="max-width:120px;height:auto;">
                                        <?php else : ?>
                                            <img src="" alt="" style="max-width:120px;height:auto;display:none;">
                                        <?php endif; ?>
                                    </div>
                                    <input class="fallback-image-id" type="hidden" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][fallback_image_id]" value="<?php echo esc_attr( $rule['fallback_image_id'] ); ?>">
                                    <input class="fallback-image-url" type="hidden" name="ebvcf_rules[<?php echo esc_attr( $i ); ?>][fallback_image_url]" value="<?php echo esc_attr( $rule['fallback_image_url'] ); ?>">
                                    <button class="button select-fallback-image"><?php echo esc_html__( 'Select', 'background-video-delay-elementor' ); ?></button>
                                    <button class="button remove-fallback-image" style="<?php echo empty( $rule['fallback_image_url'] ) ? 'display:none;' : ''; ?>"><?php echo esc_html__( 'Remove', 'background-video-delay-elementor' ); ?></button>
                                </td>
                                <td>
                                    <button class="button remove-rule"><?php echo esc_html__( 'Remove Rule', 'background-video-delay-elementor' ); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p><button id="add-rule" class="button button-primary"><?php echo esc_html__( 'Add Rule', 'background-video-delay-elementor' ); ?></button></p>

                    <?php submit_button( __( 'Save Changes', 'background-video-delay-elementor' ) ); ?>
                </form>
            </div>
            <?php
        }
    }
}

new EBVCF_Plugin();
