This is a small client to use the metagrid API with php.

Metagrid Client Features
======
- Easy to use interface to interact with the metagrid API
- Possibility to change the process of requesting data from the API

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
$links = $client->get('dodis','5')
```
Get all links for a specific resourceType
```php
$links = $client->get('dodis','5','person');
```
Get all links in a specific language
```php
$links = $client->get('dodis','5','person','de');
```
Get all links with more detail information
```php
$links = $client->get('dodis','5','person','de',true);
```
The client use Guzzle as a default http-client library. If you wanna use another http-client just inject it in the constructor. The http-client needs to implements ClientInterface
```php
use \Metagrid\MetagridClient;
use \GuzzleHttp\Client;
$guzzleClient = new Client()
$client = new MetagridClient($guzzleClient);
$links = $client->get('dodis','5')
```
If you like to change the way request to the API are done you can implement your own LinkBuilder. That may be interesting, if you need to double check properties or need to modifies the identifier

```php
class ExampleLinkBuilder implements LinkBuilderInterface {

    const BASEURL = "https://api.metagrid.ch/widget";
    const SEPARATOR = "/";
    // change the defaults slug
    public function make($slug = 'example', $identifier = '', $resourceType = 'person', $lang = 'de', $includeDescription = false)
    {

        //f.e.https://api.metagrid.ch/widget/example/person/5modification.json?lang=de&include=false&jsoncallback=jQuery19109290815709965452_1458038374939&_=1458038374940
        if($this->isExclude($identifier)) return false;
        $result =   self::BASEURL
                    .self::SEPARATOR.$slug
                    .self::SEPARATOR.$resourceType
                    .self::SEPARATOR.$identifier."modification"
                    .".json?lang=".$lang
                    ."&jsoncallback=jQuery".rand(10000,99999)."_".rand(10000,99999);
        if($includeDescription){
            $result .= "&include=true";
        }
        return $result;
    }
    
    /**
    * Exclude resource with identifier 1,2,3
    **/
    private function isExclude($identifier){
        return inArray($identifier, array(1,2,3));
    }
}```

