# symfony5-microservice

1) clone repo
2) run "composer install"
3) run "symfony server:start"
4) run db migration if needed
5) open http://127.0.0.1:8000

## Allowed API types:
* RestAPI
* gRPC
* http
* null

## Get some settings from database
GET http://127.0.0.1:8000/api/{type}

## Add some settings to database
POST http://127.0.0.1:8000/api/
> {
>     "type": "gRPC",
>     "field1": "field1 value",
>     "field2": "field2 value"
> }

## Update some settings in database
PUT http://127.0.0.1:8000/api/{id}
> {
>     "type": "http",
>     "field1": "field1 value",
>     "field2": "field2 value"
> }
