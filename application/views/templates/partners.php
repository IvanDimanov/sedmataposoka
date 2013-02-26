<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<p>Partners list</p>

<?php
foreach ($partners as $partner) {
    echo '<h1>' . $partner['name'] . '</h1>';
    echo '<img src="' . $partner['logoSrc'] . '" alt="logoImg" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $partner['link'] . '">View our partner</a>';
}

?>