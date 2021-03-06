<?php
	require_once("../includes/modele.inc.php");
	$tabRes=array();
	function enregistrer(){
		global $tabRes;	
		$titreFilm=$_POST['titreFilm']; 
		$realisFilm=$_POST['realisateur']; 
		$categFilm=$_POST['categFilm']; 
        $dureeFilm=$_POST['dureeFilm'];
		$langFilm=$_POST['langueFilm']; 
        $dateFilm=$_POST['dateFilm'];
		$urlPreview=$_POST['urlPreview'];
        $prix=$_POST['prix'];
		try{
			$unModele=new filmsModele();
			$pochete=$unModele->verserFichier("pochettes", "pochette", "avatar.jpg",$titreFilm);
			$requete="INSERT INTO films values(0,?,?,?,?,?,?,?,?,?)";
			$unModele=new filmsModele($requete,array($titreFilm,$realisFilm,$categFilm,$dureeFilm,$langFilm,$dateFilm,$pochete,$urlPreview,$prix));
			$stmt=$unModele->executer();
			$tabRes['action']="enregistrer";
			$tabRes['msg']="<span class='alert alert-success'>Le film <strong>".$titreFilm."</strong> a bien été enregistré.";
		}catch(Exception $e){
		}finally{
			unset($unModele);
		}
	}
	
	function lister(){
		global $tabRes;
		$tabRes['action']="lister";
		$requete="SELECT * FROM films ORDER BY titre";
		try{
			 $unModele=new filmsModele($requete,array());
			 $stmt=$unModele->executer();
			 $tabRes['listeFilms']=array();
			 while($ligne=$stmt->fetch(PDO::FETCH_OBJ)){
			    $tabRes['listeFilms'][]=$ligne;
			}
		}catch(Exception $e){
		}finally{
			unset($unModele);
		}
	}
	
	function enlever(){
		global $tabRes;	
		$idf=$_POST['numE'];
		try{
			$requete="SELECT * FROM films WHERE id=?";
			$unModele=new filmsModele($requete,array($idf));
			$stmt=$unModele->executer();
			if($ligne=$stmt->fetch(PDO::FETCH_OBJ)){
				$unModele->enleverFichier("pochettes",$ligne->pochette);
				$requete="DELETE FROM films WHERE id=?";
				$unModele=new filmsModele($requete,array($idf));
				$stmt=$unModele->executer();
				$tabRes['action']="enlever";
				$tabRes['msg']="<span class='alert alert-success'>Le film <strong>".$idf."</strong> a bien étéretiré.";
			}
			else{
				$tabRes['action']="enlever";
				$tabRes['msg']="<span class='alert alert-danger'>Film <strong>".$idf."</strong> introuvable.</span>";
			}
		}catch(Exception $e){
		}finally{
			unset($unModele);
		}
	}
	
	function fiche(){
		global $tabRes;
		$idf=$_POST['numF'];
		$tabRes['action']="fiche";
		$requete="SELECT * FROM films WHERE id=?";
		try{
			 $unModele=new filmsModele($requete,array($idf));
			 $stmt=$unModele->executer();
			 $tabRes['fiche']=array();
			 if($ligne=$stmt->fetch(PDO::FETCH_OBJ)){
			    $tabRes['fiche']=$ligne;
				$tabRes['OK']=true;
			}
			else{
				$tabRes['OK']=false;
			}
		}catch(Exception $e){
		}finally{
			unset($unModele);
		}
	}
	
	function modifier(){
		global $tabRes;	

		$idf=$_POST['idf']; 
		$titre=$_POST['titreFilmM'];
		$res=$_POST['realisateurM'];
		$categ=$_POST['categFilmM'];
		$duree=$_POST['dureeFilmM'];
		$langue=$_POST['langueFilmM'];
		$annee=$_POST['dateFilmM'];
		$preview=$_POST['urlPreviewM'];
		$prix=$_POST['prixM'];

		try{
			//Recuperer ancienne pochette
			$requette="SELECT pochette FROM films WHERE id=?";
			$unModele=new filmsModele($requette,array($idf));
			$stmt=$unModele->executer();
			$ligne=$stmt->fetch(PDO::FETCH_OBJ);
			$anciennePochette=$ligne->pochette;
			$pochette=$unModele->verserFichier("pochettes", "pochetteM",$anciennePochette,$titre);	
			
			$requete="UPDATE films SET titre=?,realisateur=?,categorie=?,duree=?,langue=?,annee=?, pochette=?,urlPreview=?,prix=? WHERE id=?";
			$unModele=new filmsModele($requete,array($titre,$res,$categ,$duree,$langue,$annee,$pochette,$preview,$prix,$idf));
			$stmt=$unModele->executer();
			$tabRes['action']="modifier";
			$tabRes['msg']="<span class='alert alert-success'>Le film <strong>".$idf."</strong> a été modifié avec succès.";
		}catch(Exception $e){
		}finally{
			unset($unModele);
		}
	}
	//******************************************************
	//Controleur
	$action=$_POST['action'];
	switch($action){
		case "enregistrer" :
			enregistrer();
		break;
		case "lister" :
			lister();
		break;
		case "enlever" :
			enlever();
		break;
		case "fiche" :
			fiche();
		break;
		case "modifier" :
			modifier();
		break;
	}
    echo json_encode($tabRes);
?>