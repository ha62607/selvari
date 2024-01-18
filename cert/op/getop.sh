#!/bin/bash

# To run this you need openssl and jq installed.

# Steps for registering the required keys and certificates
# 1. Valid Corporate API contract created through the web UI
# 2. OAuth clientId and clientSecret provisioned
# 3. MTLS private key generated: openssl genrsa -out sandbox-mtls.key 4096
# 4. MTLS certificate signing request (CN and other attributes are ignored): openssl req -new -key sandbox-mtls.key -out sandbox-mtls.csr
# 5. Valid MTLS certificate acquired from the web UI using the csr from step 4. and in "sandbox-mtls.crt" file

# OAuth credentials
clientId="73kYwIV9CAQlbcLCJdXr4Q"
clientSecret="A523b9zSJze7Vwy3speQiqtsdwtozTOKrHbebyO8W3FqPQi25nUpUk2wRZBE64mMcxo0RCVxJiqK8vD4-xm0pQ"

# MTLS credentials
mtlsKey="sandbox-mtls.key"
mtlsCertificate="sandbox-mtls.crt"

API_SERVER="https://sandbox-api.apiauth.aws.op-palvelut.net"

echo "Getting access token"
reply=$(curl -s ${API_SERVER}/corporate-oidc/v1/token \
 --key ${mtlsKey} \
 --cert ${mtlsCertificate} \
 -H 'Content-Type: application/x-www-form-urlencoded' \
 -d "grant_type=client_credentials&client_id=${clientId}&client_secret=${clientSecret}")


token=$(echo $reply | jq -r .access_token)
echo "Access token is: $token"



### FILTER CREDIT TRANSACTIONS BY DATE RANGE

echo "Get all credit transactions between 1.12 – 31.12.2022:"
filtered=$(curl -s \
-d '{"accountIban":"FI6359991620014321","startDate":"2022-10-01T00:00:01Z","endDate":"2022-12-31T00:00:01Z"}' \
-H "Content-Type: application/json" \
-H 'Accept: application/json' \
-X POST ${API_SERVER}/corporate-transaction-filter/v2/credit-transactions \
--key ${mtlsKey} \
--cert ${mtlsCertificate} \
-H "Authorization: Bearer $token")

echo -e "\n"
echo -e "FILTERED TRANSACTIONS BETWEEN 01.10 – 31.12.2022:\n"
echo $filtered | jq -C .

### FILTER DEBIT TRANSACTIONS BY REFERENCE

echo "Get debit transactions with a reference 00000000000000396224:"
filtered=$(curl -s \
-d '{"accountIban":"FI3859991620004143","startDate":"2020-01-01T00:00:01Z","endDate":"2020-01-30T00:00:01Z","reference":"00000000000000396224"}' \
-H "Content-Type: application/json" \
-H 'Accept: application/json' \
-X POST ${API_SERVER}/corporate-transaction-filter/v2/debit-transactions \
--key ${mtlsKey} \
--cert ${mtlsCertificate} \
-H "Authorization: Bearer $token")

echo -e "\n"
echo -e "FILTERED TRANSACTION WITH A REFERENCE:\n"
echo $filtered | jq -C .

### FILTER CREDIT TRANSACTIONS BY AMOUNT RANGE AND PAYER NAME

echo "Get payments between €0.00 and €10.00 from a company:"
filtered=$(curl -s \
-d '{"accountIban":"FI3859991620004143","startDate":"2020-01-01T00:00:01Z","endDate":"2020-01-30T00:00:01Z","minAmount":"0.00","maxAmount":"10.00","debtorName":"YRITYS KOLMONEN"}' \
-H "Content-Type: application/json" \
-H 'Accept: application/json' \
-X POST ${API_SERVER}/corporate-transaction-filter/v2/credit-transactions \
--key ${mtlsKey} \
--cert ${mtlsCertificate} \
-H "Authorization: Bearer $token")

echo -e "\n"
echo -e "FILTERED CREDIT TRANSACTIONS BETWEEN €0 AND €10.00 FROM A COMPANY:\n"
echo $filtered | jq -C .


