This is a small client to use the metagrid API in php.

PHP-Spider Features
======
- Easy to use interface to interact with the metagrid API
- Possibility to change the process of linkbuilding

Installation
------------
The easiest way to install Metagrid-Client is with [git](http://git.org/).
```bash
git clone git@source.dodis.ch:metagrid/client-php.git
```

Use [composer](http://getcomposer.org) to install dependencies
```bash
php composer.pear install
```

Usage
-----
This is a very simple example.

First create the client. Get all links for provider Dodis with identifier 5
```php
use \Metagrid\MetagridClient;

$client = new MetagridClient();
$links = $client->get('dds','5')
```
Get all links for a specific resourceType
```php
$links = $client->get('dds','5','person');
```
Get all links in a specific language
```php
$links = $client->get('dds','5','person','de');
```
Get all links with more detail information
```php
$links = $client->get('dds','5','person','de',true);
```
The client use Guzzle as a default client library. If you wanna use another http Client just inject it in the constructor. He needs to implements clientInterface
```php
use \Metagrid\MetagridClient;
use \GuzzleHttp\Client;
$guzzleClient = new Client()
$client = new MetagridClient($guzzleClient);
$links = $client->get('dds','5')
```
If you like to change the way request to the AI are done you can implement your own LinkBuilder. That may be interesting, if you need to double check properties or like to exclude some IDs

```php
class HLSLinkBuilder implements LinkBuilderInterface {

    const BASEURL = "https://api.metagrid.ch/widget";
    const SEPARATOR = "/";
    // change the defaults
    public function make($slug = 'hls', $identifier = '', $resourceType = 'person', $lang = 'de', $includeDescription = false)
    {

        //f.e.https://api.metagrid.ch/widget/dds/person/5.json?lang=en&include=true&jsoncallback=jQuery19109290815709965452_1458038374939&_=1458038374940
        if($this->isExclude($identifier)) return false;
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
}```

