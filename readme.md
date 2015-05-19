[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/neomerx/json-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/neomerx/json-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/neomerx/json-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/neomerx/json-api/?branch=master)
[![Build Status](https://travis-ci.org/neomerx/json-api.svg?branch=master)](https://travis-ci.org/neomerx/json-api)
[![HHVM](https://img.shields.io/hhvm/neomerx/json-api.svg)](https://travis-ci.org/neomerx/json-api)
[![License](https://poser.pugx.org/neomerx/limoncello-shot/license.svg)](https://packagist.org/packages/neomerx/limoncello-shot)

## Quick start JSON API application

Limoncello Shot is a [JSON API](http://jsonapi.org/) quick start application.
 
Technically it is a default [Lumen](https://github.com/laravel/lumen) application integrated with [JSON API implementation](https://github.com/neomerx/json-api).

It could be a great start if you are planning to develop API with Lumen.

As Lumen is a bit geeky this application goes geeky as well. It implements API twice. One time with [PDO](http://php.net/manual/en/book.pdo.php) and another with [Eloquent](http://laravel.com/docs/5.0/eloquent). You can find detailed performance comparison of both approaches [here](/docs/performance.md).

If you are more interested in plain vanilla Laravel and Eloquent quick start app please consider [Limoncello Collins](https://github.com/neomerx/limoncello-collins).
 
### In a nutshell

It incredibly reduces complexity of protocol implementation so you can focus on the core code. For example a minimal controller to handle ```GET resource``` requests might look as simple as this

```php
class AuthorsController extends Controller
{
	public function show($id)
	{
        return $this->getResponse(Author::findOrFail($id));
    }
}
```

It has support for

* Request validation
* Encoding result to JSON API format
* Filling in all required Response headers
* Error handling

This service can be called with ```curl``` command

```
$ curl -X GET -H "Content-Type: application/vnd.api+json" -H "Accept: application/vnd.api+json" http://localhost:8888/authors/1
```

and it will return response in JSON API format with correct headers

```
HTTP/1.1 200 OK
Content-Type: application/vnd.api+json
Host: localhost:8888
```

and body formatted

```json
{
    "data": {
        "attributes": {
            "first_name": "Dan", 
            "last_name": "Gebhardt"
        }, 
        "id": "1", 
        "links": {
            "posts": {
                "linkage": {
                    "id": "1", 
                    "type": "posts"
                }
            }, 
            "self": "http://localhost:8888/authors/1"
        }, 
        "type": "authors"
    }
}
```

Limoncello Shot ships with sample CRUD API for models ```Author```, ```Comment```, ```Post```, ```Site```. Main code locations are

* ```app/Models```
* ```app/Schemas```
* ```app/Http/Controllers/Demo```
* ```app/Http/Routes```
* ```config/limoncello.php```

### Installation

#### Download latest version

```
$ composer create-project neomerx/limoncello-shot --prefer-dist
$ cd limoncello-shot/
```

#### Migrate and seed database

For simplicity it uses sqlite database by default. You are free to change database settings before the next step.

In case of sqlite usage you need to create an empty database file

```
$ touch storage/database.sqlite
```

Migrate and seed data

```
$ php artisan migrate --force && php artisan db:seed --force
```

You should copy ```.env.example``` file to ```.env``` and set **full path** to ```database.sqlite``` in it

```$ cp .env.example .env ```

#### Run HTTP server

An easy way to start development server is

```
$ php artisan serve --port=8888
```

And that's it! The server can serve JSON API.

### Adding a new service

Let's assume that you have an Eloquent model and want to add CRUD API for it. You should

* Add application routes
* Add controller
* Add code for input validation and CRUD
* Return response
* Add model schema (details below)

#### Application routes

Routes are added the same way as in Lumen. It's recommended to read [CRUD section of the specification](http://jsonapi.org/format/#crud) to be aware which HTTP verbs should be used.

#### Controller

Your Controller should extend/inherit Controller with JSON API supported functions added. In ```app/Http/Controllers/Controller.php``` you can see it take only a few lines of code to add such support to any controller.

#### Input decoding and validation

The specification defines input document format and parameters that could be sent to API.

You can read input document with standard methods or use helper method ```$this->getDocument()```. Various decoders might be configured for input data types. Default decoder simply returns input as an array.
 
Method ```$this->getParameters()``` is used to parse input parameters (e.g. sparse fields, include, sort, paging and filters). It does not only parsing but validation as well. Valid parameter values might be configured and will be checked automatically. You configure them by adding the following properties to Controller class

| Property with default value                     | Description                                                                                                                                                                                             |
|-------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
|```protected $allowedIncludePaths = [];```       | A list of allowed include paths in input parameters. Empty array ```[]``` means clients are not allowed to specify include paths and ```null``` means all paths are allowed.                           |
|```protected $allowedFieldSetTypes = null;```    | A list of JSON API types which clients can sent field sets to. Empty array ```[]``` means clients are not allowed to specify field sets for all types and ```null``` means any field sets are allowed. |
|```protected $allowedSortFields = [];```         | A list of allowed sort field names in input parameters. Empty array ```[]``` means clients are not allowed to specify sort fields and ```null``` means all fields are allowed.                         |
|```protected $allowedFilteringParameters = [];```| A list of allowed filtering input parameters. Empty array ```[]``` means clients are not allowed to specify filtering and ```null``` means all parameters are allowed.                                 |
|```protected $allowUnrecognizedParams = false;```| If unrecognized parameters should be allowed in input parameters.                                                                                                                                      |

#### Responses

The specification requires responses to have certain headers and data to be formatted. The following helper methods could be used for it

|Method                                                                                                                               |Description                                                                                                               |
|-------------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------|
|```$this->getCodeResponse(int $statusCode)```                                                                                        | Get Response with HTTP code and empty body.                                                                              |
|```$this->getResponse(mixed $data, int $statusCode = Response::HTTP_OK, DocumentLinksInterface $links = null, mixed $meta = null)``` | Get Response with data (model or collection) encoded in JSON API format.                                                 |
|```$this->getCreatedResponse(object $resource, DocumentLinksInterface $links = null, mixed $meta = null)```                          | Get Response for HTTP code 201 (Created). The specification requires special header to be added and this method does it. |


#### Model schema

Model schema tells encoder how to convert object/model to JSON API format. It defines what fields (attributes and links) should be converted and how. How links and urls should be shown and what object should be placed to ```included``` section. Fore more information see [neomerx/json-api](https://github.com/neomerx/json-api).

Schemas are placed in ```app/Schemas``` folder. When a new schema is added a mapping between model and its schema should be added to ```config/limoncello.php``` configuration file. An example of Schema might look like

```php

class SiteSchema extends SchemaProvider
{
    protected $resourceType = 'sites';
    protected $baseSelfUrl  = '/sites';

    public function getId($site)
    {
        return $site->id;
    }

    public function getAttributes($site)
    {
        return [
            'name' => $site->name,
        ];
    }

    public function getLinks($site)
    {
        return [
            'posts' => [self::DATA => $site->posts->all()],
        ];
    }

    public function getIncludePaths()
    {
        return [
            'posts',
            'posts.author',
            'posts.comments',
        ];
    }
}
```

### Error handling

If an exception is thrown during the process of handling HTTP request it will be converted to HTTP response with certain status code. The application already has support for a few common exceptions and you can add more. Exceptions could be converted to both HTTP code only responses and response containing JSON API Error objects. Please see ```app/Exceptions/Handler.php``` for examples of both.


## Questions?

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/neomerx/json-api)

### License

This project is a fork from upstream [laravel/lumen](https://github.com/laravel/lumen). All changes to the upstream are licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Versioning

This project is synchronized with upstream ```master``` branch and uses the same version numbers.
