<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h1> Banner </h1>
<?php
echo '<h1>' . $banner[0]['title'] . '</h1>';
    echo '<img src="' . $banner[0]['imagePath'] . '" alt="'.$banner[0]['title'].'" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $banner[0]['link'] . '">Banner Link</a>';
    ?>