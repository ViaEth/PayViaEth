<?php 
if(!function_exists('_e')){
    function _e($txt, $lang_ns){
        echo $txt;
    }
}//end if !function_exists('_e')

if(!function_exists('esc_attr')){
    function esc_attr($txt, $lang_ns){
        echo $txt;
    }
}//end if !function_exists('esc_attr')

if(!function_exists('c9wep_convertCamelCaseName')){
    function c9wep_convertCamelCaseName($name)
    {
        if(!empty($name)){
            $name=preg_replace('/([A-Z]{1,20})/','-\1',$name);
            return $name;
        }
        return false;
    }
}//end if !function_exists('c9wep_convertCamelCaseName')

if(!function_exists('c9wep_getLowCaseIdName')){
    function c9wep_getLowCaseIdName($name)
    {
        if(!empty($name)){
            $raw_name=$name;
            $name=c9wep_convertCamelCaseName($name);
            $name=strtolower($name);
            $name=preg_replace('/[^A-Z0-9]+/i','_',$name);
            $name=trim($name,'_');
            return $name;
        }else if(0==$name){
            return $name;
        }
        return false;
    }
}//end if !function_exists('c9wep_getLowCaseIdName')
if(!function_exists('c9wep_render_fields')){
    function c9wep_render_fields($fields){
        if(empty($fields)){
            return false;
        }
        $ns='c9wep_';
        $fields_html=array();
        foreach ($fields as $key => $args) {
            $type='text';
            if(!empty($args['type'])){
                $type=$args['type'];
            }
            $fun=$ns . $type;
            
            switch ($type) {
                //hidden,text,textarea,checkbox_single,checkbox,select,multi_select,group_select,group_multi_select
                case 'hidden':
                case 'text':
                case 'datetimepicker':
                case 'textarea':
                case 'checkbox_single':
                    $fields_html[]=$fun($args['name'],$args['label'],$args['current_values'],$args['required'],$args['tips']);
                    break;
                
                case 'checkbox':
                case 'select':
                case 'multi_select':
                case 'group_select':
                case 'group_multi_select':
                    $fields_html[]=$fun($args['name'],$args['options'],$args['current_values'],$args['label'],$args['required'],$args['tips']);
                    break;

                default:
                    break;
            }
        } 
        return implode(PHP_EOL,$fields_html);
    }//end c9wep_render_fields() 
}//end if !function_exists('c9wep_render_fields')
if(!function_exists('c9wep_hidden')){
    function c9wep_hidden($id,$label,$value='',$required=true,$tips=''){
        ob_start();
        ?>
            <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>"/>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_hidden() 
}//end if !function_exists('c9wep_hidden')

if(!function_exists('c9wep_datetimepicker')){
    function c9wep_datetimepicker($name,$label,$value='',$required=true,$tips=''){
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="datetimepicker-wrapper <?php echo $id; ?>-datetimepicker-wrapper">
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-text" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_datetimepicker() 
}//end if !function_exists('c9wep_datetimepicker')

if(!function_exists('c9wep_file_upload')){
    function c9wep_file_upload($name,$label,$accept='*.*',$required=true,$tips='',$placeholder=''){
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="file-upload-wrapper <?php echo $id; ?>-file-upload-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9di' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty() ?>
      <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="file" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-file-upload" placeholder="<?php echo esc_attr( '', 'c9di' ); ?>"  accept="<?php echo $accept; ?>" <?php echo $required_attr; ?> /> 
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_file_upload() 
}//end if !function_exists('c9wep_file_upload')

if(!function_exists('c9wep_text')){
    function c9wep_text($name,$label,$value='',$required=true,$tips='',$placeholder=''){
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="text-wrapper <?php echo $id; ?>-text-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty() ?>
	    <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-text" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_text() 
}//end if !function_exists('c9wep_text')

if(!function_exists('c9wep_textarea')){
    function c9wep_textarea($name,$label,$value='',$required=true,$tips=''){
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="textarea-wrapper <?php echo $id; ?>-textarea-wrapper">
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <textarea name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-textarea" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" <?php echo $required_attr; ?> rows="3" cols="40"  /><?php echo $value; ?></textarea>
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_textarea() 
}//end if !function_exists('c9wep_textarea')

if(!function_exists('c9wep_checkbox_single')){
    function c9wep_checkbox_single($name, $label, $current_value, $required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        $checked='';
        $checked_value='yes';
        if($checked_value==$current_value){
            $checked='checked="checked"';
        }
    ?>
    <div class="checkbox-single-wrapper <?php echo $id; ?>-checkbox-single-wrapper">
        <label><b>
            <input type="checkbox" id="<?php echo $name; ?>" <?php echo $checked; ?> name="<?php echo $name; ?>" value="<?php echo $checked_value; ?>"><?php echo $label; ?></b>
        </label>
    </div>
<?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_checkbox_single() 
}//end if !function_exists('c9wep_checkbox_single')

if(!function_exists('c9wep_checkbox')){
    function c9wep_checkbox($name, $options=array(),$current_value_arr=array(),$label,$required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
    <div class="checkbox-wrapper <?php echo $id; ?>-checkbox-wrapper">
    <?php foreach ($options as $value => $label): ?>
        <?php 
            $checked='';
            if(in_array($value,$current_value_arr) !== false){//find the value
                $checked=' checked="checked"';
            }
         ?>
            <label>
                <input name="<?php echo $name . '[]'; ?>" type="checkbox" value="<?php echo $value; ?>" <?php echo $checked; ?>>
                <?php echo $label; ?>
            </label><br/>
    <?php endforeach ?>
    </div>
<?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_checkbox() 
}//end if !function_exists('c9wep_checkbox')

if(!function_exists('c9wep_group_checkbox')){
    function c9wep_group_checkbox($name, $options_groups=array(),$current_value_arr=array(),$label,$required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
    <div class="checkbox-wrapper <?php echo $id; ?>-checkbox-wrapper">
        <?php foreach ($options_groups as $group_name => $options): ?>
            <h4 class="group-name">
            <?php
                echo $group_name;
            ?>
            </h4>
            <?php foreach ($options as $value => $label): ?>
                <?php 
                    $checked='';
                    if(in_array($value,$current_value_arr) !== false){//find the value
                        $checked=' checked="checked"';
                    }
                 ?>
                    <label>
                        <input name="<?php echo $name . '[]'; ?>" type="checkbox" value="<?php echo $value; ?>" <?php echo $checked; ?>>
                        <?php echo $label; ?>
                    </label><br/>
            <?php endforeach ?>
        <?php endforeach ?>
    </div>
<?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_group_checkbox() 
}//end if !function_exists('c9wep_group_checkbox')

if(!function_exists('c9wep_select')){
    function c9wep_select($name,$options=array(),$current_value='',$label,$required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="select-wrapper <?php echo $id; ?>-select-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty($label) ?>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control">
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
    }//end c9wep_select() 
}//end if !function_exists('c9wep_select')
if(!function_exists('c9wep_multi_select')){
    function c9wep_multi_select($name,$options=array(),$current_value_arr=array(),$label,$required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="multi-select-wrapper <?php echo $id; ?>-multi-select-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty($label) ?>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" multiple="multiple" name="<?php echo $name; ?>[]" id="<?php echo $name; ?>" class="form-control">
                <?php foreach ($options as $value => $label): ?>
                    <?php 
                        $selected='';
                        if(in_array($value,$current_value_arr) !== false){//find the value
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
    }//end c9wep_multi_select() 
}//end if !function_exists('c9wep_multi_select')

if(!function_exists('c9wep_group_select')){
    function c9wep_group_select($name,$group_options=array(),$current_value='',$label,$required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="select-wrapper <?php echo $id; ?>-select-wrapper">
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control">
                <?php foreach ($group_options as $group_label => $options): ?>
                    <?php if(!empty($group_label)): ?>
                        <optgroup label="<?php echo $group_label; ?>">
                    <?php endif;//end !empty($group_label) ?>
                    <?php foreach ($options as $value => $label): ?>
                        <?php 
                            $selected='';
                            if($current_value==$value){
                                $selected=' selected="selected"';
                            }
                         ?>
                        <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $label; ?></option>
                    <?php endforeach ?>
                    <?php if(!empty($group_label)): ?>
                        </optgroup>
                    <?php endif;//end !empty($group_label) ?>
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
    }//end c9wep_group_select() 
}//end if !function_exists('c9wep_group_select')

if(!function_exists('c9wep_group_multi_select')){
    function c9wep_group_multi_select($name,$group_options=array(),$current_value_arr=array(),$label,$required=true,$tips='')
    {
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="multi-select-wrapper <?php echo $id; ?>-multi-select-wrapper">
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" multiple="multiple" name="<?php echo $name; ?>[]" id="<?php echo $name; ?>" class="form-control">
                <?php foreach ($group_options as $group_label => $options): ?>
                    <?php if(!empty($group_label)): ?>
                        <optgroup label="<?php echo $group_label; ?>">
                    <?php endif;//end !empty($group_label) ?>
                    <?php foreach ($options as $value => $label): ?>
                        <?php 
                            $selected='';
                            if(in_array($value,$current_value_arr) !== false){//find the value
                                $selected=' selected="selected"';
                            }
                         ?>
                        <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $label; ?></option>
                    <?php endforeach ?>
                    <?php if(!empty($group_label)): ?>
                        </optgroup>
                    <?php endif;//end !empty($group_label) ?>
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
    }//end c9wep_group_multi_select() 
}//end if !function_exists('c9wep_group_multi_select')

