<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
  <head>
		<title>Numérotation des actes</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="./css/main.css" />
  </head>
  <body>
  <?php  if(!isset($_GET['id'])) { ?>
  <div class="wrapper style3">
	<article id="CreationFichier">
		<header>
			<h2>Génération de numéros d'acte</h2>
			<p>Cet outil permet de générer une numérotation continue.</p>
		</header>
		<div class="container">
			<div class="row">
				<div class="6u 12u(mobile)">
					<article class="box style2">

						
					  
						<p> Renseignez le formulaire, cliquez sur « Générer ». Un lien est proposé ; visitez-le et enregistrez-le dans vos signets. Vous pouvez envoyer l'URL à vos contacts pour partager une numérotation unique.</p>
						<p>Préfixe, exemple : 2017-SRA-</p>
						<p>Début de numérotation, exemple : 45</p>
						<p>Nombre de 0 significatifs, exemple : 3</p>
	
					</article>
				</div>
				<div class="6u 12u(mobile)">
					<article class="box style2">

						<form class="formerize-placeholder">
							<table>
								<tr>
								  <td>
									<label for="prefixe">Préfixe :</label>
								  </td>
								  <td>
									<input type="text" id="prefixe" value="2016-" onkeyup="calcHash()">
								  </td>
								</tr>
								<tr>
								  <td>
									<label for="numeroDebut">Début de numérotation :</label>
								  </td>
								  <td>
									<input type="text" id="numeroDebut" value="1" onkeyup="calcHash()">
								  </td>
								</tr>
								<tr>
								  <td>
									<label for="zeroSign">Nombre de 0 significatifs :</label>
								  </td>
								  <td>
									<input type="text" id="zeroSign" value="3" onkeyup="calcHash()">
								  </td>
								</tr>
							</table>			
							<input type="button" id="genere" value="Générer">
						</form>
					</article>
				</div>
				
			</div>	
			<div class="row">
				
				<div class="6u 12u(mobile)">
					<article class="box style2">
						
						<h3>Résultat : <span id="resultat"></span></h3>
					
					</article>
				</div>
				
				<div class="6u 12u(mobile)">
					<article class="box style2">
						
						<!-- afficher le lien après qu'il soit créé -->
						<p id="lien" style="display:none">Visitez le <a href="index.php" id="hashOutputText">lien et conservez-le comme signet</a>.</p>
					
					</article>
				</div>
			</div>
		</div>
	</article>
	</div>
				<?php } 

		else {
		
			$list = explode("fA==", $_GET['id']);
			$url = $_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];			
			
			$pref = base64_decode($list[1]);
			$numero = intval(base64_decode($list[2]));
			($numero > 0) ? $numero-- : $numero ;
			$zero = intval(base64_decode($list[3]));
			$numeroActe = $pref . str_pad(intval($numero), $zero, '0', STR_PAD_LEFT);
				
			// vérifier fichier Json existe
			$filename = "./fichiers/".$_GET['id'].'.json';
			if (!file_exists($filename)) {
			
				// créer et entrer la première ligne
				$ligne1 = array("actes"=>(array(array("numero"=>"$numero","code"=>"$numeroActe", "libelle"=> "Initialisation"))));

				// Ouverture du fichier
				$fichier = fopen($filename, 'w+');

				// Ecriture dans le fichier
				fwrite($fichier, json_encode($ligne1));

				// Fermeture du fichier
				fclose($fichier);
			
			}

			
			echo "<div class='wrapper style3'><article id='ajoutActe'><div class='container'><div class='row'><div class='12u 12u(mobile)'><article class='box style2'><h2>Créer un nouvel acte</h2>";
			echo "<label for='nouvelActe'>Libellé : </label><input type='text' size='64' id='nouvelActe' autocomplete='off'><input type='hidden' id='filename' value='{$filename}'><input type='hidden' id='pref' value='{$pref}'><input type='hidden' id='zero' value='{$zero}'><hr>";
			
			echo"<div id='ActesDetail'><h3 id='ADH'>Actes</h3></div></article></div></div></div></article></div>";
			
		} ?>
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript">
		var d = new Date();
		var y = d.getFullYear();
		var mTimer;
		try {
			$('#prefixe').attr('value',y+"-");
		}
		catch(err) {
			console.log("Formulaire initialisé");
		}
		
		function utf8_to_b64( str ) {
		  return window.btoa(unescape(encodeURIComponent( str )));
		}

		function b64_to_utf8( str ) {
		  return decodeURIComponent(escape(window.atob( str )));
		}
		
		function calcHash() {
			hashInput1 = $("#prefixe").val();
			hashInput2 = $("#numeroDebut").val();
			hashInput3 = $("#zeroSign").val();
			hi3 = "00000000".substr(1,hashInput3);
			$('#resultat').html(hashInput1+(hi3+hashInput2).slice(-hashInput3));
		}
		
		// Ajouter un acte
		$(document).ready(function(){
			getActes();
			$('#nouvelActe').keyup(function(e) {    
			if(e.keyCode == 13) {
				sendActe();
			}
			});
		
			$( "#genere" ).click(function() {
				hashInput1 = $("#prefixe").val();
				hashInput2 = $("#numeroDebut").val();
				hashInput3 = $("#zeroSign").val();
				var hashObj = event.timeStamp + "fA==" + utf8_to_b64(hashInput1) + "fA==" + utf8_to_b64(hashInput2) + "fA==" + utf8_to_b64(hashInput3);
				$('#hashOutputText').attr('href',"?id="+hashObj);
				$('#lien').attr('style',"display:block");
			});
		});
		
		function sendActe() {
			var donnee = $('#nouvelActe').val();
			var filename = $('#filename').val();
			var pref = $('#pref').val();
			var zero = $('#zero').val();
			
			if(donnee == '') {
				alert("Vous n'avez rien écrit !");
				return;
			}
			
			$.post('put.php',{
				  libelle: donnee,
				  pref: pref,
				  zero: zero,
				  filename: filename
				},
				function(data) {
				  console.log("Envoi formulaire");
				});
						
			$('#nouvelActe').val('');
			getActes();
			location.reload();
		}

		// Affiche les actes
		function getActes() {
			$('#ActesDetail').html("<h3 id='ADH'>Actes</h3>");
			var filename = $('#filename').val();
			$.getJSON(filename, function(result){
				  console.log(result);
				  $.each(result.actes, function(i, acte) {
					$("<p class='listeActe'>" + acte.code + " : " + acte.libelle + "</p>").insertAfter($( '#ADH'));
				  });
			});
		}			
	</script>
  </body>
</html>