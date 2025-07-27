#!/bin/bash

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

NAME="$1"
TAG="$2"
PATH="$3"
CONTEXT_PATH="$4"
if [ -z "$1" ]; then
  echo 'Name not defined'
  exit 1
fi

if [ -z "$2" ]; then
  echo 'Tag not defined'
  exit 1
fi

if [ -z "$3" ]; then
  echo 'Path not defined'
  exit 1
fi

if [ -z "$4" ]; then
  echo 'Context Path not defined'
  exit 1
fi

echo "docker build -t \"$NAME\":\"$TAG\" -f \"$PATH\" \"$CONTEXT_PATH\""
/usr/bin/docker build -t "$NAME":"$TAG" -f "$PATH" "$CONTEXT_PATH"
