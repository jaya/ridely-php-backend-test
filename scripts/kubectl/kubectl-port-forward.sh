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
echo '----------------------------------------'
echo 'Port forwarding...'
echo '----------------------------------------'
echo "kubectl port-forward svc/$SHARED_DATABASE_CHART_NAME 3306:3306 -n $PROJECT_NAMESPACE"
kubectl port-forward "svc/$SHARED_DATABASE_CHART_NAME" 3306:3306 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &

echo "kubectl port-forward svc/auth-service-postgresql 5432:5432 -n $PROJECT_NAMESPACE"
kubectl port-forward "svc/auth-service-postgresql" 5432:5432 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &

export POD_NAME=$(kubectl get pods --namespace "$PROJECT_NAMESPACE" -l "app.kubernetes.io/name=keycloak" -o jsonpath="{.items[0].metadata.name}")
echo "kubectl port-forward $POD_NAME 8080:8080 -n $PROJECT_NAMESPACE"
kubectl port-forward "$POD_NAME" 8080:8080 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &


