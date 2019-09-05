```bash
docker-compose build
docker-compose up
docker-compose exec php composer install
docker-compose exec php ./bin/console doctrine:migrations:migrate
docker-compose exec php ./bin/console doctrine:fixtures:load
```

## open localhost/posts/