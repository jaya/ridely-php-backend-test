#!/bin/bash
# use the set -u command to ensure all variables are set before using
#set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

if [ -z "$1" ]; then
  echo 'Image name:version not defined'
  exit 1
fi

echo "kind load docker-image \"$1\" -n \"$CLUSTER_NAME\""
kind load docker-image "$1" -n "$CLUSTER_NAME"