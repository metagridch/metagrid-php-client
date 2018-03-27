<?php
namespace Metagrid;

/**
 * Created by PhpStorm.
 * User: tobinski
 * Date: 15.03.16
 * Time: 11:08
 */

interface LinkBuilderInterface {
    /**
     * Generate a link an return it to the MetagridClient
     * @param string $identifier
     * @param string $slug
     * @param string $resourceType
     * @param string $lang
     * @param boolean $includeDescription
     * @return string | false
     */
    public function make ($slug = '', $identifier = '', $resourceType = 'person', $lang = 'de', $includeDescription = false);
}