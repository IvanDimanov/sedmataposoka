<?php
/*This file is been used as a Proof-of-concept for 'rooter.php' logic*/



require_once('./models/db_manager.php');
$db = getDBConnection();



/*
$query = $db->prepare('
  UPDATE admin
  SET password = sha( concat("6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af", salt))
  where id = 1

');
$query->execute();
print_r( $query->errorInfo() );
/**/


/**/
$query = $db->prepare('SELECT * FROM admin where password = sha( concat("6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af", salt))');
$query->execute();

$result = $query->fetchAll(PDO::FETCH_ASSOC);

print_r( $result );
/**/

