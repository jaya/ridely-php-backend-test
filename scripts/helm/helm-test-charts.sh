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
echo 'Testing database chart...'
echo '----------------------------------------'
# database helm
helm test ridely-database -n $PROJECT_NAMESPACE
