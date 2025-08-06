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
echo 'Installing metrics server'
echo '----------------------------------------'
echo 'repo add metrics-server https://kubernetes-sigs.github.io/metrics-server/'
helm repo add metrics-server https://kubernetes-sigs.github.io/metrics-server/
helm repo update
helm install metrics-server metrics-server/metrics-server -n kube-system --set args="{--kubelet-insecure-tls,--kubelet-preferred-address-types=InternalIP}"

#helm upgrade --install metrics-server metrics-server/metrics-server \
#  -n kube-system \
#  --set args="{--kubelet-insecure-tls,--kubelet-preferred-address-types=InternalIP}"

