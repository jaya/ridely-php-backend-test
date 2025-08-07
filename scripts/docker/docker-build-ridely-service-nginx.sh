#!/bin/bash

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

echo "Building Docker image"

echo "./scripts/docker/create-tag.sh ridely-service-nginx latest ./backend/services/ridely-service/docker/nginx/Dockerfile ./backend/services/ridely-service/docker/nginx"
bash ./scripts/docker/create-tag.sh ridely-service-nginx latest ./backend/services/ridely-service/docker/nginx/Dockerfile ./backend/services/ridely-service/docker/nginx