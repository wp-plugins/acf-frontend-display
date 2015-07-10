<?php
/*
Plugin Name: ACF frontend display
Plugin URI: https://github.com/dadmor/ACF_frontend_display
Description: WordPress plugin to display afd form on frontend your site. This Plugin enhancing the Advanced Custom Fields (ACF)
Author: gdurtan
Author URI: grzegorz.durtan.pl
Version: 2.0.6
License: GPL2
*/
/* --------------------------------- */
/* SCRIPTS AND STYLES                */

function afd_admin_lib_init() {

	?>
	<script>
		window.pluginURI = '<?php echo get_bloginfo("home"); ?>/wp-content/plugins/acf-frontend-display/';
	</script>
	<?php
	
	wp_register_script( 'alpaca-js', plugins_url('/js/alpaca-core.min.js', __FILE__) );
	wp_enqueue_script( 'alpaca-js' );

	wp_register_style( 'alpaca-wpadmin-css', plugins_url('/css/alpaca-wpadmin.css', __FILE__) );
	wp_enqueue_style( 'alpaca-wpadmin-css' );

	/* toggle-switch extra field */
	//wp_register_style( 'toggle-switch-css', plugins_url('/css/toggle-switch.css', __FILE__) );
	//wp_enqueue_style('toggle-switch-css');

	//wp_register_script( 'acf-frontend-display-admin', plugins_url('/js/acf-frontend-display-admin.js', __FILE__) );
	//wp_enqueue_script('acf-frontend-display-admin');

	if(@$_GET['page']=='acf-export'){
		wp_register_script( 'acf-more-export', plugins_url('/js/acf-more-export.js', __FILE__) );
	    wp_enqueue_script( 'acf-more-export' );
	}

}
add_action('admin_enqueue_scripts', 'afd_admin_lib_init');

function afd_fields_frontend_lib_init() {

	if ( !is_admin() ) {

		?>
		<script>
			window.pluginURI = '<?php echo get_bloginfo("home"); ?>/wp-content/plugins/acf-frontend-display/';
		</script>
		<?php

		wp_enqueue_script('jquery');
		//wp_register_script( 'acf-frontend-display', plugins_url('/js/acf-frontend-display.js', __FILE__) );
		//wp_enqueue_script('acf-frontend-display');

		//wp_register_style( 'toggle-switch-css', plugins_url('/css/toggle-switch.css', __FILE__) );
		//wp_enqueue_style('toggle-switch-css');

		wp_register_style( 'fields-pack', plugins_url('/css/frontend-fields-pack.css', __FILE__) );
		wp_enqueue_style('fields-pack');

		//wp_register_script( 'acf-frontend-ajax', plugins_url('/js/acf-frontend-ajax.js', __FILE__) );
       // wp_enqueue_script( 'acf-frontend-ajax' );

	}
}
add_action('wp_enqueue_scripts', 'afd_fields_frontend_lib_init');

/* --------------------------------- */
/* RENDER FORM ON FRONT              */

require_once( plugin_dir_path( __FILE__ ) . '/inc/render-hooks.php');

/* --------------------------------- */
/* METABOX                           */

require_once( plugin_dir_path( __FILE__ ) . '/inc/metabox-hooks.php');
require_once( plugin_dir_path( __FILE__ ) . '/inc/metabox-save.php');

/* ACF EXTENTION - INIT UPLAOAD FILE */
function afd_upload_field() {

    /* comment sesurity problem js upload library */
	//require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-upload-file.php');
	//require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-upload-files.php');
	
    require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-poolAB-file.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-hidden-file.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-date-picker.php');
	require_once( plugin_dir_path( __FILE__ ) . '/fields-pack/field-flat-repeater.php');
}
add_action('acf/register_fields', 'afd_upload_field');

/***********************.*/
/*  API                 .*/
/***********************.*/
function afd_form_permision( $options = array() )
{
    global $post;
    
    // filter post_id
    @$options['post_id'] = apply_filters('acf/get_post_id', $options['post_id'] );

    // attributes
    @$options['form_attributes']['class'] .= 'acf-form';
    
    // register post box
    if( empty($options['field_groups']) )
    {
        // get field groups
        $filter = array(
            'post_id' => $options['post_id']
        );
   
        $options['field_groups'] = array();
        $options['field_groups'] = apply_filters( 'acf/location/match_field_groups', $options['field_groups'], $filter );
    }

    // html before fields
    
    $acfs = apply_filters('acf/get_field_groups', array());
  
    if( is_array($acfs) ){ foreach( $acfs as $acf ){
        
        // only add the chosen field groups
        if( !in_array( $acf['id'], $options['field_groups'] ) )
        {
            continue;
        }
        return $options['field_groups'];
    }}
    // html after fields
}

function flaten_relation_actio_init(){
    function flaten_relation_action($post_id){
       
        $args = json_decode(urldecode($_POST['multi_relation_options']));

        if($args->joined_type=='user_id'){
            $value = get_current_user_id();
        }
        flaten_relation_builder($value, $post_id , $_POST['multi_relation_field'], $args );

    }
    if($_POST['add_multi_relation']!=''){
        flaten_relation_action($_POST['add_multi_relation']);
    }
}
add_action('init', 'flaten_relation_actio_init');

function flaten_relation_builder($value, $post_id, $field_name, $args = array()){
        
        $repeter_tech_meta_key = 'ref_'.$field_name;
        $value = preg_replace( '/\s+/' , '' , $value );
        $metas_array = explode( "," , $value );
        
        /* store unique values */
        $metas_array = array_unique( $metas_array );

        /* delete all */
        //delete_post_meta_by_key( $repeter_tech_meta_key );

        delete_post_meta( $post_id , $repeter_tech_meta_key );
        
        foreach ($metas_array as $key => $fvalue) {
            //$value.= $post_id.':'.$repeter_tech_meta_key.':'.$fvalue.' | ';
            add_post_meta( $post_id , $repeter_tech_meta_key , $fvalue, false );
        }
        
        $value = implode(",", $metas_array);

        update_post_meta($post_id, $field_name, $value);

        return $value;

}
/* EDIT POST LINK */
if ( ! is_admin() ) {
    add_filter( 'get_edit_post_link', 'my_edit_post_link' );
} 
function my_edit_post_link() {
	global $post;
    $url = get_permalink().'?pid='.$post->ID;
    return $url;
}
function get_action_edit(){
    if($_GET['action']=='edit'){
        global $post;
        wp_redirect(get_permalink().'?pid='.$post->ID);
        exit;
    }
}
add_action('wp', 'get_action_edit');