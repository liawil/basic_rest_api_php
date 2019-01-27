<?php

/**
 * Toutes les fonctions utiles pour faciliter les traitements
 */

/*Fonction pour lister les options de selection d'une requête SELECT
@param array('value')
@return options_list*/
if (!function_exists('selectOptions')) {
	function selectOptions($options){
		$opt = "*";
		if (!is_null($options)) {
			$i = 0;
			foreach ($options as $key => $value) {
				if ($i) {
				 	$opt .= ", $value";
				}else{
					$opt = "$value";
				}
				$i++; 
			}
		}
		return $opt;
	}
}

/*Fonction pour lister les options de condition (WHERE) d'une requête SELECT
@param array('key'=>'value')
@return options_list */
if (!function_exists('whereOptions')) {
	function whereOptions($options,$operation){
		$opt = "";
		if (!is_null($options)) {
			$i = 0;
			foreach ($options as $key => $value) {
				if ($i) {
				 	$opt .= " AND $key ".$operation." '".addslashes(htmlspecialchars($value))."'";
				}else{
					$opt = "$key ".$operation." '".addslashes(htmlspecialchars($value))."'";
				}
				$i++; 
			}
		}
		return $opt;
	}
}

/*Fonction pour lister les options de condition (WHERE) d'une requête SELECT
@param array('key'=>'value') ex: ('id =','5') / ('Or name like','%jean%')
@return options_list */
if (!function_exists('whereOptionsNew')) {
	function whereOptionsNew($options){
		if (is_array($options)) {
			$opt = "";
			foreach ($options as $key => $value) {
				$opt .= " $key '".addslashes(htmlspecialchars($value))."'";
			}
		}
		return $opt;
	}
}

/*Fonction pour lister les données à insérer d'une requête INSERT INTO ou UPDATE
@param array('key'=>'value')
@return options_list */
if (!function_exists('insertOptions')) {
	function insertOptions($data){
		$data_list = "";
		if (!is_null($data)) {
			$i = 0;
			foreach ($data as $key => $value) {
				if ($i) {
				 	$data_list .= ", $key = '".addslashes(htmlspecialchars($value))."'";
				}else{
					$data_list = "$key = '".addslashes(htmlspecialchars($value))."'";
				}
				$i++; 
			}
		}
		return $data_list;
	}
}

/*Fonction pour lister les tables d'une requête de jointure
@param array('value')
@return options_list*/
if (!function_exists('tableList')) {
	function tableList($tables){
		if (!is_null($tables)) {
			$i = 0;
			foreach ($tables as $key => $value) {
				if ($i) {
				 	$list .= ", $value";
				}else{
					$list = "$value";
				}
				$i++; 
			}
		}
		return $list;
	}
}


/*Fonction pour lister les options de JOINTURE d'une requête SELECT
@param array('join option + table'=>'join condition')
@return options_list */
if (!function_exists('joinOptions')) {
	function joinOptions($options){
		$opt = "";
		if (!is_null($options)) {
			$i = 0;
			foreach ($options as $key => $value) {
				$opt .= "\n".addslashes(htmlspecialchars($key))." ON ".addslashes(htmlspecialchars($value)); 
			}
		}
		return $opt;
	}
}

/*Fonction recupérer les fichiers d'un projet
@param string id
@return array() data 
*/
if (!function_exists('getProjetMedia')) {
	function getProjetMedia($id){
		$join_opt = [
			'INNER JOIN media_project' => 'media_project.id_media = media.id_media'
		];
		$where_opt = ['media_project.projet_id'=>$id];
		return DBconnect::querySelectJoinWhere('media',$join_opt,$where_opt,'=');
	}
}

/*Fonction recupérer les fichiers d'un utilisateur
@param string id
@return array() data */
if (!function_exists('getProfilMedia')) {
	function getProfilMedia($id){
		$join_opt = [
			'INNER JOIN media_profile' => 'media_profile.id_media = media.id_media'
		];
		$where_opt = ['media_profile.code_secret'=>$id];
		return DBconnect::querySelectJoinWhere('media',$join_opt,$where_opt,'=');
	}
}

/*Fonction recupérer la liste des pays
@return array() data */
if (!function_exists('getPaysList')) {
	function getPaysList($pays_table){
		$pref = ($pays_table == 'pays2')? 'pays_fr':'pays';
		$data =  DBconnect::querySelectOrderBy($pays_table,'lib_'.$pref.' ASC');
		foreach ($data as $key => $value) {
			echo '<option value="'.$value["id_pays"].'">'.$value["lib_$pref"].'</option>';
		} 
	}
}

/*Fonction recupérer la liste des ville_a
@return array() data */
if (!function_exists('getVilleList')) {
	function getVilleList($ville_table,$choix){
		$pref = ($ville_table == 'ville_b')? '_b':'';
		$data = DBconnect::querySelectWhere($ville_table,["id_pays$pref"=>$choix],'=');
		foreach ($data as $key => $value) {
			echo '<option value="'.$value["id_$ville_table"].'">'.$value["nom_$ville_table"].'</option>';
		} 
	}
}


/*Fonction recupérer les infos à modifier dans une table
@return array() data */
if (!function_exists('edit')) {
	function edit($table,$whereOptions){
		return DBconnect::querySelectWhere($table,$whereOptions,'=');
	}
}


/**
 * Fonction pour créer un système de datage du temps écoulé d'un moment donnée à un moment courant.
 * @Param date
 * @Return string
 */
if (! function_exists('datage')) {    
    function datage($date){
    	//setlocale (LC_TIME, 'fr_FR.utf8','fra');
        if(!ctype_digit($date))
            $date = strtotime($date);
        if(date('Y-m-d', $date) == date('Y-m-d')){
            $diff = time()-$date;
            if($diff < 60) /* moins de 60 secondes */
                return 'Il y a '.$diff.' sec';
            else if($diff < 3600) /* moins d'une heure */
                return 'Il y a '.round($diff/60, 0).' min';
            else if($diff < 10800) /* moins de 3 heures */
                return 'Il y a '.round($diff/3600, 0).' heures';
            else /*  plus de 3 heures ont affiche ajourd'hui à HH:MM:SS */
                return 'Aujourd\'hui à '.date('H:i:s', $date);
        }
        else if(date('Y-m-d', $date) == date('Y-m-d', strtotime('- 1 DAY')))
            return 'Hier à '.date('H:i:s', $date);
        else if(date('Y-m-d', $date) == date('Y-m-d', strtotime('- 2 DAY')))
            return 'Il y a 2 jours à '.date('H:i:s', $date);
        else
            return 'Le '.date('d/m/Y à H:i:s', $date);
    }
}

if (! function_exists('datage_format')) {

    function datage_format($datetime) {
    	/*$tz = new DateTimeZone('Africa/Abidjan');
		$datedujour = new DateTime('now',$tz);*/
        $now = time();
        $created = strtotime($datetime);
        // La différence est en seconde
        $diff = $now-$created;
        $m = ($diff)/(60); // on obtient des minutes
        $h = ($diff)/(60*60); // ici des heures
        $j = ($diff)/(60*60*24); // jours
        $s = ($diff)/(60*60*24*7); // et semaines
        if ($diff < 60) { // "il y a x secondes"
            return 'Il y a '.$diff.' secondes';
        }
        elseif ($m < 60) { // "il y a x minutes"
            return 'Il y a '.floor($m).' minutes';
        }
        elseif ($h < 24) { // " il y a x heures, x minutes"
            $dateFormated = 'Il y a '.floor($h).' heures';
            return $dateFormated;
        }
        elseif ($j < 7) { // " il y a x jours, x heures"
            $dateFormated = 'Il y a '.floor($j).' jours';
            return $dateFormated;
        }
        elseif ($s < 5) { // " il y a x semaines, x jours"
            $dateFormated = 'Il y a '.floor($s).' semaines';
            return $dateFormated;
        }
        else { // on affiche la date normalement
            return strftime("%d %B %Y à %H:%M", $created);
        }
    }
}

/**
 * Fonction d'envoie de mail
 * @Param email string
 * @Param objetDuMail string
 * @Param message string
 */
if (! function_exists('sendMail')) {
	
	function sendMail($email,$objet,$message){
		
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $email)) // On filtre les serveurs qui rencontrent des bogues.
		{
			$passage_ligne = "\r\n";
		}
		else
			$passage_ligne = "\n";

		//=====Déclaration des messages au format texte et au format HTML.
		$message_html = $message;
		//==========

		//=====Création de la boundary
		$boundary = "-----=".md5(rand());
		//==========
		 
		//=====Définition du sujet.
		$sujet = $objet;
		//=========
		
		//=====Création du header de l'e-mail.
		$header = "From: \"Diaspo4Africa\"<contact@diaspo4africa.com>".$passage_ligne;
		$header.= "Reply-to: \"Diaspo4Africa\" <contact@diaspo4africa.com>".$passage_ligne;
		$header.= "MIME-Version: 1.0".$passage_ligne;
		$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
		//==========
	
		//=====Création du message.
		$message = $passage_ligne."--".$boundary.$passage_ligne;
		//=====Ajout du message au format HTML
		$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_html.$passage_ligne;
		//==========
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		//==========

		//=====Envoi de l'e-mail.
		mail($email,$sujet,$message,$header);
		//==========
	}
}

/*Fonction pour faire un curl
@param array('value')
@return options_list*/
if (!function_exists('curl_post')) {
	function curl_post($params=array(), $method, $url){
		
		$postfields = json_encode($params);

		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => $method,
		  CURLOPT_POSTFIELDS => $postfields,
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/x-www-form-urlencoded",
		    "postman-token: aef02b3c-dbd6-1549-2cee-975d747297cb"
		  ),
		));

		$content = curl_exec($curl);
		$err = curl_error($curl);
		$http = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return [
			"http" => $http,
			"content" => $content,
			"err" => $err
		];
	}
}