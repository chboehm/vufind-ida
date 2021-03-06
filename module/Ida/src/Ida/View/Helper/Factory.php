<?php
/**
 * Factory for view helpers.
 *
 * @package  View_Helpers
 * @author   dkuom <dku@outermedia.de>
 */
namespace Ida\View\Helper;
use Zend\ServiceManager\ServiceManager;

/**
 * Factory for Genderbib view helpers.
 *
 * @package  View_Helpers
 * @author   dkuom <dku@outermedia.de>
 */
class Factory {

    /**
     * Construct the Piwik Analytics helper.
     *
     * @param ServiceManager $sm Service manager.
     *
     * @return PiwikAnalytics
     */
    public static function getPiwikAnalytics(ServiceManager $sm) {

        // Read config file
        $config = $sm->getServiceLocator()->get('VuFind\Config')->get('config');

        $trackerURL = $siteId = null;

        // Piwik config exists?
        if (isset($config->Piwik->trackerURL)) {
            $trackerURL = $config->Piwik->trackerURL;
        }

        if (isset($config->Piwik->siteId)) {
            $siteId = $config->Piwik->siteId;
        }

        return new PiwikAnalytics($trackerURL, $siteId);
    }

    /**
     * Construct Facet translation helper
     *
     * @param ServiceManager $sm
     * @return FacetEntryTranslation
     */
    public static function getFacetEntryTranslation(ServiceManager $sm)
    {
        // Read config file
        $config = $sm->getServiceLocator()->get('VuFind\Config')->get('facets');
        $translatedFacets = array();

        if (isset($config->TranslatedFacets->facets))
        {
            $translatedFacets = $config->TranslatedFacets->facets->toArray();
        }

        return new FacetEntryTranslation($translatedFacets);
    }

    /**
     * Construct new helper which distributes array elements to sub-arrays
     *
     * @return DistributeToArrays
     */
    public static function getDistributeToArrays() {
        return new DistributeToArrays();
    }

    /**
     * Construct new helper which escapes HTML but leaves <br>s intact
     *
     * @return EscapeHtmlAllowBr
     */
    public static function getEscapeHtmlAllowBr() {
        return new EscapeHtmlAllowBr();
    }
}