#!/bin/bash
rm -rf docker-compose.yml
cp docker-compose.yml.dist docker-compose.yml
rm -rf .env
cp .env.dist .env
docker-compose up -d
docker-compose exec app composer install
cat app/docker/dump/db.sql | docker exec -i db psql -U user -F t postgres