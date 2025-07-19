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

ROOT_DIR=$(pwd)

echo '----------------------------------------'
echo 'Deleting database pvc'
echo '----------------------------------------'
# TODO talvez somente habilitar esse bloco quando necessário
#echo 'kubectl delete pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" > /dev/null 2>&1'
#kubectl delete pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" > /dev/null 2>&1

#if kubectl get pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" &>/dev/null; then
#  kubectl delete pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" \
#    --ignore-not-found --wait=false &
#fi

echo '----------------------------------------'
echo 'Installing database chart'
echo '----------------------------------------'
helm install "$SHARED_DATABASE_CHART_NAME" "./database/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE"

if [ $? -ne 0 ]; then
  echo 'Trying to upgrade the existing one...'
   helm upgrade "$SHARED_DATABASE_CHART_NAME" "./database/charts/$SHARED_DATABASE_CHART_NAME/" -n "$PROJECT_NAMESPACE"
fi

echo '----------------------------------------'
echo 'Installing services chart'
echo '----------------------------------------'
echo 'Installing Auth Service chart'
echo '----------------------------------------'
echo 'Moving to the directory ./backend/charts/auth-service/'

cd ./backend/charts/auth-service/

if [ -z "$(ls -A ./charts)" ]; then
#   echo 'Building dependency'
#   helm dependency build
   echo 'Updating dependency'
   helm dependency update
else
  echo "Dependency already installed"
fi

echo "returning to root directory: $ROOT_DIR "
cd $ROOT_DIR

#helm dependency update "auth-service" "./backend/charts/auth-service/" -n "$PROJECT_NAMESPACE"
helm install "auth-service" "./backend/charts/auth-service/" -n "$PROJECT_NAMESPACE" --values ./backend/charts/auth-service/values.yaml

if [ $? -ne 0 ]; then
  echo 'Trying to upgrade the existing one...'
  helm upgrade "auth-service" "./backend/charts/auth-service/" -n "$PROJECT_NAMESPACE"  --values ./backend/charts/auth-service/values.yaml
fi


