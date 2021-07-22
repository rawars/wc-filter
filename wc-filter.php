<?php 
/* 
Plugin Name: Wc Filter
Plugin URI: https://www.wcfilter.delysk.com
Description: Plugin que permite el filtrado de productos por medio de una simple api REST
Version: 1.0 
Author: Rafael Garcia
Author URI: https://www.delysk.com
License: GPLv2 
*/



add_action( 'rest_api_init', function () {
	register_rest_route( 'wc-filter/v1', '/filter/',
		array(
		    'Content-Type' => 'application/json',
			'methods' => 'GET', 
			'callback' => 'convuls_customquery'
		)
	);
});

add_filter( 'woocommerce_product_data_store_cpt_get_products_query', 'handle_custom_query_var', 10, 2 );
function handle_custom_query_var( $query, $query_vars ) {
    if ( isset( $query_vars['like_name'] ) && ! empty( $query_vars['like_name'] ) ) {
        $query['s'] = esc_attr( $query_vars['like_name'] );
    }

    return $query;
}


function convuls_customquery($data){
    
    header('Content-Type: application/json');
    
    $body = $data->get_body_params();

    global $wpdb;
    
    $product = $_GET['product'];
    
    $words = explode(" ", $product);
    
    foreach ($words as $word) {
        $word = trim($word);
        if ($word) {
            $searchQuery = $searchQuery . " {$wpdb->prefix}posts.post_title LIKE '%$word%' AND ";
        }
    }
    
    $searchQuery = rtrim($searchQuery, " AND ");

    $data = [];

    $data = $wpdb->get_results("SELECT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_name, {$wpdb->prefix}posts.post_title, {$wpdb->prefix}posts.guid, {$wpdb->prefix}postmeta.meta_value FROM {$wpdb->prefix}posts, {$wpdb->prefix}postmeta WHERE {$wpdb->prefix}posts.ID LIKE {$wpdb->prefix}postmeta.post_id AND {$wpdb->prefix}posts.post_type LIKE 'product' AND {$wpdb->prefix}posts.post_status LIKE 'publish' AND ($searchQuery) GROUP BY {$wpdb->prefix}posts.post_title");
    
    $products = [];
    
    for($i=0; $i < count($data); $i++){
        array_push($products, array(
                "id"=>$data[$i]->ID,
                "post_name"=>$data[$i]->post_name,
                "post_title"=>$data[$i]->post_title,
                "guid"=>$data[$i]->guid,
                "image"=>get_the_post_thumbnail_url($data[$i]->ID)
            ));
    }
    
    
    return new WP_REST_Response($products, 200);
}