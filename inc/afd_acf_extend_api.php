<?php
function afd_form_permision( $options = array() )
{
    global $post;
    
    // filter post_id
    $options['post_id'] = apply_filters('acf/get_post_id', $options['post_id'] );
    
    // attributes
    $options['form_attributes']['class'] .= 'acf-form';
    
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
        return true;
    }}
    // html after fields
}


function afd_form_head()
{
    // global vars
    global $post_id;



    
    // verify nonce
    if( isset($_POST['acf_nonce']) && wp_verify_nonce($_POST['acf_nonce'], 'input') )
    {
 


        // $post_id to save against
        $post_id = $_POST['post_id'];
        
        
        // allow for custom save
        $post_id = apply_filters('acf/pre_save_post', $post_id);
        
        
        // save the data
        do_action('acf/save_post', $post_id);   


        /* ADF + Forms Actions resoult */
        $fa = fa_realize_form_actions();
        if($fa["block_redirect"] != true){
       
            if(isset($_POST['return']))
            {
                wp_redirect($_POST['return']);
                exit;
            }
        }


    }
    
    
    // need wp styling
    wp_enqueue_style(array(
        'colors-fresh'
    ));
    
        
    // actions
    //do_action('acf/input/admin_enqueue_scripts');

    add_action('wp_head', 'acf_form_wp_head');
    
}

?>