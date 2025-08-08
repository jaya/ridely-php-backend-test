#!/bin/bash
# use the set -u command to ensure all variables are set before using
# set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

if [ -z "$PROJECT_NAMESPACE" ]; then
  echo '.projectrc file not found, please review the project settings, this file contains project variables for the scripts'
  exit 1
fi

echo '----------------------------------------'
echo 'Scaling service deployment'
echo '----------------------------------------'

SERVICE=$1
REPLICAS=$2

if [ ! -z "$SERVICE" ]; then
  SERVICE="ridely-service-php-fpm"
fi
if [ ! -z "$REPLICAS" ]; then
  REPLICAS=1
fi

echo "kubectl scale deployment \"$SERVICE\" --replicas="$REPLICAS" -n \"$PROJECT_NAMESPACE\""
kubectl scale deployment "$SERVICE" --replicas="$REPLICAS" -n $PROJECT_NAMESPACE