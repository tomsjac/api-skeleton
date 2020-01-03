# SLIM HYBRIDE

## API WITH JSONP STARTER OR APP STARTER

This project provides a basic structure for the creation of an API or APP.
The application supports JSONP [JWT](http://jwt.io/) for authentication server, CORS for cross-site requests, and an automatic routing system.
Logging is also set up already and application logs will be stored in `storage/logs/` directory.

## Requirements

* PHP 7.3+
* [Composer](https://www.getcomposer.org/)
* Docker

## Summary

- [Features](#features)
- [Installation](#installation)
- [Key directories](#key-directories)
- [Key files](#key-files)
- [Configuration](#configuration)
    - [New route Declaration](#new-route-declaration)
    - [Param URL Declaration](#param-url-declaration)
    - [Adding dependencies or middleware](#adding-dependencies-or-middleware)
    - [Use Limit Rate](#use-limit-rate)
- [Usage](#usage)
    - [Create controller](#create-controller)
    - [Data Filter](#data-filter)
    - [Cron Job](#cron-job)
    - [It is not an api / No problem](#it-is-not-an-api-no-problem)

## Features

* Framework : [Slim 3](http://www.slimframework.com/)
* Routing : Home made
* DotEnv : [motdotla/dotenv](https://github.com/motdotla/dotenv)
* Cache HTTP : [Slim-HttpCache](https://github.com/slimphp/Slim-HttpCache)
* DataBase : [Eloquent](https://github.com/illuminate/database)
* Authentication : [JWT](https://github.com/tuupola/slim-jwt-auth)
* Cross-site requests : [CORS](https://github.com/tuupola/cors-middleware)
* Log : [Monolog](https://github.com/monolog/monolog)
* HTTP Client : [Guzzle](https://github.com/guzzle/guzzle)
* HTTP Client Cache : [Guzzle cache](https://github.com/Kevinrob/guzzle-cache-middleware)
* HTTP Negotiation : [PSR-7 HTTP requests](https://github.com/gofabian/negotiation-middleware)
* Rate Limiter : Home made
* Template :  [Twig](https://github.com/slimphp/Twig-View)
* Phinx : [Phinx](https://book.cakephp.org/phinx/0/en/index.html)

## Installation

1. Fork the project
2. Launch docker : `docker-compose up`
2. Launch `dc composer install`

## Key directories

* `app`: Application code
* `app/controller`: Folder and class files to manage Json answers
* `app/formatter`: Formatter class for the datas
* `app/models`: Connection model class at the tables of the database
* `app/services/dependencies`: Dependencies class loaded by slim
* `app/services/middleware`: Middleware class loaded by slim
* `app/services/tools`: Class tools
* `app/services/traits`: Class traits
* `app/templates`: Files TWIG / HTML for the documentation
* `bin` : Script for server execution
* `config`: Configuration files
* `db`: Migration databases with Phinx
* `storage`: Temporary file (Cache, Log file ...)
* `public`: Webserver root
* `bootstrap`: Boot file
* `vendor`: Composer dependencies

## Key files

* `public/index.php`: Entry point to application
* `.env`: Configuration for the dev or prod
* `boostrap/bootSettings`: Load Configuration for the dev or prod
* `boostrap/bootServices`: Load Dependency management and middleware
* `config/routes.php`: Declaration of routes
* `app/CallableResolver.php`: Automatic router
* `app/controller/AbstractController.php`: Main controller
* `app/controller/Home.php`: Action class for the home page

## Composer 

##### docker 

```bash
dc composer install | update
```

## Configuration

Application configuration is stored in __/config/settings/__ and __.env__.



### New route Declaration

In the file : __/config/routes.php__

```php
  \App\CallableResolver::addRoute($app)->withNamespace('\\App\\controller')->load();
  // => All calls are routed in folder `app/controller`
 
  \App\CallableResolver::addRoute($app)->withGroup('auth')->withNamespace('\\App\\controller\\auth')->load();
  // => All calls `/auth/` are routed in folder `app/controller/auth`
 
  \App\CallableResolver::addRoute($app)->withVersionGroup('2.0')->withGroup('auth')->withNamespace('\\App\\controller\\auth')->load();
  // => All calls `/2.0/auth/` are routed in folder `app/controller/auth`
```

### Param URL Declaration
In the file : /config/settings/app.php, we define the separator (requestParamSeparator = '::')

* Arguments in Url

```php
//url called : auth/jwt/token/id::xxx/secret::yyyy
// In a method
Var_dump($this->getUrlParams());
/*
stdClass Object
  'id' => string 'xxx'
  'secret' => string 'yyyy'
*/
```

* Data Query / Body (GET / POST)

```php
//url called : myUrl/megaCrazy/limit::10?rabbit=1
// POST Data : ['cat' => 'in the kitchen']
// In a method
Var_dump($this->getAllParams());
/*
stdClass Object
  'limit' => int '10'
  'rabbit' => int '1'
  'cat' => string 'in the kitchen'
*/
```

* Data Query (GET)

```php
// url called : myUrl/megaCrazy/?rabbit=1
// POST Data : ['cat' => 'in the kitchen']
// In a method
Var_dump($this->getQueryParams());
/*
stdClass Object
  'rabbit' => int '1'
*/
```

* Data Body (POST)

```php
// url called : myUrl/megaCrazy/?rabbit=1
// POST Data : ['cat' => 'in::the::kitchen']
// In a method
Var_dump($this->getBodyParams());
/*
stdClass Object
  'cat' => array ('in', 'the', 'kitchen')
*/
```

### Adding dependencies or middleware

In the file : __/bootstrap/bootServices.php__

* Dependency / Service (__/config/services/dependencies.php__)

```php
$container['_namefunction_'] = function (Container $c) {
    return new \App\services\dependencies\_MyClass_($c);
};
```

* MiddleWare (__/config/services/middlewares.php__)

```php
$app->add(
    new \App\middleware\_MyMiddle_([]);
);
```

### Use Limit Rate

Set up the table in database

```bash
dc php bin/phinx migrate
```

Usage in the controller

```php
//Limit Rate (100 requests in 60 minutes)
if (($error = $this->checkRateLimit(100, 60)) !== true) {
    return $error;
}
```

Set WhiteList (__/config/services/api.php__)
The ips are not controlled

```php
$settings['rateLimit']['whiteList']    = [
    'ip.white.list.01'
];
```

## Usage

### Create controller

1. Create a folder or not, depending on the data processed

2. Create a class that will be extended with `AbstractController.php`

```php
<?php
namespace App\controller;

class Home extends AbstractController
{
    public function index()
    {
    }
}
```

3. Protection calls using the method and scope

```php
  public function index()
  {
    //Check Access : Scope [Public] and Method [GET]
    if (($error = $this->checkAccess(['public'], ['GET'])) !== true) {
        return $error;
    }
    
    //OR

    //Check Access : Scope [ALL] and Method [GET & POST]
    if (($error = $this->checkAccess(null, ['GET','POST'])) !== true) {
        return $error;
    }

     //OR
    
    //Check Access : Scope [Public & admin] and Method [ALL]
    if (($error = $this->checkAccess(['public','admin'], null)) !== true) {
        return $error;
    }
  }
```

4.  Protection brut force calls using Limit rate

```php
//Limit Rate (100 requests in 60 minutes)
if (($error = $this->checkRateLimit(100, 60)) !== true) {
    return $error;
}
```

5. Response JSON preformatted, The display structure can be changed in the library: `app\services\dependencies\response\ApiResponse.php`

```php
  public function index()
  {
    //1 : Simple Response with text
    $msg = "Hello World : Welcome to the API";
    return $this->getApiResponse()->write($msg);

    //2 : Simple Response with Array
    $data[] = "Hello World";
    $data[] = "GoodBye World";
    return $this->getApiResponse()->write($data);

    //3 : Change the status response
    return $this->getApiResponse()->setStatus(200)->write($data);
    
    //4 : Error Response
    return $this->getApiResponse()->setStatus(401)->setError('The method call is not allowed', 'not_authorized')->write();

    //5 : Write Raw
    return $this->getApiResponse()->writeRaw($data);
  }
```

*Reponse 1*
```json
{
    "meta": {
        "code": 200
    },
    "response": "Hello World : Welcome to the API"
}
```

*Reponse 2*
```json
{
    "meta": {
        "code": 200
    },
    "response": [
        "Hello World",
        "GoodBye World"
    ]
}
```

*Reponse 4*
```json
{
    "meta": {
        "code": 401,
        "errorType": "not_authorized",
        "errorDetail": "The method call is not allowed"
    },
    "response": ""
}
```

*Reponse 5*
```php
[
  "Hello World",
  "GoodBye World"
]
```

### Data Filter

It is possible to filter the data passed in GET / POST before their use

See : http://filter.particle-php.com/en/stable/

Use 

```php
$dataFilter = $this->getContainer('dataFilter');

//Filter
$dataFilter->value('idUser')->int();
$dataFilter->value('firstName')->string()->trim()->upperFirst();
$dataFilter->value('lastName')->string()->defaults('Georges');

$datas = $dataFilter->filterQueryParams();
```


1. Params GET

```php
$dataFilter = $this->getContainer('dataFilter');

$datas = $dataFilter->filterQueryParams();
```

2. Params POST

```php
$dataFilter = $this->getContainer('dataFilter');

$datas = $dataFilter->filterBodyParams();
```

3. Params All (GET && POST)

```php
$dataFilter = $this->getContainer('dataFilter');

$datas = $dataFilter->filterAllParams();
```

4. Params in Url
```php
$dataFilter = $this->getContainer('dataFilter');

$datas = $dataFilter->filterUrlParams();
```


### Cron Job

The call of the cron file is in the folder : "bin/cron"

In this case, a Cron controller is used with more actions depending on the desired period : "\App\controller\cron\CronJob"

Example of call of the crons

```txt
* * * * * www-data cd /bin/;php -q cron cronMinute

5 * * * * www-data cd /bin/;php -q cron cronHour

10 3 * * * www-data cd /bin/;php -q cron cronDay

15 4 * * 0 www-data cd /bin/;php -q cron cronWeek

20 5 1 * * www-data cd /bin/;php -q cron cronMonth

25 6 1 1 * www-data cd /bin/;php -q cron cronYear
```


### It is not an api / No problem

 In the file : __/boostrap/bootSettings.php__  remove the lines for the api
 
```php
//require $pathRootSettings . '/config/settings/api.php',
require $pathRootSettings . '/config/settings/app.php',
//require $pathRootSettings . '/config/settings/cache.php',
require $pathRootSettings . '/config/settings/databases.php',
require $pathRootSettings . '/config/settings/views.php'
```
In the file : __/config/services/middlewares.php__  remove the lines for the api

```php
 // comment All Code
```

## Enjoy
