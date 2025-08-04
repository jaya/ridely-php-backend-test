#!/bin/bash
# use the set -u command to ensure all variables are set before using
#set -u

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

  helm uninstall "$CHART" -n "$PROJECT_NAMESPACE"

else
  echo '----------------------------------------'
  echo 'Uninstalling databases chart'
  echo '----------------------------------------'
  helm uninstall "$SHARED_DATABASE_CHART_NAME" "./databases/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE"

  echo '----------------------------------------'
  echo 'Uninstalling Auth Service chart'
  echo '----------------------------------------'
  SERVICE="auth-service"
  helm uninstall $SERVICE -n "$PROJECT_NAMESPACE"

  echo '----------------------------------------'
  echo 'Uninstalling Ridely Service chart'
  echo '----------------------------------------'
  SERVICE="ridely-service"
  helm uninstall $SERVICE -n "$PROJECT_NAMESPACE"
fi

