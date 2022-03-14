up:
	docker-compose up -d
stop:
	docker-compose stop
build:
	docker-compose up -d --build
init:
	bash initdev.sh