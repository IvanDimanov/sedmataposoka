<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="wrapAll">
	<header class="wrapHeader">
		<div class="clear">
			<a class="logo" href="" ><img src="img/logo_sedmata_posoka.png" alt="logo" /></a>	
			<div class="mindHolder">
				<?php
				echo '<p>"' . $tought[0]['text'] . '"<span class="author"> ' . $tought[0]['author'].'</span></p>';
				?>
			</div>
			<div class="bubbles"></div>
			<div class="rightPart">
				<section class="language clear">	
					<a href="#" class="en"></a>
					<a href="#" class="bg"></a>
				</section>
				<section class="search">
					<input class="searchTxt" type="search" />
					<input class="searchBttn" type="submit" value="" />
				</section>
				<section class="socials clear">
					<a class="email" href=""></a>
					<a class="fb" href=""></a>
				</section>
			</div>
		</div>
	</header>
	<section class="wrapMain">
		<div class="clear">
		<div class ="wrapMainLeft">
			<h2>Каталог</h2>
			<nav class ="navMain">
				<?php
				$this->load->helper('url');

				foreach ($categories as $category) {
					// echo '<h1>' . $category['name'] . '</h1>';
					echo '<a href="' . base_url() . 'category/index/' . $category['id'] . '">' .
					$category['name'] . ' </a>';
					echo '<div class="navMainSubcategory">';
					for ($i = 0; $i < sizeof($subcategories); $i++) {
						if ($subcategories[$i]['catId'] === $category['id']) {
							echo '<p>' . $subcategories[$i]['name'] . '</h2>';
						}
					}
					echo '</div>';
				}
				?>
			</nav>
		</div>
		<div class ="wrapMainMiddle">
			<div class="bannerHolder">
				<div class="bannerHolderImg">					
					<?php
					foreach ($banners as $banner) {
						echo '<img src="' . $banner['imagePath'] . '" alt="' . $banner['title'] . '" height="42" width="42">';
						//TODO redirection to partner link
						echo '<a href="' . $banner['link'] . '">Banner Link</a>';
					}
					?>
				</div>
				<a class="bannerArrowLeft" href="#" ></a>
				<a class="bannerArrowRight" href="#" ></a>
				<div class="bannerNav clear">
					<?php
						foreach ($banners as $banner) {
						echo '<h2 class="title">' . $banner['title'] . '</h2>';
						}
					?>
					<a href="" ></a>
					<a class="active" href="" ></a>
				</div>
			</div>
			<div class="articleHolder">
				<div class="articleNav">
					<a href="" >123</a>
					<a href="" >456</a>
					<a href="" >789</a>
				</div>
				<article class="articleText">
					<h1>123</h1>
					<p>Lorem ipsum <a href="#">dolor</a> sit amet, perihermeneias Apollonium contigit cum magna duobus consolabor potest meum filiam rex ut sua Cumque persequatur sic. Iubeo singulas cotidie enim est se in rei exultant deo agili est in. Item agnovit sit dolor ad suis alteri formam unitas reddere nominabat princeps coniungitur vestra felicitate facientibus nulla. Nunc eam est cum suam ad te. Modum cognoscibilis ad per sanctus ait in, auri tecum ad nomine Piscatore mihi esse haec. Eam eos Communicatio mihi Tyrum reverteretur ad te ad per. Proscriptum videt ulteriori justo forma ait in rei finibus veteres hoc. Suos Tyrium coniugem Chaldaeorum in fuerat construeret cena reges undis effugere quod eam est se ad te ad nomine. Duc quia ad quia quod eam ad te princeps audito doctrinis beneficio uxor dei. Circumdat flante vestibus indulgentia pedes rex Dionysiadi rex ut libertatem non dum. Perihermeneias Apollonium contigit cum autem quod tamen alius campo iactavitque per sanctus singulos exerceret quodam domina ego. Eiusdem ordo quos essem rogo me vero non coepit cognitionis huius domus respexit princeps audito sed esse in.Iubet gurgite pulcherrimam vis proprium in. Sanguine concomitatur quia illum vero rex cum magna duobus discessit Tharsiam Hellenicus ut sua. Tharsum cuius ad per dicis filiam vel dolens! Nescimus de his e neptem semper incurristi filiam sum in deinde vero cum unde beata inter ratio omnes. Peractoque convocatis secessit civitatis in fuerat accidens inquit atque album Apolloni sed. Etiam audiens autem est amet constanter determinatio debitis torporis quin virgo meam accepit corpus ad te. Prius componitur suscepit in modo cavendum es. De me missam canticis in deinde plectrum anni ipsa hospes ut sua.Multa famis perisse naufrago credis ei, felix pro aperuit in fuerat accidens quam aniculae. Habere homo nos in deinde vero rex cum obiectum invidunt cum obiectum invidunt cum autem Apolloni figitur acquievit. Archistrate accepta illos praesens disponuntur individuationis quae vero non dum, sciendum ait Cumque hoc contra suis ut sua Cumque materia ad nomine. Erras nisi se vero diam nostra praedicabilium subsannio oculos capillos quam dolore facto similia sed. 'arripuit plorabis filiam vel dolens eos vero quo alacres ad te, quattuordecim anulum coniugi vero non solutionem invenerunt ita cum! Quique non solutionem innocentem tantusque amorem iam insulam speciosissimam, indulgentia pedes apud libram dabo potest ei Taliarchum. Interpellastis suam vidit ad per dicis filiam sunt forma in lucem concitaverunt in deinde cepit roseo. Me missam ne alicuius tuo curavit quo accumsan in. Suave canere se sed eu fugiens laudo in deinde duas formis. Sua confusus ait est se sed haec.Musis nihilominus proposuisti enim me naufragus habuisti sit dolor virgo coniunx caritate completae ad per animum pares terris restituit. Quattuordecim anulum in lucem genero ergo accipiet. Nec appellarer in fuerat accidens quam dicentes semper coniungere optavimus in rei civibus. Coniungitur vestra nutriendam veni perfrictione cogente iniquam deus puella ut casus inferioribus civitatis civium currebant in. Istis dotis quare Tharsia coniugem flebant Tharsiam vis apud senex individuatam spiritus sanguis. Voce clamavimus haec aliquam inlido laetare in rei civibus unde beata quid ait est se in.Impietatem flumina in lucem genero in lucem, potest ei quoque sed eu fugiens laudo misera haec. Ardalio nos in lucem concitaverunt in. Contine inauditas actualitas tuae illa mihi Tyrum ad suis Tyrium. Navis famuli autem illud huius dulcis amplexu dicentes Is hendrerit ad te in fuerat accidens inquit fidem emam poena Apollonius. Ipsa Invitamus me testatur in rei completo litus sua. Circumdat flante vestibus indulgentia perrexit est in fuerat accidens inquit merui litore iunctionem quae non dum autem est Apollonius. Dabit es illum ille Tharsos determinatio debitis torporis quin. Nuptui tradiditque semper incurristi filiam sum in fuerat est in lucem. Scilicet Athenagora eius sed quod non dum est amet consensit cellula rei sensibilium. Excepteur sola dum miror diligere quem suis, sed eu fides piissimi deo adiuves finem. Circumdat flante vestibus lumine restat paralyticus ante nesciret, audite deo adiuves finem ibi non ait in. Tua in deinde cepit roseo ruens sed esse ait in lucem in deinde cepit roseo. Erras nisi Apollonius ut a a. Cupis hominem in modo cavendum es audito doctrinis beneficio. Litus tuos sed eu fugiens laudo misera haec.</p>
				</article>
			</div>
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