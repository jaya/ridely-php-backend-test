#!/bin/bash


CURRENT_DIR=$(pwd)
APP_PATH="./"
ROOT_DIR=false

if [[ "$CURRENT_DIR" == *"/backend/services/ridely-service" ]]; then
  echo "You are inside backend/services/ridely-service."
else
  if test -d "backend/services/ridely-service"; then
    echo "You are inside in the project root directory."
    APP_PATH="./backend/services/ridely-service/"
    ROOT_DIR=true
  else
    echo "You are in the wrong directory, please go to the root folder or to backend/services/ridely-service"
    exit 1
  fi
fi

echo '----------------------------------------'
echo 'Checking path'
echo '----------------------------------------'

if [[ "$ROOT_DIR" == true ]]; then
  echo "Echo moving to $APP_PATH temporarily"
  cd $APP_PATH
fi

echo '----------------------------------------'
echo 'Environment setup'
echo '----------------------------------------'
echo 'Note: Only for local development (out of the cluster)'
#/bin/cp "$APP_PATH.env.example" "$APP_PATH.env"
#if test -f "$APP_PATH.env"; then
#  echo "Env file created with success"
#else
#  echo "Error: Env file not created"
#  exit 1
#fi

cp ".env.example" ".env"
if test -f ".env"; then
  echo "Env file created with success"
else
  echo "Error: Env file not created"
  exit 1
fi

echo '----------------------------------------'
echo 'Installing Project dependencies'
echo '----------------------------------------'
composer install

echo '----------------------------------------'
echo 'Application setup'
echo '----------------------------------------'
APP_KEY=$(php artisan key:generate --show)
echo "Generated key: $APP_KEY"

echo "Updating chart .env"
sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env

echo "Updating chart values for dev ./backend/charts/ridely-service/values/values-dev.yaml"
if test -f "../../charts/ridely-service/values/values-dev.yaml"; then
  #echo "yes"
  sed -i "s|appKey: \".*\"|appKey: \"$APP_KEY\"|" ../../charts/ridely-service/values/values-dev.yaml
else
  echo "file not found"
  exit 1
fi

if [[ "$ROOT_DIR" == true ]]; then
  echo "Echo moving to $CURRENT_DIR again"
  cd $CURRENT_DIR
fi