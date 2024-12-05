#!/bin/bash
set -e
echo "Deployment started..."
git pull
echo "New changes copied to server !"
echo "Installing Dependencies..."
composer8.2 install
echo "Some Artisan Commands..."
php8.2 artisan migrate
php8.2 artisan storage:link
echo "Deployment Finished!"
