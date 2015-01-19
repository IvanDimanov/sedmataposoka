

<?php
// Dummy page used for template testing only
?>
<?php include('templates/header.php'); ?>

<h1>That's our first PHP index page</h1>

<?php

function printLayout($this, $layout_name) {
    //v header-a se zarejda catalog ads baner thought of day 
    $this->load->view('templates/header',$data);
    $this->load->view( $layout_name );
    $this->load->view( $layout_name );
    $this->load->view('templates/footer');
}


/* foreach($queries as $query): ?>
  <h1><?php echo $query; ?></h2>
  <?php endforeach;?>

  <?php
  foreach($news as $newsItem): ?>
  <h1><?php echo $newsItem['newsName']; ?></h1>
  <p><?php echo $newsItem['newsContent']; ?></p>
  <?php echo $newsItem['newsDate'];
  endforeach;

 */
echo 'testview thought </br>';
//var_dump($thought);
echo '"' . $thought[0]['text'] . '" ' . $thought[0]['author'];


foreach ($partners as $partner) {
    echo '<h1>' . $partner['name'] . '</h1>';
    echo '<img src="' . $partner['logoSrc'] . '" alt="logoImg" height="42" width="42">';
    //TODO redirection to partner link
    echo '<a href="' . $partner['link'] . '">View our partner</a>';
}
$this->load->helper('html');
$image_properties = array(
    'src' => 'img/' . $partner['logoSrc'] . '',
    'alt' => 'logo',
    'class' => 'post_images',
    'width' => '200',
    'height' => '200',
    'title' => 'That was quite a night',
    'rel' => 'lightbox',
);

img($image_properties);


$i = 0;

//print left panel categories and subcategories
foreach ($categories as $category) {
    echo '<h1>' . $category['name'] . '</h1>';
    for ($i = 0; $i < sizeof($subcategories); $i++) {
        if ($subcategories[$i]['catId'] === $category['id']) {
            echo '<h2>' . $subcategories[$i]['name'] . '</h2>';
        }
    }
}
?>






<?php include('templates/footer.php'); ?>

