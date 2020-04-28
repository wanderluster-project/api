#!/bin/bash

cd /var/www/wanderluster/config/certs
openssl req  -nodes -new -x509  -keyout jwt.key -out jwt.cert