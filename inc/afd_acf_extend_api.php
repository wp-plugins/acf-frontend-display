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
        
        //var_dump( $options['field_groups'] );
        
        // only add the chosen field groups
        if( !in_array( $acf['id'], $options['field_groups'] ) )
        {
            continue;
        }
        return true;
    }}
    // html after fields
}

function afd_attached_forms_array( $options = array() )
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
             
        }else{
           $mapped_ids[] = $acf['id'];
        }
       
    }
     return $mapped_ids;
}

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
        $fa = call_user_func('fa_realize_form_actions'); 
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

function afd_frontend_form( $options = array() )
{
    global $post;
    global $acf;
   
    //var_dump('sad',$acf);
    if(@$acf->AFD_block_display == true){
        $block_display_guargian = true;
    }

    // defaults
    $defaults = array(
        'post_id' => false,
        'field_groups' => array(),
        'form' => true,
        'form_attributes' => array(
            'id' => 'post',
            'class' => '',
            'action' => '',
            'method' => 'post',
        ),
        'return' => add_query_arg( 'updated', 'true', get_permalink() ),
        'html_before_fields' => '',
        'html_after_fields' => '',
        'submit_value' => __("Update", 'acf'),
        'updated_message' => __("Post updated", 'acf'), 
    );
    
    
    // merge defaults with options
    $options = array_merge($defaults, $options);
    
    
    // merge sub arrays
    foreach( $options as $k => $v )
    {
        if( is_array($v) )
        {
            $options[ $k ] = array_merge($defaults[ $k ], $options[ $k ]);
        }
    }
    
    
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
        
        
/*        if( strpos($options['post_id'], 'user_') !== false )
        {
            $user_id = str_replace('user_', '', $options['post_id']);
            $filter = array(
                'ef_user' => $user_id
            );
        }
        elseif( strpos($options['post_id'], 'taxonomy_') !== false )
        {
            $taxonomy_id = str_replace('taxonomy_', '', $options['post_id']);
            $filter = array(
                'ef_taxonomy' => $taxonomy_id
            );
        }*/
        
        
        $options['field_groups'] = array();
        $options['field_groups'] = apply_filters( 'acf/location/match_field_groups', $options['field_groups'], $filter );
    }


    // updated message
    if(isset($_GET['updated']) && $_GET['updated'] == 'true' && $options['updated_message'])
    {
       $display_message = $options['updated_message'];
    }
   
    
    // display form

    $html_f_head = '';

    if( $options['form'] ): 
    $html_f_head = '<form ';
        if($options['form_attributes']){
            foreach($options['form_attributes'] as $k => $v){
                $html_f_head .= $k . '="' . $v .'" '; 
        }
    } 
    $html_f_head .= '>';
    endif; 

    // html before fields
    $html_body_top_decorator = '';
    $html_body_top_decorator .= '<div id="poststuff">';
    
    $html_body_top_decorator .= $options['html_before_fields'];
    
    $acfs = apply_filters('acf/get_field_groups', array());
    
   
    if( is_array($acfs) ){ foreach( $acfs as $acf ){
        
        // only add the chosen field groups
        if( !in_array( $acf['id'], $options['field_groups'] ) )
        {
            continue;
        }

        // load options
        $acf['options'] = apply_filters('acf/field_group/get_options', array(), $acf['id']);
        
        // load fields
        $fields = apply_filters('acf/field_group/get_fields', array(), $acf['id']);

        $html_body_top_decorator .= '<div id="acf_' . $acf['id'] . '" class="postbox acf_postbox ' . $acf['options']['layout'] . '">';
        $html_body_top_decorator .= '<h3 class="hndle"><span>' . $acf['title'] . '</span></h3>';
        $html_body_top_decorator .= '<div class="inside">';
        
        /* realize forced hidden fields  */
        // nie trzeba tego robić - forcehidden działa :)
        //$fields = forced_hidden($fields);
        
        /* ----------------------------- */
        
        /* ----------------------------- */
        /* Change field class            */
        //echo '<pre>';
        //var_dump($fields);
        //echo '</pre>';

        if($options['css_type'] == 'bootstrap'){
            foreach ($fields as $key => $value) {
                $fields[$key]['class'] = 'form-control';
            }
        }        

        $html_body_bottom_decorator = '</div></div>';
       
            foreach ($fields as $field) {

            if($field['updated_message'] != ''){
                
                $dependencyName = $field['conditional_logic']['rules'][0]["field"];
                $dependencyValue = $field['conditional_logic']['rules'][0]["value"];
               

                if($_POST['fields'][$dependencyName] == $dependencyValue  ){
                    //print('dep true');
                    //$dependency_guardian = true;
                    $display_message = $field['updated_message'];

                }
                /* is conditional logic */
            };

/*            if($dependency_guardian != true){
                $display_message = $field['updated_message'];
            }*/
       
    }        
        
    }}
    
    // html after fields
    $html_body_bottom_decorator .= $options['html_after_fields'];
    
    if( $options['form'] ): 
        $html_body_bottom_decorator .= '<!-- Submit -->';
        $html_body_bottom_decorator .= '<div class="field">';
        $html_body_bottom_decorator .= '<input type="submit" value="'.$options['submit_value'].'" />';
        $html_body_bottom_decorator .= '</div>';
        $html_body_bottom_decorator .= '<!-- / Submit -->';
    endif;
    
    $html_body_bottom_decorator .= '</div><!-- <div id="poststuff"> -->';
    
    if( $options['form'] ):
        $html_body_bottom_decorator .= '</form>';
    endif;
    //var_dump($_POST['acf_nonce']);
    if($_POST['acf_nonce'] != ''){  
        if($display_message != ''){
            echo '<div id="message" class="updated"><p>'.$display_message.'</p></div>';
        }
    }
    
    if( $block_display_guargian != true ){

        echo $html_f_head;
        ?>
        <div style="display:none">
            <script type="text/javascript">
                acf.o.post_id = <?php echo is_numeric($options['post_id']) ? $options['post_id'] : '"' . $options['post_id'] . '"'; ?>;
            </script>
            <input type="hidden" name="acf_nonce" value="<?php echo wp_create_nonce( 'input' ); ?>" />
            <input type="hidden" name="post_id" value="<?php echo $options['post_id']; ?>" />
            <input type="hidden" name="return" value="<?php echo $options['return']; ?>" />
            <?php wp_editor('', 'acf_settings'); ?>
        </div>
        <?php
        echo  $html_body_top_decorator;
        /* ACF action problem - dont return data - only forced print it */  
        do_action('acf/create_fields', $fields, $options['post_id']);
        echo $html_body_bottom_decorator;
    
    }

}

function forced_hidden($fields){

/*    echo '<pre>';
    var_dump($fields);
    echo '</pre>';*/

    $counter = 0;
    foreach ($fields as $field) {

        if( ( $field['forced_hidden'][0] == 'forced_hidden')  && ( $field['type'] = 'afd_hidden')){

            //unset($fields[$counter]);
            //update_field($field['key'], $field["value"], $post->ID);
        }
        $counter ++;
        //afd_hidden
        
    }

    return $fields;
}



?>