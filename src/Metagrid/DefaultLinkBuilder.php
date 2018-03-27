<?php
namespace Metagrid;

/**
 * Created by PhpStorm.
 * User: tobinski
 * Date: 15.03.16
 * Time: 11:31
 */

class DefaultLinkBuilder implements LinkBuilderInterface {

    /**
     * The url of the widget
     */
    const BASEURL = "https://api.metagrid.ch/widget";

    /**
     * The path separator
     */
    const SEPARATOR = "/";

    /**
     * @inheritdoc
     * @param string $slug
     * @param string $identifier
     * @param string $resourceType
     * @param string $lang
     * @param bool $includeDescription
     * @return false|string
     */
    public function make($slug = '', $identifier = '', $resourceType = 'person', $lang = 'de', $includeDescription = false)
    {
        //f.e.https://api.metagrid.ch/widget/dds/person/5.json?lang=en&include=true&jsoncallback=jQuery19109290815709965452_1458038374939&_=1458038374940
        $result =   self::BASEURL
                    .self::SEPARATOR.$slug
                    .self::SEPARATOR.$resourceType
                    .self::SEPARATOR.$identifier
                    .".json?lang=".$lang
                    ."&jsoncallback=jQuery".rand(10000,99999)."_".rand(10000,99999);

        // check if we need to include groups
        if($includeDescription){
            $result .= "&include=true";
        }
        return $result;
    }
}