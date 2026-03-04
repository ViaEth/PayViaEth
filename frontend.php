<?php
function pve_get_template_files($template_name,$location=false){
    $template_in_plugin=PVE_DIR . '/pve-templates/' . $template_name;
    $template_in_theme=get_theme_file_path() . '/pve-templates/' . $template_name;
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
}//end pve_get_template_files() 
