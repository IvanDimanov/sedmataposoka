﻿		<? /*div class="bannerHolder">
				<div id="slides">					
					<?php
					foreach ($banners as $banner) {
					echo '<img src="' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" height="42" width="42">';
					//TODO redirection to partner link
					echo '<h2 class="title">' . $banner['title'] . '</h2>';
					echo '<a href="' . $banner['link'] . '">Banner Link</a>';
					}
					?>
					<img src="img/banner/1a.jpg" alt="123" />
					<img src="img/banner/test.jpg" alt="123" />
				</div>
			</div*/?>
			
	<div class="bannerHolder">
		<div id="slides">	
			<?php
			var_dump($banners);
				foreach ($banners as $banner) {
				echo '<a href="' . $banner['link'] . '"><img src="' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" /></a>';
				//echo '<a href="' . $banner['link'] . '"><img src="' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" /></a>';
				//TODO redirection to partner link
				//echo '<h2 class="title">' . $banner['title'] . '</h2>';
			}
			?>
		</div>
	</div>		
	<div class="articleHolder">
		<div class="articleNav">
			<?php
			$this->load->helper('url');
			echo "<a href='".base_url()."search/dateSearch/0'>
				Днес</a>";
			echo "<a href='".base_url()."event/search/dateSearch/7'>
				Седмица</a>";
			echo "<a href='".base_url()."event/search/dateSearch/14'>
				2 Седмици</a>";
			echo "<a href='".base_url()."event/search/dateSearch/30'>
				Месец</a>";
			?>
		</div>
		<article class="articleText">
			<div class="subCategoryMainHolder">
				<h1>Събития</h1>
				<div class="subCategoryEventHolder">
					<?php
					foreach($events as $event)
					{

						echo "<div class='subCategoryEvent'><h3><a href='".base_url()."event/".$event['eventId']."'>
						".$event['title']."</a></h3>";
						echo "<p class='date'>date1</p>";
						echo "<p>".$event['descr']."</p></div>";
					}
					?>
				</div>	
			</div>
		</article>
	</div>
