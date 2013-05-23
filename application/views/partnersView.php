<div class="partnersInerPageHolder">
	<h1>Patners</h1>
	<div class="box">
		<?php
		foreach($partners as $partner)
		{
			echo '<img src="'.$partner['logoSrc'].'"/>';
			echo '<h2><a href="'.$partner['link'].'">'.$partner['name'].'</a></h2>';
			   
			
		}
		?>
	</div>
</div>