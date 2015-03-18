<?php

/**
 * Ouvre un fichier et converti son contenu en UTF-8.
 * @param string $fileName Path vers le fichier
 * @param string $openFileEncoding L'encodage du fichier actuel.
 * @return resource Ressource vers le fichier réencodé.
 */
function utf8_fopen_read($fileName, $openFileEncoding) {
	$fileContent = file_get_contents($fileName);
	// 	$fc = iconv('CP1252', 'utf-8', $fileContent);
	// 	$fc = iconv('MS-ANSI', 'utf-8', $fileContent);
	$fc = iconv($openFileEncoding, 'utf-8', $fileContent);
	$handle=fopen("php://memory", "rw");
	fwrite($handle, $fc);
	fseek($handle, 0);
	return $handle;
}



/**
 * Classe de lecture d'un fichier CSV
 * @author XFR3015001 - Basile Parent
 */
class CsvReader {
	
	/** @var string Chemin vers le fichier csv */
	private $csvFilePath;
	/** @var integer $nbHeaderLines Le nombre de lignes d'entête (à sauter pendant le parsing). */
	private $nbHeaderLines;
	/** @var array L'ensemble des lignes du fichier CSV (si fonction parseCsvFile() appelée). */
	private $lines = null;
	
	
	/**
	 * Constructeur de la classe
	 * @param string $csvFilePath Chemin vers le fichier CSV 
	 * @param bool $parseFile Si true : le fichier CSV est parsé en entier dans le constructeur 
	 * 	et stocké dans un tableau. Sinon, il faut utiliser les méthodes de lecture ligne par ligne. (à faire)
	 * @param integer $nbHeaderLines Le nombre de lignes d'entête (à sauter pendant le parsing).
	 * @throws Exception Si le fichier n'existe pas, une exception est lancée.
	 */
	public function __construct($csvFilePath, $parseFile = true, $nbHeaderLines = 1) {
		if (!file_exists(realpath($csvFilePath))) {
			throw new Exception("Le fichier ".realpath($csvFilePath)." n'existe pas.");
		}
		
		$this->csvFilePath = realpath($csvFilePath);
		$this->nbHeaderLines = $nbHeaderLines;
		
		if ($parseFile) {
			$this->parseCsvFile();
		}
	}
	
	
	
	/**
	 * Parse tout le fichier CSV et mets le résultat dans l'attribut lines.
	 * @throws Exception Si le fichier ne peut pas être ouvert en lecture, une exception est lancée.
	 */
	public function parseCsvFile() {
		if (!defined('CSV_FILE_ENCODING_CHARSET')) {
			throw new Exception("La constante CSV_FILE_ENCODING_CHARSET n'a pas été définie");
		}
		if (($handle = utf8_fopen_read($this->csvFilePath, CSV_FILE_ENCODING_CHARSET)) !== FALSE) {
			$this->lines = array();
			$rowNumber = 1;
			
		    while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
		    	if ($rowNumber > $this->nbHeaderLines) {
		    		$this->lines[] = $row;
		    		
		    		/*
		    		$num = count($row);
		    		echo "<p> $num champs à la ligne $rowNumber: <br /></p>\n";
		    		for ($c=0; $c < $num; $c++) {
		    			echo $row[$c] . "<br />\n";
		    		}
		    		*/
		    	}
		        
		        $rowNumber++;
		    }
		    fclose($handle);
		} else {
			throw new Exception("Le fichier ".realpath($this->csvFilePath)." n'existe pas.");
		}
	}
	
	
	/**
	 * Getter
	 * @return array
	 */
	public function getLines() {
		return $this->lines;
	}
	
}

?>