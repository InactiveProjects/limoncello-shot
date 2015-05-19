## JSON API Lumen vs Laravel, PDO vs Eloquent

In this article a [JSON API](http://jsonapi.org) services application will be built with Lumen ([Limoncello Shot](https://github.com/neomerx/limoncello-shot)) and Laravel ([Limoncello Collins](https://github.com/neomerx/limoncello-collins)).

Both Applications provide basic CRUD operations for a few models below

![Database schema](/docs/images/models.png)

Out of curiosity Limoncello Shot has 2 CRUD implementations [Eloquent-based](http://laravel.com/docs/5.0/eloquent) and [PDO based](http://php.net/manual/en/book.pdo.php). It will be interesting to compare their performance. 

### Environment

All tests are run in VirtualBox with Ubuntu 14.04. You can find full description of it and installation steps in ['Real Hello World in Laravel 5'](https://github.com/neomerx/rhw-l5) article.

Installation steps specific to applications
- Both projects were cloned with git.
- ```$ composer install --no-dev``` and ```$ composer dump -o --no-dev``` were executed in both application's folders.
- Env file was copied for Limoncello Shot ```$ cp .env.example .env ``` and **full path** to sqlite file was set. 
- Optimizations wer applied to Limoncello Collins ```$ php artisan config:cache && php artisan route:cache && php artisan optimize --force``` (it looks Lumen do not have similar commands).
- Database was created with ```$ touch storage/database.sqlite``` and data migrated ```$ php artisan migrate --force && php artisan db:seed --force``` for both applications.
- Access rights were granted ```$ chmod a+w -R storage/ && chmod a+w vendor/``` for both applications.

For Limoncello Collins and Limoncello shot PHP and HHVM environments were configured with NGINX for different server ports.

### Compare nude Laravel to nude Lumen

Let's start from comparison between Laravel app and Lumen app that do just one thing - 'nothing'. They both return empty HTTP response. It gives us understanding how our next steps affect performance.

Here and below simplistic approach for measuring performance is used. We use Apache ab tool to know how many Requests per second (**RPS**) the server can handle. The command is

```
ab -c 5 -t 3 -H "Content-Type: application/vnd.api+json" -H "Accept: application/vnd.api+json" http://localhost:<PORT NUMBER>/sites/1
```

| Application                              | RPS 1 | RPS 2 | RPS 3 | RPS 4 | RPS 5 |
|------------------------------------------|-------|-------|-------|-------|-------|
| Limoncello Collins (PHP,  no content)    |224.66 |231.23 |231.27 |234.97 |234.78 |
| Limoncello Collins (HHVM, no content)    |510.43 |515.00 |523.63 |502.07 |502.12 |
| Limoncello Shot    (PHP,  no content)    |660.47 |678.99 |675.18 |683.17 |683.64 |
| Limoncello Shot    (HHVM, no content)    |974.56 |1033.07|941.28 |963.20 |956.33 |

### How adding support for Eloquent, Facades and .env files hits performance?

If you plan to use database you very likely to use Eloquent ORM. In order to use it in Lumen you have to enable (uncomment) a few PHP lines in ```bootstrap/app.php```

```php
Dotenv::load(__DIR__.'/../');

...

$app->withFacades();
$app->withEloquent();
```

| Application                  | RPS 1 | RPS 2 | RPS 3 | RPS 4 | RPS 5 |
|------------------------------|-------|-------|-------|-------|-------|
| Limoncello Shot (PHP,  +ORM) |530.25 |530.17 |511.56 |531.54 |509.98 |
| Limoncello Shot (HHVM, +ORM) |809.51 |785.97 |819.33 |822.62 |838.17 |

As you can see adding support for these features without actual usage of it takes about 16.5% RPS.

### JSON API performance

When JSON API services implemented they can answer ```GET sites/1``` with a response that requires a few database calls. Both applications have identical data in their databases and will return identical JSON API responses

```json
{
    "data": {
        "type": "sites",
        "id": "1",
        "attributes": {
            "name": "JSON API Samples"
        },
        "links": {
            "self": "\/sites\/1",
            "posts": {
                "linkage": {
                    "type": "posts",
                    "id": "1"
                }
            }
        }
    },
    "included": [
        {
            "type": "authors",
            "id": "1",
            "attributes": {
                "first_name": "Dan",
                "last_name": "Gebhardt",
                "twitter": "dgeb"
            },
            "links": {
                "posts": {
                    "linkage": {
                        "type": "posts",
                        "id": "1"
                    }
                }
            }
        },
        {
            "type": "comments",
            "id": "1",
            "attributes": {
                "body": "First!"
            },
            "links": {
                "post": {
                    "linkage": {
                        "type": "posts",
                        "id": "1"
                    }
                }
            }
        },
        {
            "type": "comments",
            "id": "2",
            "attributes": {
                "body": "I like XML better"
            },
            "links": {
                "post": {
                    "linkage": {
                        "type": "posts",
                        "id": "1"
                    }
                }
            }
        },
        {
            "type": "posts",
            "id": "1",
            "attributes": {
                "title": "JSON API paints my bikeshed!",
                "body": "If you've ever argued with your team about the way your JSON responses should be formatted, JSON API is your anti-bikeshedding weapon."
            },
            "links": {
                "author": {
                    "linkage": {
                        "type": "authors",
                        "id": "1"
                    }
                },
                "comments": {
                    "linkage": [
                        {
                            "type": "comments",
                            "id": "1"
                        },
                        {
                            "type": "comments",
                            "id": "2"
                        }
                    ]
                }
            }
        }
    ]
}
```

| Application                              | RPS 1 | RPS 2 | RPS 3 | RPS 4 | RPS 5 |
|------------------------------------------|-------|-------|-------|-------|-------|
| Limoncello Collins (PHP,  Eloquent)      |127.58 |127.49 |126.64 |125.32 |123.27 |
| Limoncello Collins (HHVM, Eloquent)      |267.98 |272.22 |272.41 |271.81 |270.86 |
| Limoncello Shot    (PHP,  PDO)           |261.73 |257.93 |253.89 |252.31 |251.07 |
| Limoncello Shot    (HHVM, PDO)           |459.71 |474.60 |449.99 |446.91 |463.86 |

As you can see for Zend PHP and HHVM we have about half RPS less when database calls and data conversion added.

### Eloquent vs PDO

The next step was adding the same functionality as PDO did but with Eloquent.

| Application                     | RPS 1 | RPS 2 | RPS 3 | RPS 4 | RPS 5 |
|---------------------------------|-------|-------|-------|-------|-------|
| Limoncello Shot (PHP, Eloquent) |182.91 |182.65 |182.91 |183.99 |183.19 |
| Limoncello Shot (HHVM, Eloquent)|269.04 |340.47 |338.18 |342.46 |340.33 |

### Conclusion

It is interesting to compare Eloquent on Laravel vs Eloquent on Lumen vs PDO on Lumen

| Application                              | Average RPS (3 best)      |
|------------------------------------------|---------------------------|
| Limoncello Shot    (PHP,  PDO)           | 257.85                    |
| Limoncello Shot    (PHP,  Eloquent)      | 183.36 (-28.89% from best)|
| Limoncello Collins (PHP,  Eloquent)      | 127.24 (-50.65% from best)|

Thus if you have Laravel Eloquent application on Zend PHP and need performance your first step would be moving to HHVM (or PHP 7 when available) which roughly doubles performance. If you want to double your performance one more time you should consider moving to Lumen and use PDO for critical parts of the application. 