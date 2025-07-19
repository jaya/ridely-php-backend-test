#!/bin/bash
# use the set -u command to ensure all variables are set before using
set -u

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


echo '----------------------------------------'
echo 'Adding repositories...'
echo '----------------------------------------'
echo 'helm repo add bitnami https://charts.bitnami.com/bitnami'
helm repo add bitnami https://charts.bitnami.com/bitnami
helm repo update

#echo '----------------------------------------'
#echo 'Compiling chart locally...'
#echo '----------------------------------------'
#helm dependency update
