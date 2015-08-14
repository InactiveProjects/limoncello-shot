[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/neomerx/json-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/neomerx/json-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/neomerx/json-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/neomerx/json-api/?branch=master)
[![Build Status](https://travis-ci.org/neomerx/json-api.svg?branch=master)](https://travis-ci.org/neomerx/json-api)
[![HHVM](https://img.shields.io/hhvm/neomerx/json-api.svg)](https://travis-ci.org/neomerx/json-api)
[![License](https://poser.pugx.org/neomerx/limoncello-shot/license.svg)](https://packagist.org/packages/neomerx/limoncello-shot)

## Quick start JSON API application

Limoncello Shot is a [JSON API](http://jsonapi.org/) quick start application.
 
Technically it is a default [Lumen](https://github.com/laravel/lumen) application integrated with
- [JSON API implementation](https://github.com/neomerx/json-api)
- JWT, Bearer and Basic Authentication
- Cross-Origin Resource Sharing (CORS)

It could be a great start if you are planning to develop API with Laravel.
 
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
* Basic Authentication

This service can be called with ```curl``` and it will return response in JSON API format with correct headers

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

Limoncello Shot ships with sample CRUD API for models ```Author```, ```Comment```, ```Post```, ```Site``` and ```User```. Main code locations are

* ```app/Models```
* ```app/Schemas```
* ```app/Http/Controllers/Demo```
* ```app/Http/Controllers/JsonApi```
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

#### Run HTTP server

An easy way to start development server is

```
$ php artisan serve --port=8888
```

And that's it! The server can serve JSON API.

As it has Authentication enabled a security token (JWT) should be received first

```
curl -u user@example.com:password 'http://localhost:8888/login/basic'
```

it should return JWT (looks like long random string). API should be called with this token

```
curl -X GET -H "Authorization: Bearer <JWT here>" 'http://localhost:8888/api/v1/authors'
```

This command should return JSON-API document with a list of authors.

> Authentication middleware for API could be disabled in `app/Http/routes.php`

### Adding a new service

Let's assume that you have an Eloquent model and want to add CRUD API for it. You should

* Add application routes
* Add controller
* Add code for input validation and CRUD
* Return response
* Add model schema (details below)

#### Application routes

Routes are added the same way as in Laravel. It's recommended to read [CRUD section of the specification](http://jsonapi.org/format/#crud) to be aware which HTTP verbs should be used.

#### Controller

Your Controller should extend/inherit Controller with JSON API supported functions added. In ```app/Http/Controllers/Controller.php``` you can see it take only a few lines of code to add such support to any controller.

The Controller can now

- Parse and validate Request parameters and headers.
- Match decoders for input data based on the data type defined in ```Content-Type``` header and encoders based on the ```Accept``` header.
- Compose JSON API Responses

**To find out more, please check out the [Wiki](https://github.com/neomerx/limoncello/wiki)**

#### Model schema

Model schema tells encoder how to convert object/model to JSON API format. It defines what fields (attributes and relationships) should be converted and how. How relationships and urls should be shown and what objects should be placed to ```included``` section. Fore more information see [neomerx/json-api](https://github.com/neomerx/json-api).

Schemas are placed in ```app/Schemas``` folder. When a new schema is added a mapping between model and its schema should be added to ```config/limoncello.php``` configuration file.

### Error handling

If an exception is thrown during the process of handling HTTP request it will be converted to HTTP response with certain status code. The application already has support for a few common exceptions and you can add more. Exceptions could be converted to both HTTP code only responses and response containing JSON API Error objects. Please see ```app/Exceptions/Handler.php``` for examples of both.


## Questions?

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/neomerx/json-api)

### License

This project is a fork from upstream [laravel/laravel](https://github.com/laravel/laravel). All changes to the upstream are licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Versioning

This project is synchronized with upstream ```master``` branch and uses the same version numbers.
