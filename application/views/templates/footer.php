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
            echo '<p>' . $add['title'] . '</p>';
            echo '<img src="'.  base_url().'img/' . $add['imagePath'] . '" alt="' . $add['title'] . '" height="42" width="42">';
            echo '</a>';
            echo '</div>';
        }
        ?>				
    </aside>
    <div class="partnersMainHolder">
        <h3>Partners list</h3>
        <?php
        foreach ($partners as $partner) {
            echo '<h4>' . $partner['name'] . '</h4>';
            //echo '<img src="' . $partner['logoSrc'] . '" alt="logoImg" height="42" width="42">';
            //TODO redirection to partner link
           // echo '<a href="' . $partner['link'] . '">View our partner</a>';
        }
        echo '<a href="' . base_url().$language.'/partners' . '">View our partner</a>';
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
			echo "<a href='" . base_url().$language .'/'."events'>Събития</a>";
			echo "<a href='" . base_url().$language.'/' . "contacts'>Контакти</a>";
			?>
		</nav>
		<span class="footerRight">&copy;Copyright</span>
	</div>
</footer>
</div>
</body>
</html>
