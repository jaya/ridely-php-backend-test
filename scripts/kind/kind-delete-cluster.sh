#!/bin/bash
# use the set -u command to ensure all variables are set before using
set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

# get the clusters
# echo "kubectl config get-contexts ${CLUSTER_NAME}"
# if kubectl config get-contexts ${CLUSTER_NAME} &> /dev/null; then

# get the clusters
echo "kind get clusters | grep -q \"^${CLUSTER_NAME}$\""
if kind get clusters | grep -q "^${CLUSTER_NAME}$"; then
  echo "Cluster exists"
  echo "kind delete cluster --name ${CLUSTER_NAME}"
  kind delete cluster --name ${CLUSTER_NAME}
else
  echo "Cluster doesn't exists"

fi
