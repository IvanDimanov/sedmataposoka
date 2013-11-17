<?php
// A template file used for loading all HTML page footer elements
$this->load->helper('url');
?>
</div>
<div class ="wrapMainRight">
    <aside>
        <?php
        //Display ads only for current day
        foreach ($ads as $add) {
            echo '<div class ="advHolder">';
            //TODO redirection to partner link
            echo '<a href="' . $add['link'] . '">';
            if ($add['title']) echo '<p>'.$add['title'].'</p>';
            echo '<img src="'.  base_url().'img/' . $add['imagePath'] . '" alt="' . $add['title'] . '" />';
            echo '</a>';
            echo '</div>';
        }
        ?>				
    </aside>
    <div class="partnersMainHolder">
        <h3><?=$ui_labels['partners']['list']?></h3>
        <?php
        foreach ($partners as $partner) {
            echo '<h4>' . $partner['name'] . '</h4>';
            //echo '<img src="' . $partner['logoSrc'] . '" alt="logoImg" height="42" width="42">';
            //TODO redirection to partner link
           // echo '<a href="' . $partner['link'] . '">View our partner</a>';
        }
        echo '<a href="' . base_url().$language.'/partners'.'">'.$ui_labels['partners']['view_all'].'</a>';
        ?>
    </div>
</div>
</div>
</section>
</div>
<footer class="footer">
    <div class ="content">
		<nav class ="navBottom">
			<?php
			$this->load->helper('url');
			echo "<a href='".base_url().$language.'/'."search/dateSearch/0'>".$ui_labels['footer']['links']['events']."</a>";
            echo '<span class="separator">-</span>';
            echo "<a href='".base_url().$language.'/'."contacts'>".$ui_labels['footer']['links']['contacts']."</a>";
            echo '<span class="separator">-</span>';
			echo "<a href='".base_url().$language.'/'."partners'>".$ui_labels['footer']['links']['partners']."</a>";
			?>
		</nav>
		<span class="footerRight">&copy;Copyright 2012 - 2013</span>
	</div>
</footer>
</div>
</body>
</html>