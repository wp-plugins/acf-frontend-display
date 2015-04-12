<?php
// AJAX MODE
if($_POST['ajax_target'] != ''){    
    require_once("../../../../wp-load.php");
    $options = $_POST['args'];
    $options['post_id'] = $_POST['ID'];

    afd_frontend_form($options );
}

// NORMAL MODE

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
        return $options['field_groups'];
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
        if (defined('Forms_actions')) {
        
            $fa = call_user_func('fa_realize_form_actions'); 


            if($fa["block_redirect"] != true){

                if(isset($_POST['return']))
                {
                    
                    var_dump('redirect disabled - afd_acf_extend_api.php');
                    //wp_redirect($_POST['return']);
                    exit;
                }
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
    
    $extra_data = json_decode( urldecode ( get_post_meta($options['post_id'],'_meta_afd_form_render_box_alpaca', true )), true );
   
    
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
                   
        if (empty($fields)) {
              $fields = apply_filters('acf/field_group/get_fields', array(), $acf['id']);

        }else{
              $fields = array_merge($fields,apply_filters('acf/field_group/get_fields', array(), $acf['id'])); 
        }       

       

        

        $html_body_top_decorator .= '<div id="acf_' . $acf['id'] . '" class="postbox acf_postbox ' . $acf['options']['layout'] . '">';
        $html_body_top_decorator .= '<h3 class="hndle"><span>' . $acf['title'] . '</span></h3>';
        $html_body_top_decorator .= '<div class="inside">';
        
        /* realize forced hidden fields  */
        // nie trzeba tego robić - forcehidden działa :)
        //$fields = forced_hidden($fields);
        
        /* ----------------------------- */
        
        /* ----------------------------- */
        /* Change field class            */


        if($options['display_template'] == 'Bootstrap'){
            foreach ($fields as $key => $value) {
                
                
                if(($value['type'] == 'checkbox')||($value['type'] == 'radio')){
                   $fields[$key]['class'] = 'checkbox';
                }else{
                    $fields[$key]['class'] = 'form-control';
                }

                
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
    
    $submit_class = "";
    if( $extra_data['display_template'] == 'Bootstrap'){ 
        $submit_class = "btn btn-lg btn-success";
    }
    
    $submit_left = "";
   

    if( $extra_data['label_placement'] == 'left'){ 
        $submit_left_style = "margin-left:35%";
        $submit_left = "afd_input_left";
    }

   

    if( $extra_data['submit_ajax'] == true){ 
        $html_body_bottom_decorator .= '<!-- AJAX Submit -->';
        $html_body_bottom_decorator .= '<div class="field '.$submit_left.'" style="'.$submit_left_style.'">';
        $html_body_bottom_decorator .= '<a id="acf_ajax_submit" class="'.$submit_class.'">'.$options['submit_value'].'</a>';
        $html_body_bottom_decorator .= '</div>';
        $html_body_bottom_decorator .= '<!-- / AJAX Submit -->';       
    }else{
        if( $options['form'] ): 
            $html_body_bottom_decorator .= '<!-- Submit -->';
            $html_body_bottom_decorator .= '<div class="field '.$submit_left.'" style="'.$submit_left_style.'">';
            $html_body_bottom_decorator .= '<input type="submit" value="'.$options['submit_value'].'" class="hvr-float-shadow '.$submit_class.'"/>';
            $html_body_bottom_decorator .= '</div>';
            $html_body_bottom_decorator .= '<!-- / Submit -->';
        endif;
    }
    
    $html_body_bottom_decorator .= '</div><!-- <div id="poststuff"> -->';
    
    if( $options['form'] ):
        $html_body_bottom_decorator .= '</form>';
    endif;
    if($_POST['acf_nonce'] != ''){  
        if($display_message != ''){
            echo '<div class="message updated"><p>'.$display_message.'</p></div>';
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

        // Add user profile data
        if($_GET['guid'] != ''){
            $current_user_id = get_current_user_id();
            $user_info = get_userdata($_GET['guid']);
            if($current_user_id == $_GET['guid']){
                foreach ($fields as $key => $value) {
                    if($value['name'] == 'first_name'){
                        $fields[$key]['value'] = $user_info->first_name;
                    }
                    if($value['name'] == 'last_name'){
                        $fields[$key]['value'] = $user_info->last_name;
                    }
                }
            }else{
                echo 'User acces lock. Login as right user !!!'; 
            }
        }


        afd_create_fields($fields, $options['post_id']);
       
       

        //do_action('acf/create_fields', $fields, $options['post_id']);
        echo $html_body_bottom_decorator;
    
    }

}

function forced_hidden($fields){


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

function afd_create_fields( $fields, $post_id )
    {
        
        $extra_data = json_decode( urldecode ( get_post_meta($post_id,'_meta_afd_form_render_box_alpaca', true )), true );
        


        if( is_array($fields) ){ foreach( $fields as $field ){
            
            // if they didn't select a type, skip this field
            if( !$field || !$field['type'] || $field['type'] == 'null' )
            {
                continue;
            }
            
            
            // set value
            if( !isset($field['value']) )
            {
                $field['value'] = apply_filters('acf/load_value', false, $post_id, $field);
                $field['value'] = apply_filters('acf/format_value', $field['value'], $post_id, $field);
            }
            
            
            // required
            $required_class = "";
            $required_label = "";
            
            if( $field['required'] )
            {
                $required_class = ' required';
                $required_label = ' <span class="required">*</span>';
            }

            if( $extra_data['display_template'] == 'Bootstrap'){ 
                
                $main_css_decorator = 'form-group';
                if($field['type'] == 'radio'){
                    $main_css_decorator = 'field radio';
                }
                if($field['type'] == 'checkbox'){
                    $main_css_decorator = 'field checkbox';
                }



            }else{
                $main_css_decorator = 'field';
            }

                //echo '<pre>';
                //var_dump($field);
                //echo '</pre>';

            if( $extra_data['label_placement'] == 'left'){ 

            
                echo '<div id="acf-' . $field['name'] . '" class="'.$main_css_decorator.' field_type-' . $field['type'] . ' field_key-' . $field['key'] . $required_class . '" data-field_name="' . $field['name'] . '" data-field_key="' . $field['key'] . '" data-field_type="' . $field['type'] . '">';


                    echo '<p class="label afd_label_left">';
                        echo '<label for="' . $field['id'] . '">' . $field['label'] . $required_label . '</label>';
                        echo $field['instructions'];
                    echo '</p>';
                    
                    echo '<div class="afd_label_space">&nbsp;</div>';

                    echo '<div class="field afd_input_left">';
                    $field['name'] = 'fields[' . $field['key'] . ']';
                    
                    if( $extra_data['display_template'] == 'Bootstrap'){ 
                        $exist_classes = $field['class'];
                        if($field['type'] == 'checkbox'){
                            $field['class'] = $exist_classes.' checkbox';
                        }else{
                           $field['class'] = $exist_classes.' form-control'; 
                        }
                    }
                  
                    do_action('acf/create_field', $field, $post_id);
                    echo '</div>';

                    echo '<br style="clear:both">';
                echo '</div>';

            }else{



                echo '<div id="acf-' . $field['name'] . '" class="field '.$main_css_decorator.' field_type-' . $field['type'] . ' field_key-' . $field['key'] . $required_class . '" data-field_name="' . $field['name'] . '" data-field_key="' . $field['key'] . '" data-field_type="' . $field['type'] . '">';





                    if( $extra_data['label_placement'] != 'none'){ 
                        echo '<p class="label">';
                            echo '<label for="' . $field['id'] . '">' . $field['label'] . $required_label . '</label>';
                            echo $field['instructions'];
                        echo '</p>';
                    }


                    
                    $field['name'] = 'fields[' . $field['key'] . ']';
                   
                    do_action('acf/create_field', $field, $post_id);
                
                echo '</div>';

            }
            
        }}
                
    }

?>