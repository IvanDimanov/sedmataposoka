<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<p>Thought of the day</p>

<?php
echo '"' . $tought[0]['text'] . '" ' . $tought[0]['author'];
?>

<br/><p1>ADDS list</p1>

<?php
//Display ads only for current day
foreach ($ads as $add) {
    echo '<h1>' . $add['title'] . '</h1>';
    echo '<img src="' . $add['imagePath'] . '" alt="' . $add['title'] . '" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $add['link'] . '">Ads link</a>';
}
?>

<h1> Banner </h1>
<?php
foreach ($banners as $banner) {
    echo '<h1>' . $banner['title'] . '</h1>';
    echo '<img src="' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $banner['link'] . '">Banner Link</a>';
}
?>


<h1>Catalog</h1>
<?php
$this->load->helper('url');

foreach ($categories as $category) {
    // echo '<h1>' . $category['name'] . '</h1>';
    echo '<a href="' . base_url() . 'category/index/' . $category['id'] . '">' .
    $category['name'] . ' </a></br>';
    for ($i = 0; $i < sizeof($subcategories); $i++) {
        if ($subcategories[$i]['catId'] === $category['id']) {
            echo '<h2>' . $subcategories[$i]['name'] . '</h2>';
        }
    }
}
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