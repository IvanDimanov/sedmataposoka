<?php
/*This file will expose a set of helper function*/


/*Loop through all '$array' properties and 'trim()' each string*/
/*NOTE: Looping is made recursively*/
function trimAll(&$array) {
  if (gettype($array) != 'array') return;

  foreach ($array as &$property) {
    switch (gettype($property)) {
      case 'string':
        $property = trim($property);
      break;

      case 'array':
        return trimAll( $property );
      break;
    }
  }
}


/*
  Will implement 'array_replace_recursive' if same not exist as http://php.net/manual/en/function.array-replace-recursive.php
  Source: http://stackoverflow.com/questions/2874035/php-array-replace-recursive-alternative
*/
if (!function_exists('array_replace_recursive'))
{
  function array_replace_recursive($array, $array1)
  {
    function recurse($array, $array1)
    {
      foreach ($array1 as $key => $value)
      {
        // create new key in $array, if it is empty or not an array
        if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key])))
        {
          $array[$key] = array();
        }

        // overwrite the value in the base array
        if (is_array($value))
        {
          $value = recurse($array[$key], $value);
        }
        $array[$key] = $value;
      }
      return $array;
    }

    // handle the arguments, merge one by one
    $args = func_get_args();
    $array = $args[0];
    if (!is_array($array))
    {
      return $array;
    }
    for ($i = 1; $i < count($args); $i++)
    {
      if (is_array($args[$i]))
      {
        $array = recurse($array, $args[$i]);
      }
    }
    return $array;
  }
}


/*
  Will return a pseudo-random string in a given length.
  Source: http://stackoverflow.com/questions/4356289/php-random-string-generator
*/
function getRandomString($length) {
  if (gettype($length) != 'integer' ||
      $length < 1
  ) {
    $length = 10;
  }

  $characters    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
  $random_string = '';
  while ($length--) {
    $random_string .= $characters[mt_rand(0, strlen($characters) - 1)];
  }

  return $random_string;
}


/*Will solve the problem of
  json_encode(array('category' => 'мебели'))          =>  {"category":"\u043c\u0435\u0431\u0435\u043b\u0438"}
to
  json_unicode_encode(array('category' => 'мебели'))  =>  {"category":"мебели"} 
*/
function json_unicode_encode($struct) {
   return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct) );
}


/*
  Since 'array_merge_recursive()' merge 2 arrays but keep both values in a value collision.
  This function will merge the incoming two arrays and
  will overwrite the values of the 1st array with the values of the 2nd array.
  Source:
    http://php.net/manual/en/function.array-merge-recursive.php#92195
*/
function array_merge_recursive_distinct(array &$array1, array &$array2) {
  $merged = $array1;

  foreach ($array2 as $key => &$value) {
    if (is_array( $value ) && isset( $merged[$key] ) && is_array( $merged[$key] )) {
      $merged[$key] = array_merge_recursive_distinct( $merged[$key], $value );
    } else {
      $merged[$key] = $value;
    }
  }

  return $merged;
}


/*Correctly boolean cast of common case variables*/
function getCommonBoolean($variable) {
  if (gettype($variable) == 'boolean') {
    return $variable;
  }

  if ($variable === 'true' ||
      $variable === '1'    ||
      $variable === 1
  ) {
      return true;
  }

  if ($variable === 'false' ||
      $variable === '0'     ||
      $variable === 0
  ) {
      return false;
  }
}


/*
  Tries to convert incoming date into the given format.
  Returns formated date into string or false;

  Examples:
    getValidDate('2013-02-17')  =>  '2013-02-17 00:00:00 '
    getValidDate('2013-02-47')  =>  false
*/
function getValidDate($test_date_string = false, $format = 'Y-m-d H:i:s') {
  $valid_date_string = false;

  try {
    $date              = new DateTime( $test_date_string );
    $valid_date_string = $date->format( $format );
  } catch (Exception $error) {
    /*No action needed since '$valid_date_string' is still 'false'*/
  }

  return $valid_date_string;
}