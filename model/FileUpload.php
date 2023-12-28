<?php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class FileUpload
*
* Represents a FileUpload in the server
*
* @author Jose Ángel Pérez Garrido
*/
class FileUpload { 

	/**
	* The data of the file uploaded
	* @var string
	*/
	private $data;

	/**
	* The constructor
	*
	* @param string $data The data of the file uploaded
	*/
	public function __construct($data=NULL) {
		$this->data = $data;
	}

	/**
	* Gets the data of this file
	*
	* @return string The data of this dile
	*/
	public function getData() {
		return $this->data;
	}

	/**
	* Sets the data of this file
	*
	* @param string $data The data of the file uploaded
	* @return void
	*/
	public function setData($data) {
		$this->data = $data;
	}

	private function checkIsBase64($string){
		// Check if there are valid base64 characters
		// if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;
		// Decode the string in strict mode and check the results
		$decoded = base64_decode($string, true);
		if(false === $decoded) return false;
		// Encode the string again
		if(base64_encode($decoded) != $string) return false;
		return true;
	}

	private function checkIsSizeValid(){
		$file_size = 8000000;
		$size = @getimagesize($this->data);
		
		return ($size['bits'] < $file_size);
	}
	
	private function checkIsFileTypeValid(){
		$mime_type = @mime_content_type($this->data);
		$allowed_file_types = ['image/png', 'image/jpeg', 'application/pdf'];

		return (in_array($mime_type, $allowed_file_types));
	}

	/**
	* Checks if the current file instance is valid
	* for being uploaded in the server
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpload(){
		
		$errors = array();

		//$data = "data:mime;base64,".$base64string;

		/* if(!$this->checkIsSizeValid()){
			$errors["doc_name"] = "File size must be smaller than 8 MB";
		}
 */
		if(!$this->checkIsFileTypeValid()){
			$errors["doc_name"] = "File name must contain alphanumeric characters and a valid extension (pdf,png,jpg)";
		}

		list(,$data) = explode(';', $this->data);
		list(,$base64string) = explode(',', $data);

		if(!$this->checkIsBase64($base64string)){ 
			$errors["doc_name"] = "File must be encoded in Base64";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "spending is not valid");
		}
	}
	
}