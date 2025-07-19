#!/bin/bash
# use the set -u command to ensure all variables are set before using
set -u

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
echo 'Validating namespace...'
echo '----------------------------------------'

echo "kubectl get namespace ${PROJECT_NAMESPACE}"
if kubectl get namespace "$PROJECT_NAMESPACE" &> /dev/null; then
  echo "Namespace $PROJECT_NAMESPACE already exists."
else
  echo "Namespace $PROJECT_NAMESPACE doesn't exists. Creating..."
  kubectl create namespace "$PROJECT_NAMESPACE"
fi