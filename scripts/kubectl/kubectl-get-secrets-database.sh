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
if [ -z "$SHARED_DATABASE_CHART_NAME" ]; then
  echo "Error: \$SHARED_DATABASE_CHART_NAME variable not defined"
  exit 1
fi

echo '----------------------------------------'
echo 'Get Secrets for Database...'
echo '----------------------------------------'
echo "$SHARED_DATABASE_CHART_NAME-secret:"
kubectl get secret --namespace "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-secret" -o jsonpath="{.data.mysql-root-password}" | base64 --decode
echo ""

echo "auth-service-postgresql:"
kubectl get secret --namespace "$PROJECT_NAMESPACE" "auth-service-postgresql" -o jsonpath="{.data.password}" | base64 --decode
echo ""