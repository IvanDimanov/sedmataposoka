<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//all categories, subcategories
?>
<h1>Catalog</h1>
<?php
foreach ($categories as $category) {
    echo '<h1>' . $category['name'] . '</h1>';
    for ($i = 0; $i < sizeof($subcategories); $i++) {
        if ($subcategories[$i]['catId'] === $category['id']) {
            echo '<h2>' . $subcategories[$i]['name'] . '</h2>';
        }
    }
    
    
}


?>