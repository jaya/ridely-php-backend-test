#!/bin/bash

echo '----------------------------------------'
echo 'PlantUML create diagrams'
echo '----------------------------------------'

# Paths
#PROJECT_ROOT=$(dirname "$(dirname "$(dirname "$0")")")
PROJECT_ROOT="./docs"
PLANTUML_JAR="$PROJECT_ROOT/plantuml.jar"
DIAGRAMS_DIR="$PROJECT_ROOT/architecture/diagrams"

echo "PROJECT_ROOT: $PROJECT_ROOT"
echo "PLANTUML_JAR: $PLANTUML_JAR"
echo "DIAGRAMS_DIR: $DIAGRAMS_DIR"
if [ ! -d "$PROJECT_ROOT" ]; then
    echo "Error: incorrect folder, you must be at the project root to run this project"
    exit 1
fi

# Check if plantuml.jar exists
if [ ! -f "$PLANTUML_JAR" ]; then
    echo "Error: plantuml.jar not found at $PLANTUML_JAR"
    exit 1
fi


if [ ! -d "$DIAGRAMS_DIR" ]; then
    echo "Error: diagrams directory not found at $DIAGRAMS_DIR"
    exit 1
fi

# Gera os PNGs
# Generate PNGs from .puml files
echo "Generating diagrams from .puml files in $DIAGRAMS_DIR..."
find "$DIAGRAMS_DIR" -name '*.puml' | while read -r file; do
    echo " > Processing: $(basename "$file")"
    java -jar "$PLANTUML_JAR" -tpng "$file"
done

echo "✅ Diagrams generated successfully."