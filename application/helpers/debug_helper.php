<?php


// For printing Warnings and Notes
/***
error_reporting(E_ALL);
ini_set('display_errors', '1');
/***/



function lg($variable, $delitel = '', $print_variable_name = true) {
// getting the executed file and the executed line as a string
// example "lg($the_input_value)"
// getting everything between ( and )

 $function_name = 'lg';

 if ($print_variable_name) {
  $caller_info    = array_shift(debug_backtrace());
  $executed_file  = file($caller_info['file']);
  $executed_lines = get_executed_lines($executed_file, $caller_info['line'], $function_name);

  $start  = strpos($executed_lines, $function_name.'(') + strlen($function_name)+1;
  $end_1  = strpos($executed_lines, ');', $start);
  $end_2  = strpos($executed_lines, ',' , $start);
  $end    = ($delitel) ? $end_2 : $end_1;

  $length = $end - $start;

  $variable_name            = substr($executed_lines, $start, $length);
  $variable_name_expression = ' '.trim($variable_name).' = ';

 } else {
  $variable_name_expression = '';
 }


 if(gettype($variable) == "array") {
  echo "<pre>$variable_name_expression".recursive_get_array_view($variable, $delitel)." </pre>
";

 } elseif (gettype($variable) == "boolean") {
  echo "<pre>$variable_name_expression$delitel".($variable ? 'true' : 'false')."$delitel <br /></pre>
";

 } else {
  echo "<pre>$variable_name_expression".callable_check($variable, $delitel)." <br /></pre>
";
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
              : callable_check($variable, $delitel);
  
  $content .= "\n$level_tabulator  [$key] => $variable";
 }
 $content .= "\n$level_tabulator )";
 
 return $content;
}


function callable_check($variable, $delitel) {
  return is_callable($variable) ? 'function() {...}' : $delitel.$variable.$delitel;
}