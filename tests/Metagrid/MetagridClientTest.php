<?php

namespace Metagrid\Tests;
require_once(__DIR__ . '/../../vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Metagrid\MetagridClient;

/**
 * Created by PhpStorm.
 * User: tobinski
 * Date: 15.03.16
 * Time: 11:43
 */
class MetagridClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * true = use mocked answers for test
     * false = send request to the metgrid server
     */
    const USE_MOCK = true;

    public function testGet() {
        $client = new MetagridClient($this->getClient($this->shortResponse()));
        $links = $client->get('dds','5');
        // count resources
        $this->assertCount(4, $links);
        // check links (they can change. Its a live check
        $this->assertSame('http://www.hls-dhs-dss.ch/textes/d/D4647.php', $links['HLS']);
        $this->assertSame('http://d-nb.info/gnd/124769942', $links['GND']);
        $this->assertSame('http://www2.unil.ch/elitessuisses/index.php?page=detailPerso&idIdentite=50020', $links['Elites suisses au XXe s.']);
        $this->assertSame('http://resources.huygens.knaw.nl/europeseintegratie/en/persoon/5148', $links['Huygens ING']);
    }

    /**
     * Return descriptions for the widgets. The language of the description can change.
     */
    public function testGetExtended() {
        $client = new MetagridClient($this->getClient($this->extendedResponse()));
        $links = $client->get('dds','5','person','de', true);
        // count resources
        $this->assertCount(4, $links);

        // check links (they can change. Its a live check
        $this->assertSame('http://www.hls-dhs-dss.ch/textes/d/D4647.php', $links['HLS']['url']);
        // check descriptions
        $this->assertArrayHasKey('short_description', $links['HLS']);
        $this->assertArrayHasKey('long_description', $links['HLS']);
        // check urls
        $this->assertSame('http://d-nb.info/gnd/124769942', $links['GND']['url']);
        $this->assertSame('http://www2.unil.ch/elitessuisses/index.php?page=detailPerso&idIdentite=50020', $links['Elites suisses au XXe s.']['url']);
        $this->assertSame('http://resources.huygens.knaw.nl/europeseintegratie/en/persoon/5148', $links['Huygens ING']['url']);
    }

    /**
     * Check the returned values are in the right language.
     * At the moment just the description change. In the next
     * release the link will be language agnostic
     */
    public function testGetLang() {
        $client = new MetagridClient($this->getClient($this->frenchResponse()));
        $links = $client->get('dds','5','person','fr');

        // count resources
        $this->assertCount(4, $links);

        // check if the slug is in the right language
        $this->assertSame('http://www.hls-dhs-dss.ch/textes/d/D4647.php', $links['DHS']);

        // check if FR link is returned. Will be implemented in the next release
        // $this->assertSame('http://www.hls-dhs-dss.ch/textes/f/D4647.php', $links['DHS']['url']);
    }

    /**
     * Get a inexistent resource
     */
    public function testFailure () {
        $client = new MetagridClient($this->getClient('', 404));
        $links = $client->get('dds','111111111111111115','person','de', true);
        $this->assertFalse($links);
    }

    /**
     * Get directly over an url
     */
    public function testGetUrl(){
        $client = new MetagridClient($this->getClient($this->shortResponse()));
        $links = $client->getUrl('https://api.metagrid.ch/widget/dds/person/5.json?jsoncallback=jQuery_2q345');
        $this->assertCount(4,$links);
    }
    /**
     * Wrong URL with exception
     */
    public function testGetWrongUrl(){
        $client = new MetagridClient();
        $this->setExpectedException('InvalidArgumentException');
        $client->getUrl('https://google.ch');
    }

    /**
     * Mock guzzle client
     * @return Client
     */
    private function getClient($content = '', $statusCode = 200) {
        if(self::USE_MOCK) {
            // Mock answers
            $stream = \GuzzleHttp\Psr7\stream_for($content);
            $response[] = new Response($statusCode, [], $stream);

            $mock = new MockHandler($response);

            $handler = HandlerStack::create($mock);
            return new Client(['handler' => $handler]);
        }
        // get from live system
        return new Client();
    }

    /**
     * Mock short answer of the server
     * @return string
     */
    private function shortResponse() {
        // Create a mock and queue two responses.
        $content = array(array(  'HLS' => 'http://www.hls-dhs-dss.ch/textes/d/D4647.php',
            'GND' => 'http://d-nb.info/gnd/124769942',
            'Elites suisses au XXe s.' => 'http://www2.unil.ch/elitessuisses/index.php?page=detailPerso&idIdentite=50020',
            'Huygens ING' => 'http://resources.huygens.knaw.nl/europeseintegratie/en/persoon/5148'));
        return json_encode($content);
    }


    /**
     * Mock wrong answer of the server
     * @return string
     */
    private function frenchResponse() {
        // Create a mock and queue two responses.
        $content = array(array(  'DHS' => 'http://www.hls-dhs-dss.ch/textes/d/D4647.php',
            'GND' => 'http://d-nb.info/gnd/124769942',
            'Elites suisses au XXe s.' => 'http://www2.unil.ch/elitessuisses/index.php?page=detailPerso&idIdentite=50020',
            'Huygens ING' => 'http://resources.huygens.knaw.nl/europeseintegratie/en/persoon/5148'));
        return json_encode($content);
    }


    /**
     * Mock short answer of the server
     * @return string
     */
    private function extendedResponse() {
        // Create a mock and queue two responses.
        $content = array(array(  'HLS' => array(
                'url' => 'http://www.hls-dhs-dss.ch/textes/d/D4647.php',
                'short_description' => 'Historisches Lexikon der Schweiz',
                'long_description' => 'HDas Historische Lexikon der Schweiz sammelt daqten ....'),
            'GND' => array(
                'url' => 'http://d-nb.info/gnd/124769942',
                'short_description' => 'Diplomatische Dokumente der Schweiz',
                'long_description' => 'Die Dodis ...'),

            'Elites suisses au XXe s.' => array(
                'url' =>  'http://www2.unil.ch/elitessuisses/index.php?page=detailPerso&idIdentite=50020',
                'short_description' => 'Elite suisse',
                'long_description' => 'Das Eltie suisse projekt...'),
            'Huygens ING'  => array(
                'url' =>   'http://resources.huygens.knaw.nl/europeseintegratie/en/persoon/5148',
                'short_description' => 'Huygens',
                'long_description' => 'Huygens ist ein projekt ...'))
        );
        return json_encode($content);
    }

}