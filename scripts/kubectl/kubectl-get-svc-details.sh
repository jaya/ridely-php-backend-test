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

#
#kubectl get pods -n $PROJECT_NAMESPACE
#kubectl describe pod <nome-do-pod> -n $PROJECT_NAMESPACE
#kubectl logs <nome-do-pod> -n $PROJECT_NAMESPACE

# Suponha que você quer filtrar por pods com "ridely-databases" no nome
#POD=$(kubectl get pods -n "$PROJECT_NAMESPACE" --no-headers | grep "$SHARED_DATABASE_CHART_NAME" | head -n 1 | awk '{print $1}')
#
## Agora você pode usar $POD nos demais comandos:
#kubectl describe pod "$POD" -n "$PROJECT_NAMESPACE"
#kubectl logs "$POD" -n "$PROJECT_NAMESPACE"
#kubectl logs "job/$SHARED_DATABASE_CHART_NAME-connection-test" -n "$PROJECT_NAMESPACE"

kubectl get svc -n "$PROJECT_NAMESPACE"
