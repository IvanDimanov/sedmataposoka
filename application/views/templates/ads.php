<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<br/><p1>ADDS list</p1>
<?php
echo '<h1>' . $ads[0]['title'] . '</h1>';
    echo '<img src="' . $ads[0]['imagePath'] . '" alt="'.$ads[0]['title'].'" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $ads[0]['link'] . '">Ads link</a>';
    
    ?>