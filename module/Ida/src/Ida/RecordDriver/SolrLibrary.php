<?php
/**
 * Record Driver for Library Items.
 * User: boehm
 * Date: 6/4/14
 * Time: 3:21 PM
 */
namespace Ida\RecordDriver;

use VuFind\RecordDriver\SolrDefault;
use VuFind\XSLT\Import\VuFind;

class SolrLibrary extends SolrDefault
{

    private $formats = array();

    function __construct($mainConfig = null, $recordConfig = null,
                         $searchSettings = null)
    {
        $this->formats = $mainConfig->Format2Thumbs->formats;
        parent::__construct($mainConfig, $recordConfig, $searchSettings);
    }

    public function getEditors()
    {
        return isset($this->fields['editor']) && is_array($this->fields['editor']) ?
            $this->fields['editor'] : array();
    }

    public function getPublishers()
    {
        return isset($this->fields['publisher']) && is_array($this->fields['publisher']) ?
            $this->fields['publisher'] : array();
    }

    public function getDisplayPublicationDate()
    {
        return $this->fields['displayPublisheddate'];
    }

    public function getDisplayTitle()
    {
        return isset($this->fields['title_sub']) ?
            $this->getShortTitle() . " : " . $this->getTitleSub() : $this->getTitle();
    }

    public function  getTitleSub()
    {
        return $this->fields['title_sub'];
    }

    public function getSeriesNr()
    {
        return $this->fields['seriesNr'];
    }

    public function getTopics()
    {
        return isset($this->fields['topic']) ? $this->fields['topic'] : array();
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
            //return array('isn' => $isbn, 'size' => $size, 'contenttype' => $this->getFormatForThumb());
        }
        return $thumbnail;
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
}