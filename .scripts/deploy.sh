#!/bin/bash
set -e
echo "Deployment started..."

git pull

echo "Some Artisan Commands..."

php8.2 artisan migrate

php8.2 artisan storage:link

echo "New changes copied to server !"

echo "Installing Dependencies..."

php8.2 /usr/bin/composer install

echo "Deployment Finished!"
