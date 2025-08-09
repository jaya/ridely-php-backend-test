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

echo '----------------------------------------'
echo 'Uninstalling monitoring chart...'
echo '----------------------------------------'
helm uninstall kube-prometheus-stack --namespace monitoring

#helm uninstall kube-prometheus-stack prometheus-community/kube-prometheus-stack --namespace monitoring --create-namespace
#kubectl port-forward -n monitoring svc/kube-prometheus-stack-grafana 3000:80

# User/pass: admin / prom-operator