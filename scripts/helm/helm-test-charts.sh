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
echo 'Testing database chart...'
echo '----------------------------------------'
# database helm
echo "helm test "$SHARED_DATABASE_CHART_NAME" -n "$PROJECT_NAMESPACE""
helm test "$SHARED_DATABASE_CHART_NAME" -n "$PROJECT_NAMESPACE"
