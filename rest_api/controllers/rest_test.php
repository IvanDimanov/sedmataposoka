<?php
/*This file is been used as a Proof-of-concept for 'rooter.php' logic*/



require_once('./models/db_manager.php');
$db = getDBConnection();



/*
$query = $db->prepare('
  UPDATE admin
  SET password = sha( concat("7@posoka", salt))
  where id = 1

');
$query->execute();
print_r( $query->errorInfo() );
/**/


/**/
$query = $db->prepare('SELECT * FROM admin where password = sha( concat("7@posoka", salt))');
$query->execute();

$result = $query->fetchAll(PDO::FETCH_ASSOC);

print_r( $result );
/**/

