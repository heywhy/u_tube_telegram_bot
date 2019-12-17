#!/bin/bash

echo 'Download Credentials';

curl $GOOGLE_CRED_URL -o ./service-accounts.json
