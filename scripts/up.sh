#!/bin/bash

# copy env from example if not exists
if [ ! -f ./.env ]; then
    cp ./.env.example ./.env
fi

# If there is no vendor folder then execute a build
if [ ! -d ./vendor ]; then

    # install composer dependencies
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v $(pwd):/opt \
        -w /opt \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs

    # bring containers up
    vendor/bin/sail up -d

    # generate application key
    vendor/bin/sail artisan key:generate

else
    # bring containers up
    vendor/bin/sail up -d
fi
