<?php
// A template file used for loading all HTML page header elements

$this->load->helper('url');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Седмата посока</title>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/style.css" />
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/html5shiv.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.slides.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/script.js"></script>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
    </head>
    <body>


        <div class="wrapAll">
            <header class="wrapHeader">
                <div class="clear">
                    <a class="logo" href="" ><img src="<?php echo base_url()?>img/logo_sedmata_posoka.png" alt="logo" /></a>	
                    <div class="mindHolder">
                        <?php
                        echo '<p>"' . $tought[0]['text'] . '"<span class="author"> ' . $tought[0]['author'] . '</span></p>';
                        ?>
                    </div>
                    <div class="bubbles"></div>
                    <div class="rightPart">
                        <section class="language clear">	
                            <a href="#" class="en"></a>
                            <a href="#" class="bg"></a>
                        </section>
                        <section class="search">
                            <input class="searchTxt" type="search" />
                            <input class="searchBttn" type="submit" value="" />
                        </section>
                        <section class="socials clear">
                            <a class="email" href=""></a>
                            <a class="fb" href=""></a>
                        </section>
                    </div>
                </div>
            </header>
            <section class="wrapMain">
                <div class="clear">
                    <div class ="wrapMainLeft">
                        <h2>Каталог</h2>
                        <nav class ="navMain">
                            <?php
                            $this->load->helper('url');

                            foreach ($categories as $category) {
                                // echo '<h1>' . $category['name'] . '</h1>';
                                echo '<a href="' . base_url() . 'category/' . $category['id'] . '">' .
                                $category['name'] . ' </a>';
                                $menu_elements = '';
                                for ($i = 0; $i < sizeof($subcategories); $i++) {
                                    if ($subcategories[$i]['catId'] === $category['id']) {
                                        $menu_elements .= '<p>' . '<a href="' . base_url() . 'subcategory/' . $subcategories[$i]['id'] . '" >'
                                                . $subcategories[$i]['name'] . '</a>' . '</p>';
                                    }
                                }

                                if (strlen($menu_elements)) {
                                    echo '<div class="navMainSubcategory">' . $menu_elements . '</div>';
                                }
                            }
                            ?>
                        </nav>
                    </div>
                    <div class ="wrapMainMiddle">