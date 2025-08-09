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
echo 'Setting context'
echo '----------------------------------------'

echo "kubectl delete pvc --all -n \"$PROJECT_NAMESPACE\""
kubectl delete pvc --all -n "$PROJECT_NAMESPACE"
kubectl delete configmap keycloak-realm -n "$PROJECT_NAMESPACE"

#kubectl delete secret -n "$PROJECT_NAMESPACE" -l release=<nome-do-release>
#kubectl get configmap  -n ridely



