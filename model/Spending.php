<?php
// file: model/Spending.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Spending
*
* Represents a Spending in the Application. A Spending is generated
* by a specific User (owner)
*
* @author Juan Yuri Díaz Sánchez
*/
class Spending {

	/**
	* The id of this spending
	* @var integer
	*/
	private $id;

	/**
	* The type of this spending
	* @var string
	*/
	private $type;

	/**
	* The date of this spending
	* @var string
	*/
	private $date;

    /**
    * The quantity of this spending
    * @var float
    */
    private $qty;

    /**
    * The description of this spending
    * @var string
    */
    private $description;

    /**
    * The document name of this spending
    * @var string
    */
    private $doc_name;

	/**
    * The document name of this spending
    * @var string
    */
    private $doc_server;

    /**
	* The owner of this spending
	* @var User
	*/
	private $owner;

	/**
	* The constructor
	*
	* @param integer $id The id of the spending
	* @param string $type The type of the spending
	* @param string $date The date of the spending
	* @param float $qty The quantity of the spending
	* @param string $description The description of the spending
	* @param string $doc_name The document name of the spending of the user
	* @param string $doc_server The document name of the spending in the server
    * @param User $owner The owner of the spending
	*/
    public function __construct($id_spending=NULL, $type_spending=NULL, $date_spending=NULL, 
                                $qty_spending=NULL, $description_spending=NULL, 
								$doc_name_spending=NULL, $doc_server=NULL, User $owner_spending=NULL) {
        $this->id = $id_spending;
        $this->type = $type_spending;
        $this->date = $date_spending;
        $this->qty = $qty_spending;
        $this->description  = $description_spending;
		$this->doc_name = $doc_name_spending;
		$this->doc_server = $doc_server;
        $this->owner = $owner_spending;
    }

	/**
	* Gets the id of this spending
	*
	* @return integer The id of this spending
	*/
	public function getId() {
		return $this->id;
	}

	/**
	* Gets the type of this spending
	*
	* @return string The type of this spending
	*/
	public function getType() {
		return $this->type;
	}

	/**
	* Sets the type of this spending
	*
	* @param string $type the type of this spending
	* @return void
	*/
	public function setType($type) {
		$this->type = $type;
	}

	/**
	* Gets the date of this spending
	*
	* @return string The date of this spending
	*/
	public function getDate() {
		return $this->date;
	}

	/**
	* Sets the date of this spending
	*
	* @param string $date the date of this spending
	* @return void
	*/
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	* Gets the quantity of this spending
	*
	* @return float The quantity of this spending
	*/
	public function getQuantity() {
		return $this->qty;
	}

	/**
	* Sets the quantity of this spending
	*
	* @param float $qty the quantity of this spending
	* @return void
	*/
	public function setQuantity($qty) {
		$this->qty = $qty;
	}

	/**
	* Gets the description of this spending
	*
	* @return string The description of this spending
	*/
	public function getDescription() {
		return $this->description;
	}

	/**
	* Sets the quantity of this spending
	*
	* @param string $description the description of this spending
	* @return void
	*/
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	* Gets the document name of the user of this spending
	*
	* @return string The document name of this spending
	*/
	public function getDocName() {
		return $this->doc_name;
	}

	/**
	* Sets the document name of the user of this spending
	*
	* @param string $doc_name the document name of this spending
	* @return void
	*/
	public function setDocName($doc_name) {
		$this->doc_name = $doc_name;
	}

	/**
	* Gets the document name of the server of this spending 
	*
	* @return string The document name of this spending
	*/
	public function getDocServer() {
		return $this->doc_server;
	}

	/**
	* Sets the document name of the server of this spending
	*
	* @param string $doc_server the document name of this spending
	* @return void
	*/
	public function setDocServer($doc_server) {
		$this->doc_server = $doc_server;
	}

	/**
	* Gets the owner of this spending
	*
	* @return User The owner of this spending
	*/
	public function getOwner() {
		return $this->owner;
	}

	/**
	* Sets the owner of this spending
	*
	* @param User $owner the owner of this spending
	* @return void
	*/
	public function setOwner(User $owner) {
		$this->owner = $owner;
	}

	



	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForCreate() {
		$errors = array();

		/* Validation of TYPE */
		if (strlen(trim($this->type)) == 0) {
			$errors["type"] = "Type is mandatory";
		}else{
			$typePool = array("FOOD","COMMUNICATIONS","FUEL","FREE TIME","SUPPLIES");
			if (!in_array($this->type, $typePool)) {
				$errors["type"] = "Invalid type of spending";
			}
		}
		
		/* Validation of DATE */
		$fecha_check = strlen(trim($this->date));
		if ($fecha_check == 0 ) {
			$errors["date"] = "Date is mandatory";
		}else if($fecha_check > 10){
			$errors["date"] = "Date must not be greater than 10 characters";
		}else{
			$pattern_rgx_date = "/([0-9]{4}-[0-9]{2}-[0-9]{2})/";
			preg_match($pattern_rgx_date, $this->date, $matches, PREG_OFFSET_CAPTURE);
			if (empty($matches)) {
				$errors["date"] = "The format of the date must be valid (yyyy-mm-dd)";
			}else{
				$fields = explode("-",$this->date);
				if (!checkdate($fields[1], $fields[2], $fields[0])) {
					$errors["date"] = "Date must be valid";
				}
			}
		}
		


		/* Validations of QTY */
		if ($this->qty == NULL ) {
			$errors["qty"] = "Quantity is mandatory";
		}else if ( str_contains(strval($this->qty),",") ) {
			$errors["qty"] = "Quantity must be written with .";
		}else if ( str_contains(strval($this->qty),"-") ){
			$errors["qty"] = "Quantity must not contain -";
		}else if (floatval($this->qty) == 0.00) {
			$errors["qty"] = "Quantity can not be 0";
		}else{
			$pattern_rgx_date = "/([0-9]{1,3}\.([0-9]){1,2})/";
			preg_match($pattern_rgx_date, $this->qty, $matches, PREG_OFFSET_CAPTURE);
			if (empty($matches)) {
				$errors["qty"] = "The format of the quantity must be valid (#####.##)";
			} else if (strlen(explode(".",$this->qty)[1]) > 2){
				$errors["qty"] = "The quantity can not have more than 2 decimals";
			} else if (strlen(explode(".",$this->qty)[0]) > 3) {
				$errors["qty"] = "The quantity can not have more than 3 integer digits";
			}
		}

		/* Validation of DESCRIPTION */
		if( isset($this->description) && strlen(trim($this->description)) > 150 ){
			$errors["description"] = "Description must not be greater than 150 characters";
		}
		
		/* Validation of UPLOAD FILE */
		if (isset($this->doc_name) && $this->doc_name !== ""){
			if (strlen(trim($this->doc_name)) > 100) {
				$errors["doc_name"] = "File name must not be greater than 100 characters";
			}else{
				$extensions = 'pdf|png|jpg';
				$pattern = '/^[a-zA-Z0-9_ -]+\.('.$extensions.')$/';
				
				preg_match($pattern, $this->doc_name, $matches, PREG_OFFSET_CAPTURE);
				if (empty($matches)) {
					$errors["doc_name"] = "File name must contain alphanumeric characters and a valid extension (pdf,png,jpg)";
				}
			}
		}

		/* Validation of OWNER */
		if( isset($this->owner) && strlen(trim($this->owner->getAlias())) < 3 ){
			$errors["owner"] = "Owner must not be fewer than 3 characters";
		}
		if( isset($this->owner) && strlen(trim($this->owner->getAlias())) > 25 ){
			$errors["owner"] = "Owner must not be greater than 25 characters";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "spending is not valid");
		}

	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpdate() {
		$errors = array();

		if (!isset($this->id)) {
			$errors["id"] = "Id is mandatory";
		}

		try{
			$this->checkIsValidForCreate();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "spending is not valid");
		}
	}
}
