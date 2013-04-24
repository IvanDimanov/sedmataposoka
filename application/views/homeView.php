

<div class="bannerHolder">
    <div id="slides">	
        <?php
        foreach ($banners as $banner) {
            echo '<a href="' . $banner['link'] . '"><img src="' . base_url() . 'img/' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" /></a>';
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
        echo "<a href='" . base_url() .$language.'/'. "search/dateSearch/0'>
				Днес</a>";
        echo "<a href='" . base_url() .$language.'/'. "search/dateSearch/7'>
				Седмица</a>";
        echo "<a href='" . base_url() .$language.'/'. "search/dateSearch/14'>
				2 Седмици</a>";
        echo "<a href='" . base_url() .$language.'/'. "search/dateSearch/30'>
				Месец</a>";
        ?>
    </div>
    <article class="articleText">
        <div class="subCategoryMainHolder">
            <h1><?php echo $data['viewedEventsTitle'];?></h1>
            <div class="subCategoryEventHolder">
                <?php
                foreach ($events as $event) {

                    echo "<div class='subCategoryEvent'><h3><a href='" . base_url().$language.'/' . "event/" . $event['eventId'] . "'>
						" . $event['event_title'] . "</a></h3>";
                    echo '<p class="date">начало: </span>.'.$event["startDate"].'</p>';
                    echo '<p><span class="bold">край: </span>.'.$event["endDate"].'</p>';
                    echo "<p>" . $event['event_descr'] . "</p></div>";
                }
                ?>
            </div>	
        </div>
    </article>
</div>
