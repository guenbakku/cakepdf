## Development

```bash
# 1. Build docker image for developing (first time only)
$ docker-compose build

# 2. Composer installing (first time only)
$ docker-compose run --rm php composer install

# 3. Execute phpunit
$ docker-compose run --rm php vendor/bin/phpunit
```
