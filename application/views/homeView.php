		<div class="bannerHolder">
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
			</div>
			<div class="articleHolder">
				<div class="articleNav">
				<?php
					$this->load->helper('url');
					echo "</br><a href='".base_url()."search/dateSearch/0'>
					    Dnes</a></br>";
					echo "<a href='".base_url()."event/search/dateSearch/7'>
					    Week</a></br>";
					echo "<a href='".base_url()."event/search/dateSearch/14'>
					    Week</a></br>";
					echo "<a href='".base_url()."event/search/dateSearch/30'>
					    Week</a></br>";
				?>
				</div>
				<article class="articleText">
					<?php
						foreach($events as $event)
						{
						echo "<a href='".base_url()."event/".$event['eventId']."'>
						    ".$event['event_title']."</a></br>";
						}
					?>


					<a class="" href="" >евент</a>
					<p>Lorem ipsum <a href="#">dolor</a> sit amet, perihermeneias Apollonium contigit cum magna duobus consolabor potest meum filiam rex ut sua Cumque persequatur sic. Iubeo singulas cotidie enim est se in rei exultant deo agili est in. Item agnovit sit dolor ad suis alteri formam unitas reddere nominabat princeps coniungitur vestra felicitate facientibus nulla. Nunc eam est cum suam ad te. Modum cognoscibilis ad per sanctus ait in, auri tecum ad nomine Piscatore mihi esse haec. Eam eos Communicatio mihi Tyrum reverteretur ad te ad per. Proscriptum videt ulteriori justo forma ait in rei finibus veteres hoc. Suos Tyrium coniugem Chaldaeorum in fuerat construeret cena reges undis effugere quod eam est se ad te ad nomine. Duc quia ad quia quod eam ad te princeps audito doctrinis beneficio uxor dei. Circumdat flante vestibus indulgentia pedes rex Dionysiadi rex ut libertatem non dum. Perihermeneias Apollonium contigit cum autem quod tamen alius campo iactavitque per sanctus singulos exerceret quodam domina ego. Eiusdem ordo quos essem rogo me vero non coepit cognitionis huius domus respexit princeps audito sed esse in.Iubet gurgite pulcherrimam vis proprium in. Sanguine concomitatur quia illum vero rex cum magna duobus discessit Tharsiam Hellenicus ut sua. Tharsum cuius ad per dicis filiam vel dolens! Nescimus de his e neptem semper incurristi filiam sum in deinde vero cum unde beata inter ratio omnes. Peractoque convocatis secessit civitatis in fuerat accidens inquit atque album Apolloni sed. Etiam audiens autem est amet constanter determinatio debitis torporis quin virgo meam accepit corpus ad te. Prius componitur suscepit in modo cavendum es. De me missam canticis in deinde plectrum anni ipsa hospes ut sua.Multa famis perisse naufrago credis ei, felix pro aperuit in fuerat accidens quam aniculae. Habere homo nos in deinde vero rex cum obiectum invidunt cum obiectum invidunt cum autem Apolloni figitur acquievit. Archistrate accepta illos praesens disponuntur individuationis quae vero non dum, sciendum ait Cumque hoc contra suis ut sua Cumque materia ad nomine. Erras nisi se vero diam nostra praedicabilium subsannio oculos capillos quam dolore facto similia sed. 'arripuit plorabis filiam vel dolens eos vero quo alacres ad te, quattuordecim anulum coniugi vero non solutionem invenerunt ita cum! Quique non solutionem innocentem tantusque amorem iam insulam speciosissimam, indulgentia pedes apud libram dabo potest ei Taliarchum. Interpellastis suam vidit ad per dicis filiam sunt forma in lucem concitaverunt in deinde cepit roseo. Me missam ne alicuius tuo curavit quo accumsan in. Suave canere se sed eu fugiens laudo in deinde duas formis. Sua confusus ait est se sed haec.Musis nihilominus proposuisti enim me naufragus habuisti sit dolor virgo coniunx caritate completae ad per animum pares terris restituit. Quattuordecim anulum in lucem genero ergo accipiet. Nec appellarer in fuerat accidens quam dicentes semper coniungere optavimus in rei civibus. Coniungitur vestra nutriendam veni perfrictione cogente iniquam deus puella ut casus inferioribus civitatis civium currebant in. Istis dotis quare Tharsia coniugem flebant Tharsiam vis apud senex individuatam spiritus sanguis. Voce clamavimus haec aliquam inlido laetare in rei civibus unde beata quid ait est se in.Impietatem flumina in lucem genero in lucem, potest ei quoque sed eu fugiens laudo misera haec. Ardalio nos in lucem concitaverunt in. Contine inauditas actualitas tuae illa mihi Tyrum ad suis Tyrium. Navis famuli autem illud huius dulcis amplexu dicentes Is hendrerit ad te in fuerat accidens inquit fidem emam poena Apollonius. Ipsa Invitamus me testatur in rei completo litus sua. Circumdat flante vestibus indulgentia perrexit est in fuerat accidens inquit merui litore iunctionem quae non dum autem est Apollonius. Dabit es illum ille Tharsos determinatio debitis torporis quin. Nuptui tradiditque semper incurristi filiam sum in fuerat est in lucem. Scilicet Athenagora eius sed quod non dum est amet consensit cellula rei sensibilium. Excepteur sola dum miror diligere quem suis, sed eu fides piissimi deo adiuves finem. Circumdat flante vestibus lumine restat paralyticus ante nesciret, audite deo adiuves finem ibi non ait in. Tua in deinde cepit roseo ruens sed esse ait in lucem in deinde cepit roseo. Erras nisi Apollonius ut a a. Cupis hominem in modo cavendum es audito doctrinis beneficio. Litus tuos sed eu fugiens laudo misera haec.</p>
				</article>
			</div>
