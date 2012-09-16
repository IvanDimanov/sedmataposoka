<?php


// For printing Warnings and Notes
/***/
error_reporting(E_ALL);
ini_set('display_errors', '1');
/***/



function _l($variable, $delitel = '') {
// getting the executed file and the executed line as a string
// example "_l($the_input_value)"
// getting everything between ( and )

 $function_name = '_l';

 $caller_info    = array_shift(debug_backtrace());
 $executed_file  = file($caller_info['file']);
 $executed_lines = get_executed_lines($executed_file, $caller_info['line'], $function_name);

 $start  = strpos($executed_lines, $function_name.'(') + strlen($function_name)+1;
 $end_1  = strpos($executed_lines, ');', $start);
 $end_2  = strpos($executed_lines, ',' , $start);
 $end    = ($delitel) ? $end_2 : $end_1;

 $length = $end - $start;

 $variable_name = substr($executed_lines, $start, $length);
 $variable_name = trim($variable_name);


 if(gettype($variable) == "array") {
  echo "<pre> $variable_name = ".recursive_get_array_view($variable, $delitel)." </pre>";

 } else {
  
  echo "<pre> $variable_name = $delitel".callable_check($variable)."$delitel <br /></pre>";
 }
}



function get_executed_lines($file, $line_number, $function_name) {
 $line = '';
 do {
  $line = $file[--$line_number].$line;
 } while(!strstr($line, $function_name));

 return $line;
}



function recursive_get_array_view($array, $delitel = '', $level_tabulator = '') {
 $content = "Array (";
 foreach($array as $key => $variable) {
  $variable = (gettype($variable) == 'array')
              ? recursive_get_array_view($variable, $delitel, "\t".$level_tabulator)
              : callable_check($variable);
  
  $content .= "\n$level_tabulator  [$key] => $variable";
 }
 $content .= "\n$level_tabulator )";
 
 return $content;
}


function callable_check($variable) {
  return is_callable($variable) ? 'function() {...}' : $variable;
}
