#!/bin/bash

CERT_PATH=$1
if [ -z "$1" ]; then
  CERT_PATH="."
fi
if [ ! -f "$CERT_PATH/certificate.crt" ]; then
  OUT="$CERT_PATH/certificate.crt"
  KEYOUT="$CERT_PATH/certificate.key"

  echo $CERT_PATH
  echo $OUT
  echo $KEYOUT

  openssl req -newkey rsa:2048 \
          -x509 \
          -sha256 \
          -nodes \
          -days 365 \
          -out $OUT \
          -keyout $KEYOUT \
          -subj "/C=BR/CN=localhost"
else
  echo "Certificate already exists. Skipping generation."
fi