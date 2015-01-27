<div class="partnersInerPageHolder">
	<h1><?=$ui_labels['partners']['content_title']?></h1>
		<?php
		foreach($partners as $partner)
		{
			echo '<div class="box">';
			echo '  <img src="'.base_url().'img/'.$partner['imagePath'].'" title="'.$partner['name'].'" alt="'.$partner['name'].'" />';
			echo '  <h2><a href="'.$partner['link'].'">'.$partner['name'].'</a></h2>';
			echo '</div>';
		}
		?>
</div>