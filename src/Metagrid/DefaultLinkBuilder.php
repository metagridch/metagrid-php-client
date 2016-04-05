<?php
namespace Metagrid;

/**
 * Created by PhpStorm.
 * User: tmen
 * Date: 15.03.16
 * Time: 11:31
 */

class DefaultLinkBuilder implements LinkBuilderInterface {

    const BASEURL = "https://api.metagrid.ch/widget";
    const SEPARATOR = "/";
    public function make($slug = 'dds', $identifier = '', $resourceType = 'person', $lang = 'de', $includeDescription = false)
    {
        //f.e.https://api.metagrid.ch/widget/dds/person/5.json?lang=en&include=true&jsoncallback=jQuery19109290815709965452_1458038374939&_=1458038374940
        $result =   self::BASEURL
                    .self::SEPARATOR.$slug
                    .self::SEPARATOR.$resourceType
                    .self::SEPARATOR.$identifier
                    .".json?lang=".$lang
                    ."&jsoncallback=jQuery".rand(10000,99999)."_".rand(10000,99999);
        if($includeDescription){
            $result .= "&include=true";
        }
        return $result;
    }
}