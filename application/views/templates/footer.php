<?php
  // A template file used for loading all HTML page footer elements
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
						echo '<img src="' . $add['imagePath'] . '" alt="' . $add['title'] . '" height="42" width="42">';
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
					echo '<a href="' . $partner['link'] . '">View our partner</a>';
				}
				?>
			</div>
		</div>
		</div>
	</section>
	<footer class="footer">
		<nav class ="navBottom">
			<a href="">Събития</a>
			<a href="">Категории</a>
			<a href="contacts.html">Контакти</a>
		</nav>
		<span class="footerRight">&copy;Copyright</span>
	</footer>
</div>
</body>
</html>
