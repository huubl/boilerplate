<?

namespace IMAGA\Theme\Setup;

use IMAGA\Theme\Assets;

/*
 * Theme setup
 */
function setup() {

  // Make theme available for translation
  load_theme_textdomain('imaga', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'imaga'),
    'secondary_navigation' => __('Secondary Navigation', 'imaga'),
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');

  // Enable post formats
  //add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

  // Enable HTML5 markup support
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/dashboard.css'));

  // Add image sizes
  // Header Element
  add_image_size('header-narrow', 750, 600, true);
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/*
 * Theme assets
 */
function assets() {
  wp_enqueue_style('imaga/css', Assets\asset_path('styles/main.css'), false, null);

  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', Assets\asset_path('scripts/jquery.js'), null, null, true);

  wp_enqueue_script('imaga/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);

/*
 * Login assets
 */
function add_login_stylesheet() {

  wp_register_style('imaga/login', Assets\asset_path('styles/login.css') );

  wp_enqueue_style( 'imaga/login');

}
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\add_login_stylesheet' );

/*
 * ACF Google Maps API Key
 */
function add_acf_google_maps_key() {

  if( ! defined( 'GOOGLE_MAPS_API' ) ) return;

  acf_update_setting('google_api_key', GOOGLE_MAPS_API );

}
add_action('acf/init', __NAMESPACE__ . '\\add_acf_google_maps_key');

/*
 * Add Google Fonts
 */
function add_google_fonts() {

  // Defined in functions.php
  if( ! defined( 'GOOGLE_FONTS' ) ) return;

  wp_enqueue_style( 'imaga/google-fonts', 'http://fonts.googleapis.com/css?family=' . GOOGLE_FONTS );

}
add_action( 'wp_head', __NAMESPACE__ . '\\add_google_fonts' , 1);

/*
 * Remove 'page-template' from body class on pages with custom templates
 */
function prefix_remove_body_class($wp_classes) {

  if ( is_page_template() ):
    foreach ($wp_classes as $key => $value) {
      $wp_classes[$key] = str_replace('page-template-template-', '', $value);
    }
  endif;

  return $wp_classes;
}
add_filter('body_class', __NAMESPACE__ . '\\prefix_remove_body_class', 20, 2);

/*
 * Remove from admin menu
 */
function remove_admin_menus() {
  remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_admin_menus' );

/*
 * Remove from post and pages
 */
function remove_comment_support() {
  remove_post_type_support( 'post', 'comments' );
  remove_post_type_support( 'page', 'comments' );
}
add_action( 'init', __NAMESPACE__ . '\\remove_comment_support', 100);

/*
 * Remove from admin bar
 */
function admin_bar_render() {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', __NAMESPACE__ . '\\admin_bar_render' );

/*
 * Add custom styling for the dashboard interface
 */
function register_admin_styles(){

  wp_enqueue_style( 'imaga/admin-styles', Assets\asset_path('styles/admin.css') );

}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\register_admin_styles');

/*
 * Disable auto-paragraphing for Contact Form 7
 */
add_filter('wpcf7_autop_or_not', '__return_false');
