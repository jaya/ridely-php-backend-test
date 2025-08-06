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

# Function to wait for a service to become available
wait_for_service() {
  local name="$1"
  local namespace="$2"
  local max_retries=10
  local i=0

  echo "Waiting for service $name..."
  until kubectl get svc "$name" -n "$namespace" > /dev/null 2>&1 || [ "$i" -ge "$max_retries" ]; do
    sleep 1
    i=$((i + 1))
  done

  if [ "$i" -ge "$max_retries" ]; then
    echo "Service $name not found. Skipping..."
    return 1
  fi

  echo "Service $name is available"
  return 0
}

# Function to wait for a pod with a specific label to become available
wait_for_pod() {
  local label="$1"
  local namespace="$2"
  local max_retries=3
  local i=0

  echo "⏳ Waiting for pod with label $label..."
  until kubectl get pods -n "$namespace" -l "$label" -o jsonpath="{.items[0].metadata.name}" | grep -q . || [ "$i" -ge "$max_retries" ]; do
    sleep 1
    i=$((i + 1))
  done

  if [ "$i" -ge "$max_retries" ]; then
    echo "No pod found with label $label. Skipping..."
    return 1
  fi

  echo "Pod with label $label is available"
  return 0
}

#SERVICE_NAME="ridely-database"
#if kubectl get svc -n "$PROJECT_NAMESPACE" | grep -q "^$SERVICE_NAME "; then
#  echo "Service $SERVICE_NAME exists"
#else
#  echo "Service $SERVICE_NAME not found"
#fi

#exit
# Forward MySQL port if the service is available
if wait_for_service "$SHARED_DATABASE_CHART_NAME" "$PROJECT_NAMESPACE"; then
  echo "kubectl port-forward svc/$SHARED_DATABASE_CHART_NAME 3306:3306 -n $PROJECT_NAMESPACE"
  kubectl port-forward "svc/$SHARED_DATABASE_CHART_NAME" 3306:3306 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &
  echo ""
fi

# Forward Redis port if the service is available
if wait_for_service "$SHARED_CACHE_DATABASE_CHART_NAME-redis-master" "$PROJECT_NAMESPACE"; then
  echo "kubectl port-forward svc/$SHARED_CACHE_DATABASE_CHART_NAME-redis-master 6379:6379 -n $PROJECT_NAMESPACE"
  kubectl port-forward "svc/$SHARED_CACHE_DATABASE_CHART_NAME-redis-master" 6379:6379 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &
  echo ""
fi

# Forward PostgreSQL (Auth) port if the service is available
if wait_for_service "auth-service-postgresql" "$PROJECT_NAMESPACE"; then
  echo "kubectl port-forward svc/auth-service-postgresql 5432:5432 -n $PROJECT_NAMESPACE"
  kubectl port-forward "svc/auth-service-postgresql" 5432:5432 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &
  echo ""
fi


# Forward Keycloak pod port if the pod is available
if wait_for_service "auth-service-keycloak" "$PROJECT_NAMESPACE"; then
  # TODO migrar para o serviço
  POD_NAME=$(kubectl get pods -n "$PROJECT_NAMESPACE" -l "app.kubernetes.io/name=keycloak" -o jsonpath="{.items[0].metadata.name}")
  echo "kubectl port-forward $POD_NAME 8080:8080 -n $PROJECT_NAMESPACE"
  kubectl port-forward "$POD_NAME" 8080:8080 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &
  echo ""
fi

## Forward Location Service pod port if the pod is available
#if wait_for_service "location-service-nominatim" "$PROJECT_NAMESPACE"; then
#  echo "kubectl port-forward svc/location-service-nominatim 8010:8010 -n $PROJECT_NAMESPACE"
#    kubectl port-forward "svc/location-service-nominatim" 8010:8010 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &
#  echo ""
#fi

# Forward Ridely service pod port if the pod is available
if wait_for_service "ridely-service" "$PROJECT_NAMESPACE"; then
  POD_NAME=$(kubectl get pods -n "$PROJECT_NAMESPACE" -l "app=ridely-service" -o jsonpath="{.items[0].metadata.name}")
  echo "kubectl port-forward $POD_NAME 8000:80 -n $PROJECT_NAMESPACE"
  kubectl port-forward "$POD_NAME" 8000:80 -n "$PROJECT_NAMESPACE" > /dev/null 2>&1 &
  echo ""
fi

