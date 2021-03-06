<?php
/**
 * Institution
 *
 * @package  Institution
 * @author   phe
 */
namespace Ida\Institution;

use Zend\Config\Reader\Ini;
use Zend\Config\Exception;

/**
 * Delivers institution details
 *
 * @package  Institution
 * @author   phe
 */
class Institution
{

    /**
     * Identifier of an institution
     *
     * @var String
     */
    private $institutionId;

    /**
     * Language identifier
     *
     * @var String
     */
    private $language;

    /**
     * Local path where the institutions are stored
     *
     * @var String
     */
    private $institutionDir;

    /**
     * Constructor
     *
     * @param String $institutionId Identifier of an institution
     * @param String $language Language identifier
     */
    public function __construct($institutionId, $language)
    {
        $this->institutionId = $institutionId;
        $this->language = $language;
        $this->institutionDir = APPLICATION_PATH . "/module/Ida/data/institutions/";
    }

    /**
     * Return all details about an institution, if a corresponding
     * file exists in the file system.
     *
     * @return array
     */
    public function getInstitutionDetails()
    {
        $details = array();
        $fileName = $this->institutionDir . $this->getInstitutionFileName();

        // Check if the institution file exists
        if (is_readable($fileName))
        {
            // Try to parse the file and return the result
            $reader = new Ini();

            try {
                $details = (array) $reader->fromFile($fileName);
            }
            catch (Exception\RuntimeException $error)
            {
                error_log( "getInstitutionDetails(): error reading from file " . $fileName );
            }
        }
        else
        {
            error_log( "can not read file " . $fileName );
        }

        return $details;
    }

    private function sortBySubArrayValue($array, $key) {
        $sortArray = array();

        foreach($array as $k => $a) {
            $sortArray[$k] = $this->normalizeSortString($a[$key]);
        }

        array_multisort($sortArray, SORT_ASC, SORT_STRING, $array);

        return $array;
    }

    private function normalizeSortString($string)
    {
    	$string = strtoupper($string);
    	$aSearch   = array("Ä","Ö","Ü","ß","-","*"," ");
    	$aReplace  = array("AE","OE","UE","SS","","","");
    	$string = str_replace($aSearch, $aReplace, $string);
    	return $string;
    }
    
    /**
     * Get all institutions from .ini files and sort them by country and name
     *
     * @param string $sortFirst
     * @param string $sortSecond
     * @return array
     */
    public function getAllInstitutions($sortFirst = "country_norm", $sortSecond = "name_norm")
    {
        $institutionDirContent = scandir($this->institutionDir);
        $fileEnding = "_" . $this->language . ".ini";
        $fileEndingIndex = -1 * strlen($fileEnding);
        $institutions = array();

        // Get all institutions for the current language
        foreach ($institutionDirContent as $file)
        {
            if ($fileEnding === substr($file, $fileEndingIndex)) {
                $this->institutionId = substr($file, 0, $fileEndingIndex);
                $institution = $this->getInstitutionDetails();
                $institution["id"] = $this->institutionId;
                // Use case insensitive $sortSecond as first array element which is
                // used in array_multisort() as second sort condition
                $institution["name_norm"] = $this->normalizeSortString($institution["name"]);
                $institution["country_norm"] = $this->normalizeSortString($institution["country"]);
                $institutions[] = $institution;
            }
        }
		$institutions = $this->array_orderby($institutions, $sortFirst, SORT_ASC, $sortSecond, SORT_ASC);
				
        return $institutions;
    }
    
    private function array_orderby()  {
    	$args = func_get_args();
    	$data = array_shift($args);
    	foreach ($args as $n => $field) {
    		if (is_string($field)) {
    			$tmp = array();
    			foreach ($data as $key => $row)
    				$tmp[$key] = $row[$field];
    				$args[$n] = $tmp;
    		}
    	}
    	$args[] = &$data;
    	call_user_func_array('array_multisort', $args);
    	return array_pop($args);
    }

    /**
     * Return the file name for a given institution id and
     * remove all chars which can cause (security) problems
     *
     * @return string
     */
    private function getInstitutionFileName()
    {
        return preg_replace("/[^0-9a-zA-Z_\-öäüßÄÖÜ]/", "", $this->institutionId) . "_" . $this->language . ".ini";
    }
}