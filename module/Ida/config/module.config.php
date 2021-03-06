<?php
namespace Ida\Module\Configuration;

$config = array(
    'vufind' => array(
        'plugin_managers' => array(
            'recorddriver' => array(
                'factories' => array(
                    'solrlibrary' => array('Ida\Factory', 'getSolrLibrary'),
                    'solrarchive' => array('Ida\Factory', 'getSolrArchive'),
                    'solrsystematics' => array('Ida\Factory', 'getSolrSystematics'),
                ),
            ),
            'hierarchy_treedatasource' => array(
                'factories' => array(
                    'solr' => array('Ida\Hierarchy\TreeDataSource\Factory', 'getSolr'),
                ),
                'invokables' => array(
                    'xmlfile' => 'VuFind\Hierarchy\TreeDataSource\XMLFile',
                ),
            ),
            'hierarchy_treerenderer' => array(
                'invokables' => array(
                    'jstree' => 'Ida\Hierarchy\TreeRenderer\JSTree',
                )
            ),
            'search_backend' => array(
                'factories' => array(
                    'Solr' => 'Ida\Search\Factory\SolrDefaultBackendFactory',
                )
            ),
        	'recordtab' => array(
        		'factories' => array(
        			'OtherInstitutions' => 'Ida\RecordTab\TabFactory::getOtherInstitutions'
        		)
        	),
        ),
        'recorddriver_tabs' => array(
            // Disable Holdings, Comments and  Staff View
            'Ida\RecordDriver\SolrIDA' => array(
                'tabs' => array (
                    //'Holdings' => 'HoldingsILS',
                    //'TOC' => 'TOC',
                    //'UserComments' => 'UserComments',
                    //'Reviews' => 'Reviews',
                    //'Excerpt' => 'Excerpt',
                    'Description' => 'Description',
                    //'Details' => 'StaffViewArray',
                    'HierarchyTree' => 'HierarchyTree',
                    //'Map' => 'Map', // <-- This is NOT the institution
                    // The INSTITUTION is not tab, but part of the record/view.phtml template.
                    'OtherInstitutions' => 'OtherInstitutions'
                ),
                'defaultTab' => null,
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'topics' => array('Ida\Factory', 'getTopicsController'),
            'hierarchyhelper' => array('Ida\Factory', 'getHierarchyCacheHelperController'),
        ),
        'invokables' => array(
            'idaoai' => 'Ida\Controller\IdaOaiController',
            'search' => 'Ida\Controller\SearchController',
            'about'  => 'Ida\Controller\AboutController'
        ),
    ),
    'router' => array(
        'routes' => array(
            'topics' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/Topics',
                    'defaults' => array(
                        'controller' => 'Topics',
                        'action'     => 'Home',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'list' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/List',
                            'defaults' => array(
                                'controller' => 'Topics',
                                'action'     => 'List',
                            )
                        )
                    ),
                    'topics' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route'    => '/Topics',
                            'defaults' => array(
                                'controller' => 'Topics',
                                'action'     => 'Topics',
                            )
                        )
                    )
                ),
            ),
            'about' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/About',
                    'defaults' => array(
                        'controller' => 'About',
                        'action'     => 'Home',
                    ),
                ),
            ),
            'idaoai' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/OAI/Server',
                    'defaults' => array(
                        'controller' => 'idaoai',
                        'action'     => 'Server',
                    ),
                ),
            ),
            'hierarchyhelper' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/HierarchyHelper',
                    'defaults' => array(
                        'controller' => 'hierarchyhelper',
                        'action'     => 'Delete',
                    ),
                ),
            ),
            'search-contributors' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/Search/Contributors',
                    'defaults' => array(
                        'controller' => 'Search',
                        'action'     => 'Contributors'
                    )
                )
            ),
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'piwikanalytics' => array('Ida\View\Helper\Factory', 'getPiwikAnalytics'),
            'facetEntryTranslation' => array('Ida\View\Helper\Factory', 'getFacetEntryTranslation'),
            'distributeToArrays' => array('Ida\View\Helper\Factory', 'getDistributeToArrays'),
            'escapeHtmlAllowBr' => array('Ida\View\Helper\Factory', 'getEscapeHtmlAllowBr')
        ),
    ),
);

return $config;