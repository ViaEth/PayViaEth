<?php
function c9wep_get_fields_line( $fields=array() ) {
    $fields_line='*';
    if(!empty($fields)){
        $fields_line=implode("`,`",$fields);
        $fields_line="`" . $fields_line . "`";
    }
    return $fields_line;
}

function c9wep_get_where($args=array()){
    global $wpdb;
    if(!empty($args)){
        $results=array();
        foreach ($args as $field => $value) {
            $field=trim($field,'_');
            if(strpos($value,'*') !== false){
                $value=trim($value,'*');
                $results[]=$wpdb->prepare("`$field` like '%%%s%%'",$value);
            }else if(strpos($value,'>=') !== false){
                $value=trim($value,'>=');
                $results[]=$wpdb->prepare("`$field`>=%s",$value);
            }else if(strpos($value,'<=') !== false){
                $value=trim($value,'<=');
                $results[]=$wpdb->prepare("`$field`<=%s",$value);
            }else if(strpos($value,'>') !== false){
                $value=trim($value,'>');
                $results[]=$wpdb->prepare("`$field`>%s",$value);
            }else if(strpos($value,'<') !== false){
                $value=trim($value,'<');
                $results[]=$wpdb->prepare("`$field`<%s",$value);
            }else if(strpos($value,'!=') !== false){
                $value=trim($value,'!=');
                $results[]=$wpdb->prepare("`$field` != %s",$value);
            }else if(strpos($value,'not_in_') !== false){ //input value format in_color1;nice,tome;color3;
                $value=trim($value,'not_in_');
                $in_values=explode(";",$value);
                $in_querys=array();
                foreach ($in_values as $k=>$v) {
                    $in_querys[]=$wpdb->prepare("%s",$v);
                }

                $results[]="`$field` NOT IN (".implode(",",$in_querys).")";
            }else if(strpos($value,'age_or_') !== false){ //input value format in_color1;nice,tome;color3;
                $value=str_replace('age_or_','',$value);
                $in_values=explode(";",$value);
                $in_querys=array();
                // ob_start();
                // print_r($in_values);
                // echo PHP_EOL;
                // echo PHP_EOL;
                // echo PHP_EOL;
                // echo PHP_EOL;
                // $data1=ob_get_clean();
                // file_put_contents(dirname(__FILE__) . '/in_values.log',$data1,FILE_APPEND);
                foreach ($in_values as $k=>$age_range) {
                    list($min,$max)=explode("_",$age_range,2);
                    $in_querys[]=$wpdb->prepare("(`$field`>=%s AND `$field`<=%s)",$min, $max);//$wpdb->prepare("`$field` like '%%%s%%'",$v);//$wpdb->prepare("%s",$v);
                }

                $results[]="(".implode(" OR ",$in_querys).")";
            }else if(strpos($value,'like_') !== false){ //input value format in_color1;nice,tome;color3;
                $value=str_replace('like_','',$value);//str_replace($value,'like_');
                $in_values=explode(";",$value);
                $in_querys=array();
                foreach ($in_values as $k=>$v) {
                    $in_querys[]=$wpdb->prepare("`$field` like '%%%s%%'",$v);//$wpdb->prepare("%s",$v);
                }

                $results[]="(".implode(" OR ",$in_querys).")";
            }else if(strpos($value,'in_') !== false){ //input value format in_color1;nice,tome;color3;
                $value=str_replace('in_','',$value);//trim($value,'in_');
                $in_values=explode(";",$value);
                $in_querys=array();
                foreach ($in_values as $k=>$v) {
                    $in_querys[]=$wpdb->prepare("%s",$v);
                }

                $results[]="`$field` IN (".implode(",",$in_querys).")";
            }else{
                $results[]=$wpdb->prepare("`$field`=%s",$value);
            }
        }

        return ' WHERE ' . implode(' AND ',$results);
    }
    return '';
}

function c9wep_cache_key($args=array()){
    return md5(serialize($args));
}
