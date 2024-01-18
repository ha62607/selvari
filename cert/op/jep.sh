#!/bin/bash

# To run this you need openssl and jq installed.

# Steps for registering the required keys and certificates
# 1. Valid Corporate API contract created through the web UI
# 2. OAuth clientId and clientSecret provisioned
# 3. MTLS private key generated: openssl genrsa -out sandbox-mtls.key 4096
# 4. MTLS certificate signing request (CN and other attributes are ignored): openssl req -new -key sandbox-mtls.key -out sandbox-mtls.csr
# 5. Valid MTLS certificate aquired from the web UI using the csr from step 4. and in "sandbox-mtls.crt" file

# OAuth credentials
clientId="73kYwIV9CAQlbcLCJdXr4Q"
clientSecret="A523b9zSJze7Vwy3speQiqtsdwtozTOKrHbebyO8W3FqPQi25nUpUk2wRZBE64mMcxo0RCVxJiqK8vD4-xm0pQ"

# MTLS credentials
mtlsKey="./sandbox-mtls.key"
mtlsCertificate="./sandbox-mtls.crt"

API_SERVER="https://sandbox-api.apiauth.aws.op-palvelut.net"

echo "Getting access token"
reply=$(curl -s ${API_SERVER}/corporate-oidc/v1/token \
    --key ${mtlsKey} \
    --cert ${mtlsCertificate} \
    -H 'Content-Type: application/x-www-form-urlencoded' \
    -d "grant_type=client_credentials&client_id=${clientId}&client_secret=${clientSecret}")

token=$(echo $reply | jq -r .access_token)


echo "Access token is: $token"

echo "Fetching account listing"
accounts=$(curl -s ${API_SERVER}/corporate-account-data/v1/accounts \
--key ${mtlsKey} \
--cert ${mtlsCertificate} \
-H "Authorization: Bearer $token")

echo $accounts | jq -C .

ACCOUNTID=$(echo $accounts | jq -r .[0].surrogateId)

echo "Fetching accountInfo for account $ACCOUNTID"
accountinfo=$(curl -s ${API_SERVER}/corporate-account-data/v1/accounts/$ACCOUNTID \
--key ${mtlsKey} \
--cert ${mtlsCertificate} \
-H "Authorization: Bearer $token")

echo $accountinfo | jq -C .

echo "Fetching transactions for account $ACCOUNTID"
transactions=$(curl -s ${API_SERVER}/corporate-account-data/v1/accounts/$ACCOUNTID/transactions \
--key ${mtlsKey} \
--cert ${mtlsCertificate} \
-H "Authorization: Bearer $token")

echo "Ttransactions for account $ACCOUNTID:"
echo $transactions | jq -C .


