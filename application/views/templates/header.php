<?php
// A template file used for loading all HTML page header elements

$this->load->helper('url');

/*Detect any requests coming as URL strings*/
$query = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';

/*Secure at least language URL var*/
$uri_string = $this->uri->uri_string();
$uri_string = $uri_string ? $uri_string : $language;

/*Combine the final URL link & remove the not needed 'index.php' location*/
$full_url = $this->config->site_url().'/'.$uri_string.$query;
$full_url = str_replace('/index.php', '', $full_url);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?=$ui_labels['page_title']?></title>
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/style.css" />
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/html5shiv.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery.slides.min.js"></script>

        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>js/jquery-ui-1.10.2.custom/css/ui-darkness/jquery-ui-1.10.2.custom.css"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>js/jquery-ui-1.10.2.custom/css/ui-darkness/jquery-ui-1.10.2.custom.min.css"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url();?>js/script.js"></script>
    </head>
    <body>
        <div class="wrapAllMain">
        <div class="wrapAll">
            <header class="wrapHeader">
                <div class="clear">
                    <a class="logo" href="<?php echo base_url().$language;?>/" ><img src="<?php echo base_url()?>img/logo_sedmata_posoka.png" alt="logo" /></a>	
                    <div class="mindHolder">
                        <?php
                        echo '<p>"' . $tought[0]['text'] . '"<span class="author"> ' . $tought[0]['author'] . '</span></p>';
                        ?>
                    </div>
                    <div class="bubbles"></div>
                    <div class="rightPart">
                        <section class="language clear">	
                            <a href="<?php echo str_replace('/bg', '/en', $full_url); ?>" class="en"></a>
                            <a href="<?php echo str_replace('/en', '/bg', $full_url); ?>" class="bg"></a>
                        </section>
						<section id="searchHolder" class="search">
							<form id="formSearch" name="formSearch" action="" method="post" onsubmit="return searchByWord();">
								<input class="searchTxt" id="searchTxt" name="searchTxt" type="search" />
								<input class="searchBttn" id="searchBttn" type="submit" value="" />
							</form>
						</section>
                        <section class="socials clear">
                            <a class="email" href="<?=base_url().$language?>/contacts"></a>
                            <a class="fb" href=""></a>
                        </section>
                    </div>
                </div>
            </header>
            <section class="wrapMain">
                <div class="clear">
                    <div class ="wrapMainLeft">
                        <nav class ="navMain">
							<ul class="level1">
                            <?php
                            $this->load->helper('url');
                            foreach ($categories as $category) {
                                // echo '<h1>' . $category['name'] . '</h1>';
                                echo '<li class="level1Li"><a class="level1Link" href="' . base_url() .$language.'/'. 'category/' . $category['id'] . '">' .
                                $category['name'] . ' </a>';
                                $menu_elements = '';
                                for ($i = 0; $i < sizeof($subcategories); $i++) {
                                    if ($subcategories[$i]['catId'] === $category['id']) {
                                        $menu_elements .= '<li>' . '<a href="' . base_url().$language.'/' . 'subcategory/' . $subcategories[$i]['id'] . '" >'
                                                . $subcategories[$i]['name'] . '</a>' . '</li>';
                                    }
                                }

                                if (strlen($menu_elements)) {
                                    echo '<ul class="level2">' . $menu_elements . '</ul>';
                                }
								echo '</li>';
                            }
                            ?>
							</ul>
                        </nav>
						<div class="datePickerHolder">
							<div id="datepicker"></div>
						</div>
                    </div>
                    <div class ="wrapMainMiddle">