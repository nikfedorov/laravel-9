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

    # take a nap
    echo "Waiting for the DB to come up. Sleeping 10 seconds..."
    sleep 10

    # migrate the database
    vendor/bin/sail artisan migrate

else
    # bring containers up
    vendor/bin/sail up -d
fi

# start laravel horizon
vendor/bin/sail artisan horizon
