<?php
function c9wep_string_line_formatter($string_line){
  if(empty($string_line)) return false;

  $clean_dash_name=preg_replace('/[^A-Z0-9]+/i','-',$string_line);
  $clean_dash_name=trim($clean_dash_name,'-');

  $clean_id_name=preg_replace('/[^A-Z0-9]+/i','_',$string_line);
  $clean_id_name=trim($clean_id_name,'_');

  $parts=explode("_",$clean_id_name);
  $parts=array_map('ucfirst',$parts);
  $name=implode("_",$parts);
  $class_name=$name;

  $low_case_class_name=lcfirst($class_name);

  $id=strtolower($clean_id_name);

  $constant=strtoupper($clean_id_name);

  $dash_id=strtolower($clean_dash_name);

  $parts=explode('-',$clean_dash_name);
  $parts=array_map('ucfirst',$parts);
  //for some special strings
  foreach ($parts as $key => $part) {
      if('Id'==$part){
          $parts[$key]='ID';
      }
  }
  $title=implode(' ',$parts);

  $result=compact('constant','id','dash_id','title');

  return $result;
}