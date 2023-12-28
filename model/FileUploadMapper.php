<?php
// file: model/FileUploadMapping.php

/**
* Class FileUploadMapping
*
* Server interface for FileUpload entities
*
*
* @author Jose Ángel Pérez Garrido
*/
class FileUploadMapper{

	public function __construct(){
	}

	/**
	 * Loads a File from the server given its server name
	 *
	 * @param String $docServerName The specific file to find
	 * @return FileUpload The FileUpload instances.
	 * NULL if the FileUpload is not found
	 */
	public function findByName($docServerName)
	{
		$file = $_SERVER['DOCUMENT_ROOT']."/spending_manager/documents/" . $docServerName;

		if(file_exists($file)){
			return new FileUpload(base64_encode(file_get_contents($file)));
		}

		return NULL;
	}

	/**
	 * Saves a File into the server
	 *
	 * @param FileUpload $file The file to be saved
	 * @return String The server name for the new file
	 */
	public function save(FileUpload $file)
	{
		// Upload File variables (Path, name, extension)
        list($type, $data) = explode(';', $file->getData());
        list(,$extension) = explode('/',$type);
        list(,$base64string) = explode(',', $data);
        
        $fileNameServer ='';
    
        $target_dir = $_SERVER['DOCUMENT_ROOT']."/spending_manager/documents/";
        $target_file_uniqid = uniqid();

        $fileNameServer = strval($target_file_uniqid . "." . $extension);
        $base64string = base64_decode($base64string);
        file_put_contents($target_dir . $fileNameServer, $base64string);

		return $fileNameServer;
	}

	/**
	 * Updates a File from the server
	 *
	 * @param FileUpload $file The file to be updated
	 * @return String The server name for the new file
	 */
	public function update(FileUpload $file, $oldDocNameServ)
	{
		//add new file
		$fileNameServer = $this->save($file);

		//remove previous file
		$this->delete($oldDocNameServ);

		return $fileNameServer;
	}

	/**
	 * Deletes a File from the server
	 *
	 * @param String $docServerName The name of the file to be deleted
	 * @return void
	 */
	public function delete($docServerName)
	{
		if (isset($docServerName) && file_exists($_SERVER['DOCUMENT_ROOT']."/spending_manager/documents/" . $docServerName)) {
			unlink($_SERVER['DOCUMENT_ROOT']."/spending_manager/documents/" . $docServerName);
		}
	}
}

