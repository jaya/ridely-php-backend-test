#!/bin/bash

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

echo "Building Docker image"

echo "./scripts/docker/create-tag.sh ridely-service-php latest ./backend/services/ridely-service/docker/php/Dockerfile ./backend/services/ridely-service/"
bash ./scripts/docker/create-tag.sh ridely-service-php latest ./backend/services/ridely-service/docker/php/Dockerfile ./backend/services/ridely-service/