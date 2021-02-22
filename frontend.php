<?php
function c9wep_get_template_files($template_name,$location=false){
    // $template_name='my-submissions.php';
    $template_in_plugin=C9WEP_DIR . '/c9wep-templates/' . $template_name;
    $template_in_theme=get_theme_file_path() . '/c9wep-templates/' . $template_name;
    if(file_exists($template_in_theme)){
        $using=$template_in_theme;
    }else if(file_exists($template_in_plugin)){
        $using=$template_in_plugin;
    }

    if($location){
        return array('plugin'=>$template_in_plugin,'theme'=>$template_in_theme,'using'=>$using);
    }else{
        return $using;
    }
}//end c9wep_get_template_files() 