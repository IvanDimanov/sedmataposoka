<?php


// Undefined | Multiple Files | $_FILES Corruption Attack
// If this request falls under any of them, treat it invalid.
if (!isset(  $_FILES['file']['error']) ||
    is_array($_FILES['file']['error'])
) {
  die('Invalid parameters');
}

// Check $_FILES['file']['error'] value.
switch ($_FILES['file']['error']) {
  case UPLOAD_ERR_OK       : break;
  case UPLOAD_ERR_NO_FILE  : die('No file sent');
  case UPLOAD_ERR_INI_SIZE :
  case UPLOAD_ERR_FORM_SIZE: die('Exceeded file size limit');
  default                  : die('Error during upload');
}

// You should also check filesize here.
if ($_FILES['file']['size'] > 1000000) {
  die('Exceeded files ize limit');
}

$uploaded_file_path = './uploads/test.file';

// You should name it uniquely.
// DO NOT USE $_FILES['file']['name'] WITHOUT ANY VALIDATION !!
// On this example, obtain safe unique name from its binary data.
if (!move_uploaded_file( $_FILES['file']['tmp_name'], $uploaded_file_path )) {
  die('Failed to move uploaded file');
}

echo 'File is uploaded successfully as "'.$uploaded_file_path.'"';
