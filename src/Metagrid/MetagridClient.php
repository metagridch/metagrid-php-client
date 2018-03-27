<?php
namespace Metagrid;
require_once(__DIR__ . '/../../vendor/autoload.php');

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\ClientInterface;
use \GuzzleHttp\Exception\GuzzleException;

/**
 * Created by PhpStorm.
 * User: tobinski
 * Date: 15.03.16
 * Time: 10:47
 */
class MetagridClient
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client = null;

    /**
     * @var LinkBuilderInterface | null
     */
    private $linkBuilder = null;

    /**
     * MetagridClient constructor.
     * @param LinkBuilderInterface $linkBuilder
     * @param ClientInterface $client
     */
    function __construct(ClientInterface $client = null, LinkBuilderInterface $linkBuilder = null)
    {
        $this->client = $client;
        $this->linkBuilder = $linkBuilder;

    }

    /**
     * Get all links for an identifier and a language
     * @param string $slug Slug of the provider. Usually this is the domain
     * @param string $identifier Identifier of the resource
     * @param string $resourceType ResourceType of the resource. At the moment just person|place|organization
     * @param string $lang Language of the resource
     * @param boolean $includeDescription Include a longer description of the providers in the response
     * @return array|boolean
     */
    public function get($slug = 'dds', $identifier = '', $resourceType = 'person', $lang = 'de', $includeDescription = false){
        $url = $this->getLinkBuilder()->make($slug, $identifier, $resourceType, $lang, $includeDescription);
        if($url === false) return array();
        return $this->getUrl($url);
    }

    /**
     * Get an url directly
     * @param string $url
     * @throws \InvalidArgumentException
     * @return array|bool
     */
    public function getUrl($url = '') {
        if(!preg_match('/api.metagrid.ch/',$url)){
            throw new \InvalidArgumentException("You can just request the metagrid server");
        }
        try {
            $response = $this->getClient()->request('GET', $url, array());
        }
        catch(ClientException $e){
            // todo: log error
            return false;
        }
        catch(GuzzleException $e) {
            // todo: log error
            return false;
        }

        // decode jsonp. In future you can get pure json from the server
        return $this->jsonp_decode($response->getBody()->__toString());

    }

    /**
     * Return a client, Lazy load guzzle Client if no client is configured
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient () {
        if($this->client == null){
            $this->client = new Client();
        }
        return $this->client;
    }

    /**
     * Set a Client for HTTP request
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function setClient(ClientInterface $client){
        $this->client = $client;
    }


    /**
     * Delegate link generation to an external function
     * @return LinkBuilderInterface
     */
    private function getLinkBuilder(){
        if($this->linkBuilder == null){
            $this->linkBuilder = new DefaultLinkBuilder();
        }
        return $this->linkBuilder;
    }

    /**
     * Decode JSONP to an array.
     * @param  string    $jsonp A jsonp string
     * @return array         an array of the jsonp input
     */
    function jsonp_decode($jsonp = '') {
        if($jsonp[0] !== '[' && $jsonp[0] !== '{')
        {
            $jsonp = substr($jsonp, strpos($jsonp, '('));
        }
        return json_decode(trim($jsonp,'();'), true)[0];
    }
}