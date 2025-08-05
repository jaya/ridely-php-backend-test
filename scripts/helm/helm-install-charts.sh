#!/bin/bash
# use the set -u command to ensure all variables are set before using
# set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

# variables validation
if [ -z "$PROJECT_NAMESPACE" ]; then
  echo '.projectrc file not found, please review the project settings, this file contains project variables for the scripts'
  exit 1
fi
if [ -z "$SHARED_DATABASE_CHART_NAME" ]; then
  echo "Error: \$SHARED_DATABASE_CHART_NAME variable not defined"
  exit 1
fi

ROOT_DIR=$(pwd)

CHART=$1
if [ ! -z "$CHART" ]; then
  echo "Target chart: $CHART"

  # Check if the chart required is "ridely-databases"
  if [ "$CHART" = "ridely-database" ]; then
    CHART_PATH="./databases/charts/$CHART"
  elif [ "$CHART" = "ridely-cache-database" ]; then
    CHART_PATH="./databases/charts/$CHART"

    cd $CHART_PATH

    if [ -z "$(ls -A ./charts)" ]; then
       echo 'Updating dependency'
       helm dependency update
    else
      echo "Dependency already installed"
    fi

    echo "returning to root directory: $ROOT_DIR "
    cd $ROOT_DIR

  else
    CHART_PATH="./backend/charts/$CHART"
  fi

  helm install "$CHART" "$CHART_PATH/" -n "$PROJECT_NAMESPACE" --values "$CHART_PATH/values/values-dev.yaml" --set rollme=$(date +%s)

  if [ $? -ne 0 ]; then
    echo 'Trying to upgrade the existing one...'
    helm upgrade "$CHART" "$CHART_PATH/" -n "$PROJECT_NAMESPACE" --values "$CHART_PATH/values/values-dev.yaml" --set rollme=$(date +%s)
  fi

else
  echo '----------------------------------------'
  echo 'Installing databases chart'
  echo '----------------------------------------'
  helm install "$SHARED_DATABASE_CHART_NAME" "./databases/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE" --values ./databases/charts/$SHARED_DATABASE_CHART_NAME/values/values-dev.yaml

  if [ $? -ne 0 ]; then
    echo 'Trying to upgrade the existing one...'
     helm upgrade "$SHARED_DATABASE_CHART_NAME" "./databases/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE" --values ./databases/charts/$SHARED_DATABASE_CHART_NAME/values/values-dev.yaml
  fi

  echo '----------------------------------------'
  echo 'Installing cache databases chart'
  echo '----------------------------------------'
  helm install "$SHARED_CACHE_DATABASE_CHART_NAME" "./databases/charts/$SHARED_CACHE_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE" --values ./databases/charts/$SHARED_CACHE_DATABASE_CHART_NAME/values/values-dev.yaml

  if [ $? -ne 0 ]; then
    echo 'Trying to upgrade the existing one...'
     helm upgrade "$SHARED_CACHE_DATABASE_CHART_NAME" "./databases/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE" --values ./databases/charts/$SHARED_CACHE_DATABASE_CHART_NAME/values/values-dev.yaml
  fi


  echo '----------------------------------------'
  echo 'Installing services chart'
  echo '----------------------------------------'
  echo ''
  echo '----------------------------------------'
  echo 'Installing Auth Service chart'
  echo '----------------------------------------'
  echo 'Moving to the directory ./backend/charts/auth-service/'

  cd ./backend/charts/auth-service/

  if [ -z "$(ls -A ./charts)" ]; then
     echo 'Updating dependency'
     helm dependency update
  else
    echo "Dependency already installed"
  fi

  echo "returning to root directory: $ROOT_DIR "
  cd $ROOT_DIR

  helm install "auth-service" "./backend/charts/auth-service/" -n "$PROJECT_NAMESPACE" --values ./backend/charts/auth-service/values/values-dev.yaml

  if [ $? -ne 0 ]; then
    echo 'Trying to upgrade the existing one...'
    helm upgrade "auth-service" "./backend/charts/auth-service/" -n "$PROJECT_NAMESPACE"  --values ./backend/charts/auth-service/values/values-dev.yaml
  fi

  echo ''
  echo '----------------------------------------'
  echo 'Installing Ridely Service chart'
  echo '----------------------------------------'

  echo "Preparing Nginx image"
  echo "Generating certificate files..."
  bash ./scripts/nginx/nginx-gen-certs.sh ./backend/services/ridely-service/docker/nginx

  echo "Building Docker images"

  echo "./scripts/docker/create-tag.sh ridely-service-nginx latest ./backend/services/ridely-service/docker/nginx/Dockerfile ./backend/services/ridely-service/docker/nginx"
  bash ./scripts/docker/create-tag.sh ridely-service-nginx latest ./backend/services/ridely-service/docker/nginx/Dockerfile ./backend/services/ridely-service/docker/nginx

  echo "./scripts/docker/create-tag.sh ridely-service-php latest ./backend/services/ridely-service/docker/php/Dockerfile ./backend/services/ridely-service/"
  bash ./scripts/docker/create-tag.sh ridely-service-php latest ./backend/services/ridely-service/docker/php/Dockerfile ./backend/services/ridely-service/

  echo "Loading Docker images"

  echo "./scripts/kind/kind-load-image.sh ridely-service-php:latest"
  bash ./scripts/kind/kind-load-image.sh ridely-service-php:latest

  echo "./scripts/kind/kind-load-image.sh ridely-service-nginx:latest"
  bash ./scripts/kind/kind-load-image.sh ridely-service-nginx:latest

  helm install "ridely-service" "./backend/charts/ridely-service/" -n "$PROJECT_NAMESPACE" --values ./backend/charts/ridely-service/values/values-dev.yaml

  if [ $? -ne 0 ]; then
    echo 'Trying to upgrade the existing one...'
     helm upgrade "ridely-service" "./backend/charts/ridely-service/" -n "$PROJECT_NAMESPACE"  --values ./backend/charts/ridely-service/values/values-dev.yaml  --set rollme=$(date +%s)
  fi
fi
