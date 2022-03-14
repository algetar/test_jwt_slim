#!/bin/bash
rm -rf docker-compose.yml
cp docker-compose.dev.dist docker-compose.yml
docker-compose up -d
docker-compose exec app composer install
cat app/docker/dump/db.sql | docker exec -i db psql -U user
