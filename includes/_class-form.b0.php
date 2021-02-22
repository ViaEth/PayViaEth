<?php 
if(!class_exists('C9wep_Form')){
class C9wep_Form{
    static public function get_request_fields_values( $keys=array() ) {
        if(!is_array($keys) && !empty($keys)){
            return self::get_request_parameter($keys,'');
        }

        $values=array();
        foreach ($keys as $key) {
            $values[$key]=self::get_request_parameter($key,'');
        }
        return $values;
    }

    static public function get_request_parameter( $key, $default = '' ) {
        // If not request set
        if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
            return $default;
        }
     
        // Set so process it
        return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
    }

    static public function out_fields($fields=array(),$fields_current_value=array(),$return_array=false){
        $htmls=array();
        foreach ($fields as $key => $field) {
            $current_value=isset($fields_current_value[$key]) ? $fields_current_value[$key] : null;
            $htmls[]=self::out_single_field($field,$current_value);
        }

        if($return_array){
            return $htmls;
        }else{
            return implode($htmls,PHP_EOL);
        }
    }

    static public function out_single_field($field=array(),$current_value=null){
        extract($field);
        if(empty($ui_type)){
            $ui_type='text';
        }
        $field_html_fun=$ui_type."_field";
        return self::$field_html_fun($field,$current_value);
    }

    static public function email_field($field=array(),$current_value=null){
        ob_start();
        extract($field);
        $value=$default_value;
        if(null != $current_value){
            $value=$current_value;
        }
        ?>
        <div>
            <label for="<?php echo $id; ?>"><?php _e($label, 'c9nn' ); ?><?php if($required){echo '(*)';} ?></label>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="email" autocomplete="off" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-email" placeholder="<?php echo esc_attr( $placeholder, 'c9nn' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }
    static public function text_field($field=array(),$current_value=null){
        ob_start();
        extract($field);
        $value=$default_value;
        if(null != $current_value){
            $value=$current_value;
        }
        ?>
        <div class="<?php echo $out_class; ?>">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><?php _e($label, 'c9nn' ); ?><?php if($required){echo '(*)';} ?></label>
            <?php endif;//end !empty($label) ?>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="text" name="<?php echo $id; ?>" autocomplete="off" id="<?php echo $id; ?>" class="<?php echo $ui_class; ?>" placeholder="<?php echo esc_attr( $placeholder, 'c9nn' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }

    static public function textarea_field($field=array(),$current_value=null){
        ob_start();
        extract($field);
        $value=$default_value;
        if(null != $current_value){
            $value=$current_value;
        }
        ?>
        <div class="<?php echo $out_class; ?>">
            <label for="<?php echo $id; ?>"><?php _e($label, 'c9nn' ); ?><?php if($required){echo '(*)';} ?></label>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <textarea name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-textarea" placeholder="<?php echo esc_attr( $placeholder, 'c9nn' ); ?>" <?php echo $required_attr; ?> rows="3" cols="40"  /><?php echo $value; ?></textarea>
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }
        
    static public function checkbox_field($field=array(),$current_value_arr=array()){
        ob_start();
        extract($field);
        $value=$default_value;
        // if(null != $current_value){
        //     $value=$current_value;
        // }
        ?>
        <div class="<?php echo $out_class; ?>">
        <?php foreach ($options as $value => $label): ?>
            <?php 
                $checked='';
                if(is_array($current_value_arr) && in_array($value,$current_value_arr) != false){//find the value
                    $checked=' checked="checked"';
                }
             ?>
                <label>
                    <input name="<?php echo $id . '[]'; ?>" type="checkbox" value="<?php echo $value; ?>" <?php echo $checked; ?>>
                    <?php echo $label; ?>
                </label>
        <?php endforeach ?>
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }

    static public function select_field($field=array(),$current_value=null){
        ob_start();
        extract($field);
        $value=$default_value;
        if(!empty($current_value)){
            $value=$current_value;
        }
        ?>
        <div class="<?php echo $out_class; ?>">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><?php _e($label, 'c9nn' ); ?><?php if($required){echo '(*)';} ?></label>
            <?php endif;//end !empty($label) ?>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select name="<?php echo $id; ?>" id="<?php echo $id; ?>" autocomplete="off" class="<?php echo $ui_class; ?>" <?php echo $required_attr; ?>  data-width="100%">
            <?php foreach ($options as $value => $label): ?>
                <?php 
                    $selected='';
                    if($current_value==$value){
                        $selected=' selected="selected"';
                    }
                 ?>
                <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $label; ?></option>
            <?php endforeach ?>
            </select>
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }
}
}
    
