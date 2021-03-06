<?php
/**
 * Abstract super class for Record Drivers for IDA Items.
 * User: boehm
 * Date: 6/4/14
 * Time: 3:21 PM
 */
namespace Ida\RecordDriver;

use Ida\Institution\Institution,
    VuFind\RecordDriver\SolrDefault,
    VuFind\View\Helper\Root\RecordLink;

abstract class SolrIDA extends SolrDefault
{

    function __construct($mainConfig = null, $recordConfig = null,
                         $searchSettings = null)
    {
        $this->formats = $mainConfig->Format2Thumbs->formats;
        parent::__construct($mainConfig, $recordConfig, $searchSettings);
    }

    public function isSubRecord() {

        return isset($this->fields['_isSubRecord']) ? $this->fields['_isSubRecord'] : false;
    }

    public function getSubRecords() {

        return isset($this->fields['_subRecords']) ? $this->fields['_subRecords'] : null;
    }

    public function hasSubRecords() {

        if (null !== ($collection = $this->getSubRecords())) {

            return 0 < $collection->count();
        }

        return false;
    }
    
    public function getGroupID()
    {
    	return $this->getSingleValuedField("groupID");
    }

    public function getAmazonAffiliateId()
    {
        $amazonassociate = false;
        if (isset($this->mainConfig->Content->amazonassociate)) {
            $amazonassociate = $this->mainConfig->Content->amazonassociate;
        }
        return $amazonassociate;
    }

    /**
    * Shorthand for record type
    */
    public function isLibrary()
    {
        return $this->getRecordType() === "library";
    }

    /**
    * Shorthand for record type
    */
    public function isArchive()
    {
        return $this->getRecordType() === "archive";
    }

    /**
    * Shorthand for record type
    */
    public function isSystematics()
    {
        return $this->getRecordType() === "systematics";
    }

     /**
     * Deduplicate author information into associative array with main/
     * additional keys.
     *
     * @return array [main] and [additional] authors
     */
    public function getDeduplicatedAuthors()
    {
        $authors = array(
            'main' => $this->getPrimaryAuthor(),
            'additional' => $this->getAdditionalAuthors(),
        );

        // The additional author array may contain a primary author;
        // let's be sure we filter out duplicate values.
        $duplicates = array();
        if (!empty($authors['main'])) {
            $duplicates[] = $authors['main'];
        }
        if (!empty($authors['additional'])) {
            $authors['additional'] = array_diff($authors['additional'], $duplicates);
        }

        return $authors;
    }

    public function getAdditionalAuthors()
    {
        return $this->getMultiValuedField("author_additional");
    }

    /**
    * return array [[topic], [geo], [person]]
    */
    public function getAllSubjectHeadings()
    {

        $topic = $this->getTopics();
        $geo = $this->getGeographicTopics();
        $person = $this->getPersonTopics();

        $retval = array();
        if (!empty($topic))
        {
            $retval["topic"] = $topic;
        }
        if (!empty($geo))
        {
            $retval["geo"] = $geo;
        }
        if (!empty($person))
        {
            $retval["person"] = $person;
        }

        return $retval;
    }

    public function getAccess()
    {
        return $this->getMultiValuedField("url");
    }
    
    public function getDisplayFormats()
    {
        return $this->getMultiValuedField("format");
    }
    
    public function getSearchFilter()
    {
        return $this->getSingleValuedField('searchfilter');
    }

    public function getJournalCore()
    {
        return $this->getSingleValuedField('journalCore');
    }

    public function getIssuesInstitutions()
    {
        return $this->getSingleValuedField('issuesInstitutions');
    }

    public function getIssuesGlobal()
    {
        return $this->getSingleValuedField('issuesGlobal');
    }

     public function getEditions()
    {
        return $this->getMultiValuedField("edition");
    }
    
     public function getOutOfStocks()
    {
        return $this->getMultiValuedField("outOfStocks");
    }
    
     public function getPublicationFrequency()
    {
        return $this->getMultiValuedField("publicationFrequency");
    }
    
    public function getInsertOf()
    {
    	return $this->getMultiValuedField("insertOf");
    }
    
     public function getSpecialIssue()
    {
        return $this->getMultiValuedField("specialIssue");
    }
    
     public function getSpecificMaterialDesignations()
    {
        return $this->getMultiValuedField("specificMaterialDesignation");
    }

    public function getTranslatedTopic()
    {
        return $this->getMultiValuedField("translatedTopic");
    }
    
    public function getContributors()
    {
        return $this->getMultiValuedField("contributor");
    }
    
    public function getContributorsNoFacet()
    {
        return $this->getMultiValuedField("contributorNoFacet");
    }
    
    public function getProvenances()
    {
        return $this->getMultiValuedField("provenance");
    }

    public function getRecordCreationDate()
    {
        return $this->getSingleValuedField("recordCreationDate");
    }

    public function getPlacesOfPublication()
    {
        return $this->getMultiValuedField("placeOfPublication");
    }

    public function getSeriesNr()
    {
        return $this->getSingleValuedField('seriesNr');
    }

    public function getInstitutionsFull()
    {
        return $this->getMultiValuedField("institutionFull");
    }
    
    public function getInstitution()
    {
        return $this->getMultiValuedField("institution");
    }

    public function getInstitutionDetails($institutionId, $language)
    {
        $institution = new Institution($institutionId, $language);

        return $institution->getInstitutionDetails();
    }
    
    public function getTranslatedTerms()
    {
        return $this->getMultiValuedField("translatedTerms");
    }

    public function getDisplayTitle()
    {
        return isset($this->fields['title_sub']) ?
            $this->getShortTitle() . " : " . $this->getTitleSub() : $this->getTitle();
    }

    public function getRecordType()
    {
        return $this->getSingleValuedField('recordtype');
    }

    public function getTitleSub()
    {
        return $this->getSingleValuedField('title_sub');
    }
    
    public function getLocation()
    {
        return $this->getSingleValuedField('location');
    }

    public function getFormerTitles()
    {
      return $this->getMultiValuedField("formerTitle");
    }

    public function getUpcomingTitles()
    {
      return $this->getMultiValuedField("upcomingTitle");
    }
    
    public function getAlternativeTitles()
    {
      return $this->getMultiValuedField("title_alt");
    }
    
    public function getOriginalTitles()
    {
      return $this->getMultiValuedField("originalTitle");
    }
    
    public function getEditors()
    {
        return $this->getMultiValuedField("editor");
    }

    public function getEntities()
    {
        return $this->getMultiValuedField("entity");
    }

    public function getPublishers()
    {
        return $this->getMultiValuedField("publisher");
    }

    public function getSignatory()
    {
        return $this->getSingleValuedField('signatur');
    }
    
    public function getAnnotations()
    {
        return $this->getMultiValuedField('annotation');
    }
    
    public function getCollectionHolding()
    {
        return $this->getSingleValuedField('collectionHolding');
    }

    /**
    * Single valued
    */
    public function getDisplayPublicationDate()
    {
        return $this->getSingleValuedField('displayPublishDate');
    }

    public function getDimensions()
    {
        return $this->getMultiValuedField("dimension");
    }

    public function getRunTimes()
    {
        return $this->getMultiValuedField("runTime");
    }

    /**
    * @return array
    */
    public function getTopics()
    {
        return $this->getMultiValuedField("topic");
    }

    /**
     * @return array
    */
    public function getGeographicTopics()
    {
        return $this->getMultiValuedField("subjectGeographic");
    }

    /**
     * @return array
    */
    public function getPersonTopics()
    {
        return $this->getMultiValuedField("subjectPerson");
    }

    /**
    * @return array
    */
    public function getIssues()
    {
        return $this->getMultiValuedField("issue");
    }

      /**
    * @return array
    */
    public function getVolumes()
    {
        return $this->getMultiValuedField("volume");
    }

    /**
    * @return array
    */
    public function getZDBIDs()
    {
        return $this->getMultiValuedField("zdbId");
    }

    public function getSourceInfos()
    {
        return $this->getMultiValuedField("sourceInfo");
    }

    public function getShelfMark()
    {
        return $this->getSingleValuedField('shelfMark');
    }

    public function getTimeSpanStart()
    {
        return $this->getSingleValuedField('timeSpanStart');
    }

    public function getTimeSpanEnd()
    {
        return $this->getSingleValuedField('timeSpanEnd');
    }

    public function getDescriptions()
    {
        return $this->getMultiValuedField('description');
    }

    public function getProjects()
    {
        return $this->getMultiValuedField("project");
    }

    public function getTypeOfRessource()
    {
        return $this->getMultiValuedField("typeOfRessource");
    }

    public function getLanguageCodes()
    {
        return $this->getMultiValuedField("language_code");
    }

    public function getDocumentType()
    {
        return $this->getSingleValuedField("documentType");
    }

    public function getReviewers()
    {
        return $this->getMultiValuedField("reviewer");
    }

    public function getLeader()
    {
        return $this->getSingleValuedField('leader');
    }

    public function getControlField()
    {
        return $this->getSingleValuedField('controlfield');
    }

    public function getRecordContentSource()
    {
        return $this->getMultiValuedField("recordContentSource");
    }
    
    public function getListOfContents()
    {
        return $this->getMultiValuedField("listOfContents");
    }
    
    protected function getSingleValuedField($fieldName)
    {
        return isset($this->fields[$fieldName]) ? $this->fields[$fieldName] : null;
    }

    protected function getMultiValuedField($fieldName)
    {
        return isset($this->fields[$fieldName]) && is_array($this->fields[$fieldName]) ? $this->fields[$fieldName] : array();
    }

    /**
     * Expects one entry for systematik_parent_id and systematik_parent_title
     * @return array [id, tittle]
     */
    public function getBelongsTo()
    {
        if (isset($this->fields['systematik_parent_id']) && isset($this->fields['systematik_parent_title']))
        {
            return array($this->fields['systematik_parent_id'][0], $this->fields['systematik_parent_title'][0]);
        }
        return  array();
    }

    /**
     * Expects one entry for hierarchy_top_id and hierarchy_top_title
     * @return array [id, tittle]
     */
    public function getBelongsToTop()
    {
        if (isset($this->fields['hierarchy_parent_id']) && isset($this->fields['hierarchy_parent_title']))
        {
            return array($this->fields['hierarchy_parent_id'][0], $this->fields['hierarchy_parent_title'][0]);
        }
        return array();
    }

    /**
     * Returns one of three things: a full URL to a thumbnail preview of the record
     * if an image is available in an external system; an array of parameters to
     * send to VuFind's internal cover generator if no fixed URL exists; or false
     * if no thumbnail can be generated.
     *
     * @param string $size Size of thumbnail (small, medium or large -- small is
     * default).
     *
     * @return string|array|bool
     */
    public function getThumbnail($size = 'small')
    {
        $thumbnail = array('size' => $size, 'contenttype' => $this->getFormatForThumb());
        if ($isbn = $this->getCleanISBN()) {
            $thumbnail['isn'] = $isbn;
        }
        return $thumbnail;
    }
    
    public function getServerUrl() {
    	$url = "";
    	if (isset($this->mainConfig->Site->url)) {
    		$url = $this->mainConfig->Site->url;
    	}
    	return $url;
    }

    /**
     * @return string
     */
    private function getFormatForThumb()
    {
        $formats = $this->getFormats();
        $format = null;

        if (!empty($formats))
        {
            $format = $this->formats->get($formats[0]);
        }
        return $format;
    }

    /**
     * Return an XML representation of the record using the specified format.
     * Return false if the format is unsupported.
     *
     * @param string     $format     Name of format to use (corresponds with OAI-PMH
     * metadataPrefix parameter).
     * @param string     $baseUrl    Base URL of host containing VuFind (optional;
     * may be used to inject record URLs into XML when appropriate).
     * @param RecordLink $recordLink Record link helper (optional; may be used to
     * inject record URLs into XML when appropriate).
     *
     * @return mixed         XML, or false if format unsupported.
     */
    public function getXML($format, $baseUrl = null, $recordLink = null)
    {
        if ($format == 'oai_dc')
        {
            return $this->getDublinCoreXML($format, $baseUrl, $recordLink);
        }
        if ($format == "marc21")
        {
            return $this->getMarcXML($format, $baseUrl, $recordLink);
        }
        else
        {
            return false;
        }
    }

    private function getMarcXML($format, $baseUrl, $recordLink)
    {
        $map = array();

        $marc21="http://www.loc.gov/MARC21/slim";
        $xsi = "http://www.w3.org/2001/XMLSchema-instance";

        $record = new \SimpleXMLElement('<record/>');
        $record->addAttribute('xmlns:xsi', $xsi);
        $record->addAttribute('xmlns', $marc21);
        $record->addAttribute('xsi:schemaLocation',
            'http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd',
            $xsi);
        $record->addAttribute('type', 'Bibliographic');

        $leader=$this->getLeader();
        if (!empty($leader))
        {
            $record->addChild('leader', htmlspecialchars($leader), $marc21);
        }

        $id = $this->getUniqueID();
        if (!empty($id))
        {
            $record->addChild('controlfield', htmlspecialchars($id), $marc21)->addAttribute("tag", "001");
        }

        $controlField=$this->getControlField();
        $record->addChild('controlfield', htmlspecialchars($controlField), $marc21)->addAttribute("tag", "008");

        $isbns=$this->getISBNs();
        $this->mapChar($map, $isbns);
        $this->addDataField($map, "020", " ", " ", $record, $marc21);

        $ISSNs=$this->getISSNs();
        $this->mapChar($map, $ISSNs);
        $this->addDataField($map, "022", " ", " ", $record, $marc21);

        $recordContentSource = $this->getRecordContentSource();
        $this->mapChar($map, $recordContentSource);
        $this->addDataField($map, "040", " ", " ", $record, $marc21);

        $languageCodes=$this->getLanguageCodes();
        foreach ($languageCodes as $languageCode)
        {
            $map = array($languageCode => "a", "iso639-3" => "2");
            $this->addDataField($map, "041", " ", " ", $record, $marc21);
        }

        $primary = $this->getPrimaryAuthor();
        $this->mapChar($map, $primary);
        $this->addDataField($map, "100", "1", " ", $record, $marc21);

        $entities=$this->getEntities();
        $this->mapChar($map, $entities);
        $this->addDataField($map, "110", "2", " ", $record, $marc21);

        $shortTitle=$this->getShortTitle();
        $this->mapChar($map, $shortTitle);
        $titleSub = $this->getTitleSub();
        $this->mapChar($map, $titleSub, "b");
        $this->addDataField($map, "245", "1", "0", $record, $marc21);

        $issues=$this->getIssues();
        $this->mapChar($map, $issues);
        $volumes=$this->getVolumes();
        $this->mapChar($map, $volumes, "n");
        $this->addDataField($map, "245", " ", " ", $record, $marc21);

        $placesOfPublication=$this->getPlacesOfPublication();
        $this->mapChar($map, $placesOfPublication);
        $publishers=$this->getPublishers();
        $this->mapChar($map, $publishers, "b");
        $publicationDates=$this->getPublicationDates();
        $this->mapChar($map, $publicationDates, "c");
        //$timeSpanStart=$this->getTimeSpanStart();
        //$this->mapChar($map, $timeSpanStart, "c");
        $this->addDataField($map, "260", " ", " ", $record, $marc21);

        $physicals=$this->getPhysicalDescriptions();
        $this->mapChar($map, $physicals);
        $this->addDataField($map, "300", " ", " ", $record, $marc21);

        $runTimes=$this->getRunTimes();
        $this->mapChar($map, $runTimes);
        $this->addDataField($map, "306", " ", " ", $record, $marc21);

        $series=$this->getSeries();
        $this->mapChar($map, $series);
        $this->addDataField($map, "490", "0", " ", $record, $marc21);

        $description=$this->getDescriptions();
        $this->mapChar($map, $description);
        $this->addDataField($map, "500", " ", " ", $record, $marc21);

        $personTopics=$this->getPersonTopics();
        foreach($personTopics as $personTopic)
        {
            $this->mapChar($map, array($personTopic));
            $this->addDataField($map, "600", "1", "0", $record, $marc21);
        }

        $topics=$this->getTopics();
        foreach($topics as $topic)
        {
            $this->mapChar($map, array($topic));
            $this->addDataField($map, "650", "1", "4", $record, $marc21);
        }

        $geographicTopics=$this->getGeographicTopics();
        foreach($geographicTopics as $geographicTopic)
        {
            $this->mapChar($map, array($geographicTopic));
            $this->addDataField($map, "651", " ", "4", $record, $marc21);
        }

        $formats=$this->getFormats();
        foreach ($formats as $format)
        {
            $map = array($format => "a", "local" => "2");
            $this->addDataField($map, "655", " ", "7", $record, $marc21);
        }

        $additionalAuthors=$this->getAdditionalAuthors();
        $this->mapChar($map, $additionalAuthors);
        $this->addDataField($map, "700", "1", " ", $record, $marc21);

        $editors=$this->getEditors();
        foreach ($editors as $editor)
        {
            $map = array($editor => "a", "editor" => "e");
            $this->addDataField($map, "700", "1", " ", $record, $marc21);
        }

        $hierarchyTopIDs=$this->getHierarchyTopID();
        $this->mapChar($map, $hierarchyTopIDs, "w");
        $hierarchyTopTitles=$this->getHierarchyTopTitle();
        $this->mapChar($map, $hierarchyTopTitles, "t");
        $this->addDataField($map, "773", "0", " ", $record, $marc21);

        $institutions=$this->getInstitutions();
        $this->mapChar($map, $institutions);
        $this->addDataField($map, "852", " ", " ", $record, $marc21);

        return $record->asXML();
    }

    /**
    * Adds elements to record xml and resets map
    */
    private function addDataField(&$map, $tag, $ind1, $ind2, $record, $ns)
    {
        if (!empty($map))
        {
            $child = $record->addChild("datafield", null, $ns);
            $child->addAttribute("tag", $tag);
            $child->addAttribute("ind1", $ind1);
            $child->addAttribute("ind2", $ind2);
            foreach ($map as $subfield => $code)
            {
                $child->addChild("subfield", htmlspecialchars($subfield), $ns)->addAttribute("code", $code);
            }
        }
        // reset map
        $map = array();
    }

    private function mapChar(&$map, $elements, $char = "a")
    {
        if(is_array($elements))
        {
            foreach ($elements as $el)
            {
                $map[$el] = $char;
            }
        }
        else if (!empty($elements))
        {
            $map[$elements] = $char;
        }
    }

    private function getDublinCoreXML($format, $baseUrl, $recordLink)
    {
        // For OAI-PMH Dublin Core, produce the necessary XML:
        $dc = 'http://purl.org/dc/elements/1.1/';
        $xsi='http://www.w3.org/2001/XMLSchema-instance';
        $xml = new \SimpleXMLElement(
            '<oai_dc:dc '
            . 'xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" '
            . 'xmlns:dc="' . $dc . '" '
            . 'xmlns:xsi="'.$xsi.'" '
            . 'xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ '
            . 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd" />'
        );

        $xml->addChild('title', htmlspecialchars($this->getDisplayTitle()), $dc);
        // Authors
        $primary = $this->getPrimaryAuthor();
        if (!empty($primary))
        {
            $xml->addChild('creator', htmlspecialchars($primary), $dc);
        }
        foreach ($this->getAdditionalAuthors() as $current)
        {
            $xml->addChild('creator', htmlspecialchars($current), $dc);
        }
        // Editors
        foreach ($this->getEditors() as $current)
        {
            $xml->addChild('creator', htmlspecialchars($current), $dc);
        }

        // Entity (Körperschaft)
        foreach ($this->getEntities() as $current)
        {
            $xml->addChild('creator', htmlspecialchars($current), $dc);
        }

        // Language
        foreach ($this->getLanguages() as $lang)
        {
            $xml->addChild('language', htmlspecialchars($lang), $dc);
        }

        foreach ($this->getLanguageCodes() as $lang)
        {
            if ($lang != "none")
            {
                $child = $xml->addChild('language', htmlspecialchars($lang), $dc);
                $child->addAttribute('xsi:type', 'dcterms:ISO639-3', $xsi);
            }
        }

        // Publisher
        foreach ($this->getPublishers() as $pub)
        {
            $xml->addChild('publisher', htmlspecialchars($pub), $dc);
        }

        // Date
        $date = $this->getDisplayPublicationDate();
        if (!empty($date))
        {
            $xml->addChild('date', htmlspecialchars($date), $dc);
        }

        // format: physical
        foreach ($this->getPhysicalDescriptions() as $current)
        {
            $xml->addChild('format', htmlspecialchars($current), $dc);
        }

        // format: dimension
        foreach ($this->getDimensions() as $current)
        {
            $xml->addChild('format', htmlspecialchars($current), $dc);
        }

        // format: runTime
        foreach ($this->getRunTimes() as $current)
        {
            $xml->addChild('format', htmlspecialchars($current), $dc);
        }

        // subjects
        foreach ($this->getTopics() as $subj)
        {
            $xml->addChild('subject', htmlspecialchars($subj), $dc);
        }
        foreach ($this->getPersonTopics() as $subj)
        {
            $xml->addChild('subject', htmlspecialchars($subj), $dc);
        }
        foreach ($this->getGeographicTopics() as $subj)
        {
            $xml->addChild('coverage', htmlspecialchars($subj), $dc);
        }

        // identifier
        foreach ($this->getISBNs() as $identifier)
        {
            $xml->addChild('identifier', htmlspecialchars($identifier), $dc);
        }
        foreach ($this->getISSNs() as $identifier)
        {
            $xml->addChild('identifier', htmlspecialchars($identifier), $dc);
        }
        foreach ($this->getZDBIDs() as $identifier)
        {
            $xml->addChild('identifier', htmlspecialchars($identifier), $dc);
        }
        $xml->addChild('identifier', htmlspecialchars($this->getUniqueID()), $dc);
        if (null !== $baseUrl && null !== $recordLink)
        {
            $url = $baseUrl . $recordLink->getUrl($this);
            $xml->addChild('identifier', $url, $dc);
        }
        $shelfMark = $this->getShelfMark();
        if (!empty($shelfMark))
        {
            $xml->addChild('identifier', htmlspecialchars($shelfMark), $dc);
        }

        // description
        $projects = $this->getProjects();
        foreach ($projects as $current)
        {
            $xml->addChild('description', htmlspecialchars($current), $dc);
        }
        $desc = $this->getDescriptions();
        if (!empty($desc))
        {
            $xml->addChild('description', htmlspecialchars($desc), $dc);
        }

        // type of ressource
        $typeOfRessource = $this->getTypeOfRessource();
        foreach ($typeOfRessource as $current)
        {
            $xml->addChild('type', htmlspecialchars($current), $dc);
        }

        $formats=$this->getFormats();
        if (!empty($formats) && $formats[0] == "Artikel" && null !== $baseUrl && null !== $recordLink)
        {
            foreach ($this->getHierarchyTopID() as $current)
            {

                $url = $baseUrl . $recordLink->getUrl($current);
                $xml->addChild('source', $url, $dc);
            }
        }
        return $xml->asXml();
    }

}
