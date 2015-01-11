<?php
/*This file is been used as a Proof-of-concept for 'rooter.php' logic*/

header('HTTP/1.1 200 OK');
echo '{"name":"'.$request['controller_name'].'"}';