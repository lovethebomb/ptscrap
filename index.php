<?php
/**
 *  TODO
 * - Gérer la boucle qui compte le nb de pages et donc d'images à scraper
 * - nomination des images (titre de l'image ou num ?)
 * - gestion du dossier temporaire et de l'action
 * - affichage en 2 fois 1) prépation du lien 2) création du lien
 */


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pinterest Scrapper</title>   
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
</head>
<body>
	<div class="container">
		<div class="row-fluid">
			<header>
				<h1>Pinterest Board Scrapper</h1>
				<p class="lead">Zip all the pins!</p>
			</header>
			<section class="main">
				<div class="span8">
					<form action="#">
						<label for="url">URL</label>
						<input type="text" class="span6" id="url" placeholder="http://www.pinterest.com/user/board/" value="/stevenmengin/food/">
						<label for="submit"></label>
						<input class="btn btn-primary" id="submit" type="submit" value="Scrap me!">
					</form>

					<div class="result span8" >
						    <p id="status">Retrieving board informations…</p>

						    <div class="progress progress-striped active">
							    <div class="bar" style="width: 1%;"></div>
						    </div>
					</div>

					<div class="error alert alert-error span8" >
						    <p class="message">Error</p>
					</div>

					<div class="file span8" >
						<div class="btn-group">
							<a class="btn btn-success btn-large disabled" href="#" title="Download"><i class="icon-download icon-white"></i> </a>
						    <a class="btn btn-success btn-large" id="download" href="" title="Download">Download file</a>
						</div>
					</div>

			    </div>
			</section>
		</div>
	</div>
	<script type="text/javascript" src="js/app2.js"></script>
</body>
</html>
