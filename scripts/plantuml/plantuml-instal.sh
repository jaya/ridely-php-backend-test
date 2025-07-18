#!/bin/bash

echo '----------------------------------------'
echo "PlantUML diagrams resources installation"
echo '----------------------------------------'
echo "Checking the dependencies"
echo '----------------------------------------'
echo "Validating Java installation..."
echo '----------------------------------------'
# check java version
java -version > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo 'You need to install Java. The minimum version needed is Java 8.'
  echo 'Read more details at: https://plantuml.com/starting'
  echo 'https://plantuml.com/faq-install'
  exit
else
  echo 'Java installed'
fi

echo '----------------------------------------'
echo "Validating GraphViz installation..."
echo '----------------------------------------'
# sudo sudo apt-get install graphviz libgraphviz-dev pkg-config
sudo sudo apt-get install graphviz

cho '----------------------------------------'
echo "Validating PlantUml installation..."
echo '----------------------------------------'
if [ ! -f 'plantuml.jar' ]; then
  echo 'Executing the download of plantuml.jar (https://plantuml.com/download)'
  wget https://github.com/plantuml/plantuml/releases/download/v1.2025.0/plantuml-mit-1.2025.0.jar
#  mv plantuml-mit-1.2025.0.jar plantuml.jar
  mv ./plantuml-mit-1.2025.0.jar ./docs/plantuml.jar

  echo "PlantUML installed"
else
  echo 'The plantuml.jar is already installed'
fi