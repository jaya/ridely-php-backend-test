#!/bin/bash
# use the set -u command to ensure all variables are set before using
set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

f [ -z "$1" ]; then
  echo 'First argument not defined'
  exit 1
else
#  docker-compose exec $1 /bin/sh
fi