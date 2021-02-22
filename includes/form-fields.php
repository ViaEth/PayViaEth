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
    function c9wep_convertCamelCaseName($name){
        if(!empty($name)){
            $name=preg_replace('/([A-Z]{1,20})/','-\1',$name);
            return $name;
        }
        return false;
    }
}//end if !function_exists('c9wep_convertCamelCaseName')

if(!function_exists('c9wep_getLowCaseIdName')){
    function c9wep_getLowCaseIdName($name){
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
            /*
            $args=[
                'name'=>'',
                'type'=>'',
                'id'=>'',
                'class'=>'',
                'label'=>'',
                'layout'=>'',//one_row, default is empty(two rows)
                'data_attr'=>[],//like [order_id=>190]
                'options'=>[],//could be group or not group options
                'values'=>[],
                'default_values'=>[],
                'value'=>'',
                'default_value'=>'',
                'min'=>'',
                'max'=>'',
                'accept'=>'*.*',//for file upload
                'placeholder'=>'',
                'required'=>true,
                'tips'=>'',
            ];
            */
            $type='text';
            if(!empty($args['type'])){
                $type=$args['type'];
            }
            $fun=$ns . $type;
            $fields_html[]=$fun($args);
        } 
        return implode(PHP_EOL,$fields_html);
    }//end c9wep_render_fields() 
}//end if !function_exists('c9wep_render_fields')
if(!function_exists('c9wep_hidden')){
    function c9wep_hidden($args=[]){
        extract($args);
        ob_start();
        ?>
            <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>"/>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_hidden() 
}//end if !function_exists('c9wep_hidden')

if(!function_exists('c9wep_datetimepicker')){
    function c9wep_datetimepicker($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="datetimepicker-wrapper <?php echo $id; ?>-wrapper">
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-text <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
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
    function c9wep_file_upload($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="file-upload-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty() ?>
      <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="file" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-file-upload <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>"  accept="<?php echo $accept; ?>" <?php echo $required_attr; ?> /> 
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
    function c9wep_text($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="text-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label>
            <?php if('one_row'!=$layout): ?>
            <br/>
            <?php endif;//end 'one_row'!=$layout ?>
            <?php endif;//end !empty() ?>
        <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-text <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
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

if(!function_exists('c9wep_number')){
    function c9wep_number($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="number-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label>
            <?php endif;//end !empty() ?>
        <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="number" min="0" max="9999" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-number <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_number() 
}//end if !function_exists('c9wep_number')

if(!function_exists('c9wep_tel')){
    function c9wep_tel($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="tel-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty() ?>
        <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="tel" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-tel <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_tel() 
}//end if !function_exists('c9wep_tel')

if(!function_exists('c9wep_email')){
    function c9wep_email($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="email-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty() ?>
	    <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <input type="email" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-email <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" value="<?php echo $value; ?>" <?php echo $required_attr; ?> />
            <?php 
                if(!empty($tips)){
                    echo "<p style=\"margin:0px;\">$tips</p>";
                }
            ?>
        </div>
        <?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_email() 
}//end if !function_exists('c9wep_email')

if(!function_exists('c9wep_textarea')){
    function c9wep_textarea($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        ?>
        <div class="textarea-wrapper <?php echo $id; ?>-wrapper">
            <label for="<?php echo $id; ?>"><b><?php _e($label, 'c9wep' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <textarea name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="regular-textarea <?php echo $class; ?>" placeholder="<?php echo esc_attr( '', 'c9wep' ); ?>" <?php echo $required_attr; ?> rows="3" cols="40"  /><?php echo $value; ?></textarea>
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

if(!function_exists('c9wep_radio')){
    function c9wep_radio($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
    <div class="radio-wrapper <?php echo $id; ?>-wrapper">
    <?php foreach ($options as $init_value => $label): ?>
        <?php 
            $checked='';
            if($init_value == $value ){//find the value
                $checked=' checked="checked"';
            }
         ?>
            <label>
                <input name="<?php echo $name; ?>" type="radio" class="regular-radio <?php echo $class; ?>" value="<?php echo $init_value; ?>" <?php echo $checked; ?>>
                <?php echo $label; ?>
            </label>
    <?php endforeach ?>
    </div>
<?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_radio() 
}//end if !function_exists('c9wep_radio')

if(!function_exists('c9wep_checkbox_single')){
    function c9wep_checkbox_single($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
        $checked='';
        $checked_value='yes';
        if($checked_value==$value){
            $checked='checked="checked"';
        }
    ?>
    <div class="checkbox-single-wrapper <?php echo $id; ?>-wrapper">
        <label><b>
            <input class="<?php echo $class; ?>" type="checkbox" id="<?php echo $name; ?>" <?php echo $checked; ?> name="<?php echo $name; ?>" value="<?php echo $checked_value; ?>"><?php echo $label; ?></b>
        </label>
    </div>
<?php
        $html=ob_get_clean();
        return $html;
    }//end c9wep_checkbox_single() 
}//end if !function_exists('c9wep_checkbox_single')

if(!function_exists('c9wep_checkbox')){
    function c9wep_checkbox($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
    <div class="checkbox-wrapper <?php echo $id; ?>-wrapper">
    <?php foreach ($options as $value => $label): ?>
        <?php 
            $checked='';
            if(in_array($value,$values) !== false){//find the value
                $checked=' checked="checked"';
            }
         ?>
            <label>
                <input class="<?php echo $class; ?>" name="<?php echo $name . '[]'; ?>" type="checkbox" value="<?php echo $value; ?>" <?php echo $checked; ?>>
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
    function c9wep_group_checkbox($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
    <div class="checkbox-wrapper <?php echo $id; ?>-wrapper">
        <?php foreach ($options as $group_name => $sub_options): ?>
            <h4 class="group-name">
            <?php
                echo $group_name;
            ?>
            </h4>
            <?php foreach ($sub_options as $value => $label): ?>
                <?php 
                    $checked='';
                    if(in_array($value,$values) !== false){//find the value
                        $checked=' checked="checked"';
                    }
                 ?>
                    <label>
                        <input class="<?php echo $class; ?>" name="<?php echo $name . '[]'; ?>" type="checkbox" value="<?php echo $value; ?>" <?php echo $checked; ?>>
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
    function c9wep_select($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="select-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty($label) ?>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control <?php echo $class; ?>">
                <?php foreach ($options as $value => $label): ?>
                    <?php 
                        $selected='';
                        if($value==$value){
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
    function c9wep_multi_select($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="multi-select-wrapper <?php echo $id; ?>-wrapper">
            <?php if(!empty($label)): ?>
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php endif;//end !empty($label) ?>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" multiple="multiple" name="<?php echo $name; ?>[]" id="<?php echo $name; ?>" class="form-control <?php echo $class; ?>">
                <?php foreach ($options as $value => $label): ?>
                    <?php 
                        $selected='';
                        if(in_array($value,$values) !== false){//find the value
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
    function c9wep_group_select($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="select-wrapper <?php echo $id; ?>-wrapper">
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control <?php echo $class; ?>">
                <?php foreach ($options as $group_label => $options): ?>
                    <?php if(!empty($group_label)): ?>
                        <optgroup label="<?php echo $group_label; ?>">
                    <?php endif;//end !empty($group_label) ?>
                    <?php foreach ($options as $value => $label): ?>
                        <?php 
                            $selected='';
                            if($value==$value){
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
    function c9wep_group_multi_select($args=[]){
        extract($args);
        $id=c9wep_getLowCaseIdName($name);
        ob_start();
?>
        <div class="multi-select-wrapper <?php echo $id; ?>-wrapper">
            <label for="<?php echo $name; ?>"><b><?php _e($label, 'c9ic' ); ?><?php if($required){echo '(*)';} ?></b></label><br/>
            <?php 
                $required_attr='';
                if($required){
                    $required_attr='required="required"';
                }
            ?>
            <select autocomplete="off" multiple="multiple" name="<?php echo $name; ?>[]" id="<?php echo $name; ?>" class="form-control <?php echo $class; ?>">
                <?php foreach ($options as $group_label => $sub_options): ?>
                    <?php if(!empty($group_label)): ?>
                        <optgroup label="<?php echo $group_label; ?>">
                    <?php endif;//end !empty($group_label) ?>
                    <?php foreach ($sub_options as $value => $label): ?>
                        <?php 
                            $selected='';
                            if(in_array($value,$values) !== false){//find the value
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

