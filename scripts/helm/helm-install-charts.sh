#!/bin/bash
# Include the project variables
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
echo 'Installing database chart'
echo '----------------------------------------'
helm install ridely-database ./database/charts/ridely-database/ -n $PROJECT_NAMESPACE

if [ $? -ne 0 ]; then
  echo 'Trying to upgrade the existing one...'
   helm upgrade ridely-database ./database/charts/ridely-database/  -n $PROJECT_NAMESPACE
fi
#if [ $? -ne 0 ]; then