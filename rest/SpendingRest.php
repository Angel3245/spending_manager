<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Spending.php");
require_once(__DIR__."/../model/SpendingMapper.php");

require_once(__DIR__."/BaseRest.php");

require_once(__DIR__."/../model/FileUpload.php");
require_once(__DIR__."/../model/FileUploadMapper.php");

/**
* Class SpendingRest
*
* It contains operations for creating, retrieving, updating, deleting and
* listing spendings, as well as to create comments to spendings.
*
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
* 
* @author Jose Ángel Pérez Garrido
*/
class SpendingRest extends BaseRest {
	private $userMapper;
	private $spendingMapper;
	private $fileUploadMapper;

	public function __construct() {
		parent::__construct();

		$this->userMapper = new UserMapper();
		$this->spendingMapper = new SpendingMapper();
		$this->fileUploadMapper = new FileUploadMapper();
	}

	public function getSpendings() {
		$currentLogged = parent::authenticateUser();

		if($currentLogged->getRole() == "admin"){
			$spendings = $this->spendingMapper->findAll();
		} else {
			$spendings = $this->spendingMapper->findAllMine($currentLogged->getAlias());
		}

		// json_encode Spending objects.
		// since Spending objects have private fields, the PHP json_encode will not
		// encode them, so we will create an intermediate array using getters and
		// encode it finally
		$spendings_array = array();
		foreach($spendings as $spending) {
			array_push($spendings_array, array(
				"id_spending" => $spending->getId(),
				"type_spending" => $spending->getType(),
				"date_spending" => $spending->getDate(),
				"qty_spending" => $spending->getQuantity(),
				"description_spending" => $spending->getDescription(),
				"doc_name_spending" => $spending->getDocName(),
				"owner" => $spending->getOwner()->getAlias()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header('Content-Type: application/json');
		echo(json_encode($spendings_array));
	}


	/**
	 * Function that do create a spending based upon a FORM in the html
	 */
	public function createSpending($sp) {
		$currentUser = parent::authenticateUser();
		$spending = new Spending();
		

		if (isset($sp->type_spending) && isset($sp->date_spending) && isset($sp->qty_spending)) {
			
			//Mandatory
			$spending->setType($sp->type_spending);
			$spending->setDate($sp->date_spending);
			$spending->setQuantity($sp->qty_spending);

			//Optional
			if(isset($sp->description_spending)){
				$spending->setDescription($sp->description_spending);
			}
				
			if( isset($sp->doc_name_spending) && isset($sp->doc_data) ){

				$base64file = new FileUpload($sp->doc_data);

				try {
					// validate FileUpload object
					$base64file->checkIsValidForUpload();

					// save the FileUpload object into the server
					$fileNameServer = $this->fileUploadMapper->save($base64file);

					$spending->setDocName($sp->doc_name_spending);
					$spending->setDocServer($fileNameServer);

				} catch (ValidationException $e) {
					header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
					header('Content-Type: application/json');
					echo(json_encode($e->getErrors()));
				}
			}

			if ($currentUser->getRole() == "admin") {
				$spending->setOwner(new User($sp->owner_spending));
				
			} else {
				$spending->setOwner($currentUser);
			}
		}

		try {
			// validate Spending object
			$spending->checkIsValidForCreate(); // if it fails, ValidationException

			if ($this->userMapper->aliasExists($spending->getOwner()->getAlias())) { //check if owner exists

				// save the Spending object into the database
				$spendingId = $this->spendingMapper->save($spending);

				// response OK. Also send spending in content
				header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
				header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $spendingId);
				header('Content-Type: application/json');
				echo (json_encode(
					array(
						"id_spending" => $spendingId,
						"type_spending" => $spending->getType(),
						"date_spending" => $spending->getDate(),
						"qty_spending" => $spending->getQuantity(),
						"description_spending" => $spending->getDescription(),
						"doc_name_spending" => $spending->getDocName(),
						"owner" => $spending->getOwner()->getAlias()
					)
				));

			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 400 Not Found');
				header('Content-Type: application/json');
				$errors = array();
				$errors["owner"] = "The owner does not exist";
				echo(json_encode($errors));
			}

		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}





	
	public function readSpending($spendingId) {
		$currentUser = parent::authenticateUser();

		// find the Spending object in the database
		$spending = $this->spendingMapper->findById($spendingId);
		if ($spending == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Spending with id ".$spendingId." not found");
			return;
		}

		// Check if the Spending owner is the currentUser (in Session)
		if ($spending->getOwner()->getAlias() != $currentUser->getAlias() && $currentUser->getRole() != "admin") {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not the author of this spending or you are not admin");
			return;
		}

		$spending_array = array(
			"id_spending" => $spending->getId(),
			"type_spending" => $spending->getType(),
			"date_spending" => $spending->getDate(),
			"qty_spending" => $spending->getQuantity(),
			"description_spending" => $spending->getDescription(),
			"doc_name_spending" => $spending->getDocName(),
			"owner" => $spending->getOwner()->getAlias()
		);

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($spending_array));
	}







	public function updateSpending($spendingId, $data) {
		$currentUser = parent::authenticateUser();

		$spending = $this->spendingMapper->findById($spendingId);
		if ($spending == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Spending with id ".$spendingId." not found");
			return;
		}



		// Check if the Spending owner is the currentUser (in Session)
		if ($spending->getOwner()->getAlias() != $currentUser->getAlias() && $currentUser->getRole() != "admin") {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not the author of this spending or you are not admin");
			return;
		}


		$spending->setType($data->type_spending);
		$spending->setDate($data->date_spending);
		$spending->setQuantity($data->qty_spending);
		$spending->setDescription($data->description_spending);
		
		try {
			if(isset($data->doc_name_spending) && isset($data->doc_data)){
				$base64file = new FileUpload($data->doc_data);

				// validate FileUpload object
				$base64file->checkIsValidForUpload();

				// Name of the previous file 
				$oldDocNameServ = $spending->getDocServer();
				
				/// save the FileUpload object into the server
				$fileNameServer = $this->fileUploadMapper->update($base64file,$oldDocNameServ);

				$spending->setDocName($data->doc_name_spending);
				$spending->setDocServer($fileNameServer);

			}

			// validate Spending object
			$spending->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->spendingMapper->update($spending);

			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');

		}catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function deleteSpending($spendingId) {
		$currentUser = parent::authenticateUser();
		$spending = $this->spendingMapper->findById($spendingId);

		if ($spending == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Spending with id ".$spendingId." not found");
			return;
		}
		// Check if the Spending author is the currentUser (in Session)
		if ($spending->getOwner()->getAlias() != $currentUser->getAlias() && $currentUser->getRole() != "admin") {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not the author of this spending or you are not admin");
			return;
		}

		// Delete spending doc
		if ($spending->getDocServer() != NULL) {
			$this->fileUploadMapper->delete($spending->getDocServer());
		}

		$this->spendingMapper->delete($spending);

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}








	public function downloadFileSpending($spendingId)
	{
		$currentUser = parent::authenticateUser();
		$spending = $this->spendingMapper->findById($spendingId);

		if ($spending == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Spending with id ".$spendingId." not found");
			return;
		}

		// Check if the Spending owner is the currentUser (in Session)
		if ($spending->getOwner()->getAlias() != $currentUser->getAlias() && $currentUser->getRole() != "admin") {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not the author of this spending or you are not admin");
			return;
		}

		$file = $this->fileUploadMapper->findByName($spending->getDocServer());
		
		if ($file != NULL) {
			$file_name_user = $spending->getDocName();

			$pattern = '/\.[A-Za-z]+/';
			preg_match($pattern, $file_name_user, $matches, PREG_OFFSET_CAPTURE);


			/* DO NOT TOUCH, it works */
			$mime = '';
			if ($matches[0][0] == ".pdf") {
				$mime = 'application/pdf';
			} else if ($matches[0][0]  == ".png") {
				$mime = 'image/png';
			} else if ($matches[0][0] == ".jpg") {
				$mime = 'image/jpeg';
			}

			header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
			header('Content-Type: application/json');

			echo(json_encode(array(
				"doc_data"=>"data:".$mime.";base64,".$file->getData())
			));

			exit;
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("File from the spending with id ".$spendingId." not found");
			return;
		}
	
	}

	public function generateGraphic()
	{
		$currentUser = parent::authenticateUser();

		//Check if there's any query params and handle them
		if (isset($_SERVER['QUERY_STRING']) && (strlen($_SERVER['QUERY_STRING']) > 0)) {
			$this->generateQueryGraphic($currentUser);
		}

		//If there's none query params, show the results of the last year
		else {
		
			$toret_array = array(
				"months" => array(),
				"gastos" => array()
			);

			$initial_date = date('y', time()) . "-01-01";
			$end_date = date('y', time()) . "-12-31";

			$diff_total = $this->affectedMonths($initial_date, $end_date);

			/* Array of zeros for each type of spending */
			$zeros_array = $this->fillZeroes($diff_total);

			$types = array("FOOD", "FUEL", "COMMUNICATIONS", "SUPPLIES", "FREE TIME");
			$output_array = array(
				"FOOD" => $zeros_array,
				"FUEL" => $zeros_array,
				"COMMUNICATIONS" => $zeros_array,
				"SUPPLIES" => $zeros_array,
				"FREE TIME" => $zeros_array
			);

			/* Get the MonthSpending objects from the database */
			$msp_array = $this->spendingMapper->findMonthSpendings($currentUser->getAlias(), $initial_date, $end_date, $types);

			/* Array of Strings for x-axis */
			$label_array = $this->parseMonths($initial_date, $diff_total);

			/* Parse MonthSpending objects */
			$output_array = $this->pushData($msp_array, $label_array, $output_array);

			$toret_array["months"] = $label_array;

			/* Push spending data to toret_array */
			foreach ($output_array as $key => $value) {
				array_push($toret_array["gastos"], array(
					"name" => $key,
					"data" => $value
				));
			}

			echo(json_encode($toret_array));

		}

		exit;
	}

	public function generateQueryGraphic($currentUser)
	{
		//$currentUser = parent::authenticateUser();

		$toret_array = array(
			"months" => array(),
			"gastos" => array()
		);

		//Separate all the parts of the query and insert them into an array
		$aux = explode("&",$_SERVER['QUERY_STRING']);
		$aux2 = array();

		for ($i=0; $i < count($aux); $i++) { 
			$aux3 = explode("=",$aux[$i]);
			$aux2[$aux3[0]] = $aux3[1];
		}

		$idate = array_key_exists("date1", $aux2) ? $aux2["date1"] : null;
		$fdate = array_key_exists("date2", $aux2) ? $aux2["date2"] : null;
		$food = array_key_exists("food_chk", $aux2) ? $aux2["food_chk"] : "false";
		$fuel = array_key_exists("fuel_chk", $aux2) ? $aux2["fuel_chk"] : "false";
		$coms = array_key_exists("comm_chk", $aux2) ? $aux2["comm_chk"] : "false";
		$supp = array_key_exists("supp_chk", $aux2) ? $aux2["supp_chk"] : "false";
		$ftime = array_key_exists("ft_chk", $aux2) ? $aux2["ft_chk"] : "false";

		$initial_date = $idate;
		$end_date = $fdate;

		try{
			/* validate input Dates */
			$this->checkIsValidForGraph($initial_date,$end_date); // if it fails, ValidationException

			$diff_total = $this->affectedMonths($initial_date, $end_date);

			/* Array of zeros for each type of spending */
			$zeros_array = $this->fillZeroes($diff_total);

			$types = array();
			$output_array = array();

			$chk_types = array(); //array

			strcmp($food, "on") == 0 ? array_push($chk_types, "FOOD") : "";
			strcmp($fuel, "on") == 0 ? array_push($chk_types, "FUEL") : "";
			strcmp($coms, "on") == 0 ? array_push($chk_types, "COMMUNICATIONS") : "";
			strcmp($supp, "on") == 0 ? array_push($chk_types, "SUPPLIES") : "";
			strcmp($ftime, "on") == 0 ? array_push($chk_types, "FREE TIME") : "";

			/* Iterate the maximum number of checked types */
			for ($i = 0; $i < 5; $i++) {
				if(isset($chk_types[$i])){
					switch($chk_types[$i]){
						case "FOOD":
							array_push($types, "FOOD");
							$output_array += array("FOOD" => $zeros_array);
							break;
						case "FUEL":
							array_push($types, "FUEL");
							$output_array += array("FUEL" => $zeros_array);
							break;
						case "COMMUNICATIONS":
							array_push($types, "COMMUNICATIONS");
							$output_array += array("COMMUNICATIONS" => $zeros_array);
							break;
						case "SUPPLIES":
							array_push($types, "SUPPLIES");
							$output_array += array("SUPPLIES" => $zeros_array);
							break;
						case "FREE TIME":
							array_push($types, "FREE TIME");
							$output_array += array("FREE TIME" => $zeros_array);
							break;
					}
				} else{
					array_push($types, "");
				}
			}

			/* Get the MonthSpending objects from the database */
			$msp_array = $this->spendingMapper->findMonthSpendings($currentUser->getAlias(), $initial_date, $end_date, $types);

			/* Array of Strings for x-axis */
			$label_array = $this->parseMonths($initial_date, $diff_total);

			/* Parse MonthSpending objects */
			$output_array = $this->pushData($msp_array, $label_array, $output_array);

			$toret_array["months"] = $label_array;

			/* Push spending data to toret_array */
			foreach ($output_array as $key => $value) {
				array_push($toret_array["gastos"], array(
					"name" => $key,
					"data" => $value
				));
			}

			echo(json_encode($toret_array));

			exit;
		} catch(ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	private function checkIsValidForGraph($initial_date,$end_date){

		$errors = array();
		$compare1 = null;
		$compare2 = null;

		/* Validation of DATE1 */
		$fecha_check = strlen(trim($initial_date));
		if ($fecha_check == 0 ) {
			$errors["date1"] = "Initial date is mandatory";
		}else if($fecha_check > 10){
			$errors["date1"] = "Initial date must not be greater than 10 characters";
		}else{
			$pattern_rgx_date = "/([0-9]{4}-[0-9]{2}-[0-9]{2})/";
			preg_match($pattern_rgx_date, $initial_date, $matches, PREG_OFFSET_CAPTURE);
			if (empty($matches)) {
				$errors["date1"] = "The format of the initial date must be valid (yyyy-mm-dd)";
			}else{
				$fields = explode("-",$initial_date);
				if (!checkdate($fields[1], $fields[2], $fields[0])) {
					$errors["date1"] = "Initial date must be valid";
				}
				else {
					$compare1 = $initial_date;
				}
			}
		}

		/* Validation of DATE2 */
		$fecha_check = strlen(trim($end_date));
		if ($fecha_check == 0 ) {
			$errors["date2"] = "End date is mandatory";
		}else if($fecha_check > 10){
			$errors["date2"] = "End date must not be greater than 10 characters";
		}else{
			$pattern_rgx_date = "/([0-9]{4}-[0-9]{2}-[0-9]{2})/";
			preg_match($pattern_rgx_date, $end_date, $matches, PREG_OFFSET_CAPTURE);
			if (empty($matches)) {
				$errors["date2"] = "The format of the end date must be valid (yyyy-mm-dd)";
			}else{
				$fields = explode("-",$end_date);
				if (!checkdate($fields[1], $fields[2], $fields[0])) {
					$errors["date2"] = "End date must be valid";
				}
				else {
					$compare2 = $end_date;
				}
			}
		}

		if(isset($compare1) && isset($compare2) && ($compare2 < $compare1)){
			$errors["date2"] = "End date can not be less than the initial date";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "search dates are not valid");
		}

	}

	private function affectedMonths($initial_date, $end_date)
	{
		// Get the total of months that affect the query
		$m_start = date('m', strtotime($initial_date));
		$m_end = date('m', strtotime($end_date));

		$y_start = date('y', strtotime($initial_date));
		$y_end = date('y', strtotime($end_date));

		$affected_months = ($m_end - $m_start) + 1;

		$affected_years = ($y_end - $y_start);

		$diff_total = $affected_years * 12 + $affected_months;

		return $diff_total;
	}

	private function fillZeroes($diff_total)
	{
		$zeros_array = array();
		for ($i = 0; $i < $diff_total; $i++) {
			array_push($zeros_array, 0);
		}
		return $zeros_array;
	}

	private function parseMonths($initial_date, $diff_total)
	{
		$label_array = array();
		for ($i = 0; $i < $diff_total; $i++) {
			$temp_date = strtotime("+$i months", strtotime($initial_date));
			$str_month = date("F", $temp_date);
			$year = date("Y", $temp_date);
			array_push($label_array, $str_month . " " . $year);
		}
		return $label_array;
	}

	private function pushData($msp_array, $label_array, $output_array)
	{
		foreach ($msp_array as $month_spending) {

			$month_num = $month_spending->getMonth();
			$dateObj   = DateTime::createFromFormat('!m', $month_num);
			$month_name = $dateObj->format('F');

			$year_num = $month_spending->getYear();

			$insert_index = array_search($month_name . " " . $year_num, $label_array);

			$output_array[$month_spending->getType()][$insert_index] = floatval($month_spending->getSum());
		}
		return $output_array;
	}
}


// URI-MAPPING for this Rest endpoint
$spendingRest = new SpendingRest();
URIDispatcher::getInstance()
->map("GET",	"/spending", array($spendingRest,"getSpendings"))
->map("GET",	"/spending/graphic", array($spendingRest,"generateGraphic"))
->map("GET",	"/spending/$1", array($spendingRest,"readSpending"))
->map("GET",	"/spending/$1/file", array($spendingRest,"downloadFileSpending"))
->map("POST", "/spending", array($spendingRest,"createSpending"))
->map("PUT",	"/spending/$1", array($spendingRest,"updateSpending"))
->map("DELETE", "/spending/$1", array($spendingRest,"deleteSpending"));