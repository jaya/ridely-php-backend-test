#!/bin/bash
# use the set -u command to ensure all variables are set before using
#set -u

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi



echo '----------------------------------------'
echo 'Backend setup'
echo '----------------------------------------'
echo "Preparing Ridely Service Nginx"
echo '----------------------------------------'
bash ./scripts/nginx/nginx-gen-certs.sh ./backend/services/ridely-service/docker/nginx