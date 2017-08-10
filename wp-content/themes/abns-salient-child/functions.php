<?php 

if( !defined( 'PROPEL_CSR_ADMIN_EMAIL' ) ) {
  define( 'PROPEL_CSR_ADMIN_EMAIL', 'purchase.orders@scitent.com' );
}

function salient_child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('font-awesome'));
    wp_enqueue_style( 'catalog-style', get_stylesheet_directory_uri() . '/css/catalog-style.css');
    wp_enqueue_style( 'my-courses-style', get_stylesheet_directory_uri() . '/css/my-courses-style.css');
}
add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles');

function course_catalog_js() {
    wp_enqueue_script( 'course_catalog_js', get_stylesheet_directory_uri() . '/js/tmci-catalog.js', array( 'jquery' ), false, true );
}
add_action( 'wp_enqueue_scripts', 'course_catalog_js' );


//sku product page redirects
function redirect_sku_slugs() {
  global $wpdb;
  $uri = explode('/', $_SERVER["REQUEST_URI"]);
  if ($uri[1] == 'sku') {
    error_log("sku slugs loaded: ".$uri[2]);
    $product_query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1";
    $product_id = $wpdb->get_var( 
      $wpdb->prepare( 
        $product_query, 
        $uri[2] 
      ) );
    error_log($product_id);
    error_log(get_permalink( $product_id ));
    if ($product_id){
      wp_redirect(get_permalink( $product_id )); 
      exit;
    }
  }
}
add_action( 'init', 'redirect_sku_slugs' );


//////////////////////////


function prefix_reset_metabox_positions(){
  //delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_post' );
  //delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_page' );
  //delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_product' );
  delete_user_meta( wp_get_current_user()->ID, 'meta-box-order_custom_post_type' );
}
add_action( 'admin_init', 'prefix_reset_metabox_positions' );

// WP User Manager Integrations for ABNS:

add_action('wpum/form/register/after/field=wpum_abns_pledge',
  array( 'Propel_User_Reg', 'generic_required_warning' ), 10 );

add_filter( 'wpum/form/validate=register', function( $passed, $fields, $values ) {
  if( '' === $values['register']['wpum_abns_pledge'] ) {
    return new WP_Error( 'registration-validation-error', __('You must agree to the pledge to register.','propel'));
  }
  if( '' === $values['register']['wpum_abns_terms'] ) {
    return new WP_Error( 'registration-validation-error', __('You must agree to the terms and conditions to register.','propel'));
  }
  return $passed;
}, 10, 3 );