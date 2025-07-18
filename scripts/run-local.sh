#!/bin/bash

# Include the project variables
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

echo '----------------------------------------'
echo "Validating .projectrc file..."
echo '----------------------------------------'
if [ -z "$PROJECT_NAME" ]; then
  echo '.projectrc file not found, please review the project settings, this file contains project variables for the scripts'
  exit 1
else
  echo "Project name: ${PROJECT_NAME}"
  echo "Cluster name: ${CLUSTER_NAME}"
fi

echo ""
echo 'Preparing to run the project locally'

# TODO adicionar passos para instalar o helm
# https://helm.sh/docs/intro/install/

echo '----------------------------------------'
echo "Validating Kind installation..."
echo '----------------------------------------'
kind --version > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo "Kind not installed, installing..."
  bash ./scripts/kind/kind-install.sh
else
  echo "Kind installed"
fi

echo '----------------------------------------'
echo 'Checking cluster'
echo '----------------------------------------'
bash ./scripts/kubectl/kubectl-create-namespace.sh

echo '----------------------------------------'
echo 'Checking cluster'
echo '----------------------------------------'
bash ./scripts/kind/kind-create-cluster.sh



echo '----------------------------------------'
echo 'Validating charts'
echo '----------------------------------------'
bash ./scripts/helm/helm-validate-charts.sh

if [ $? -ne 0 ]; then
  echo 'Validation error, exiting...'
  exit 1
else
  echo 'Validation OK'
fi

echo '----------------------------------------'
echo 'Installing charts'
echo '----------------------------------------'
bash ./scripts/helm/helm-install-charts.sh

echo '----------------------------------------'
echo 'Checking helms'
echo '----------------------------------------'
helm list -n $PROJECT_NAMESPACE
kubectl get all -n $PROJECT_NAMESPACE

echo '----------------------------------------'
echo 'Testing charts'
echo '----------------------------------------'
bash ./scripts/helm/helm-test-charts.sh

echo '----------------------------------------'
echo 'Port Forward'
echo '----------------------------------------'
bash ./scripts/kubectl/kubectl-port-forward.sh