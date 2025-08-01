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
echo 'Validating databases chart...'
echo '----------------------------------------'
# databases helm
helm template "$SHARED_DATABASE_CHART_NAME" "./databases/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE"  --values ./databases/charts/$SHARED_DATABASE_CHART_NAME/values.yaml

echo '----------------------------------------'
echo 'Validating services chart'
echo '----------------------------------------'
helm template "auth-service" "./backend/charts/auth-service/" -n "$PROJECT_NAMESPACE" --values ./backend/charts/auth-service/values.yaml
helm template "ridely-service" "./backend/charts/ridely-service/" -n "$PROJECT_NAMESPACE" --values ./backend/charts/ridely-service/values.yaml --debug