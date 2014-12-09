

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
        echo "<a href='".base_url().$language.'/'."search/dateSearch/0' >".$ui_labels['search']['today'  ]."</a>";
        echo "<a href='".base_url().$language.'/'."search/dateSearch/7' >".$ui_labels['search']['week'   ]."</a>";
        echo "<a href='".base_url().$language.'/'."search/dateSearch/14'>".$ui_labels['search']['2_weeks']."</a>";
        echo "<a href='".base_url().$language.'/'."search/dateSearch/30'>".$ui_labels['search']['month'  ]."</a>";
        ?>
    </div>
    <article class="articleText">
        <div class="subCategoryMainHolder">
            <h1><?php echo $viewedEventsTitle;?></h1>
			<p class="noItems">There are no Events for this date :(</p>
            <div class="subCategoryEventHolder">
                <?php
                foreach ($events as $event) {

                    echo "<div class='subCategoryEvent'><h3><a href='" . base_url().$language.'/' . "event/" . $event['eventId'] . "'>
						" . $event['event_title'] . "</a></h3>";
                    echo '<p class="date"><span class="bold">'.$ui_labels['event']['start_date'].': </span>'.$event["startDate"].'</p>';
                    echo '<p class="date"><span class="bold">'.$ui_labels['event']['end_date'  ].': </span>'.$event["endDate"]  .'</p>';
                    echo '<p class="eventDescr">'.$event['event_descr'].'</p></div>';
                }
                ?>
				<div class="pagging"></div>
            </div>	
        </div>
    </article>
</div>
