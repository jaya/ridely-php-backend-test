#!/bin/bash

echo '----------------------------------------'
echo 'Validating the status of a chart...'
echo '----------------------------------------'
# database helm
helm status ridely-database -n $PROJECT_NAMESPACE

