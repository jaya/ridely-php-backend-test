#!/bin/bash
# use the set -u command to ensure all variables are set before using
# set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

echo '----------------------------------------'
echo 'Deleting databases pvc'
echo '----------------------------------------'
echo 'kubectl delete pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" > /dev/null 2>&1'
kubectl delete pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" > /dev/null 2>&1

if kubectl get pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" &>/dev/null; then
  kubectl delete pvc -n "$PROJECT_NAMESPACE" "$SHARED_DATABASE_CHART_NAME-pvc" \
    --ignore-not-found --wait=false &
fi