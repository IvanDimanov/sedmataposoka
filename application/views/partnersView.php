<div class="partnersInerPageHolder">
	<h1>Patners</h1>
		<?php
		foreach($partners as $partner)
		{
			echo '<div class="box">';
			echo '  <img src="'.base_url().'img/'.$partner['logoSrc'].'" title="'.$partner['name'].'" alt="'.$partner['name'].'" />';
			echo '  <h2><a href="'.$partner['link'].'">'.$partner['name'].'</a></h2>';
			echo '</div>';
		}
		?>
</div>