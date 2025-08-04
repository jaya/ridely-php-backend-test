#!/bin/bash
# use the set -u command to ensure all variables are set before using
# set -u
# causes the script to stop immediately if any command returns a non-zero error code.
set -e

# Include the project variables file
if test -f .projectrc; then
  source .projectrc
elif test -f ./scripts/.projectrc; then
  source ./scripts/.projectrc
fi

# Default version: Get the latest stable version from GitHub
DEFAULT_VERSION=$(curl -s https://api.github.com/repos/GoogleContainerTools/skaffold/releases/latest | grep tag_name | cut -d '"' -f 4)

# Use the version passed as argument ($1), or fallback to the default
VERSION=${1:-$DEFAULT_VERSION}

echo "Installing Skaffold version: $VERSION"

# Download binary
curl -Lo skaffold "https://storage.googleapis.com/skaffold/releases/${VERSION}/skaffold-linux-amd64"

# Make executable
chmod +x skaffold

# Move to /usr/local/bin (requires sudo)
sudo mv skaffold /usr/local/bin/

# Check installed version
skaffold version > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo "Error during the installation of skaffold"
  exit $?
else
  echo "Skaffold installed!"
  exit 0
fi