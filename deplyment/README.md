TO RUN 
docker build -t my-ubuntu-apache-php .
TO expose port 
docker run -d -p 8080:80 my-ubuntu-apache-php


FOR AWS CLI DYNOBM SETUP 

aws dynamodb create-table \
    --table-name AccountsTable \
    --attribute-definitions AttributeName=email,AttributeType=S \
    --key-schema AttributeName=email,KeyType=HASH \
    --provisioned-throughput ReadCapacityUnits=5,WriteCapacityUnits=5


FOR CREATE USER 

aws iam create-user --user-name joseph

FOR ATTACHING TO POLICY 


aws iam put-user-policy \
    --user-name joseph \
    --policy-name DynamoDBAccessPolicy \
    --policy-document file://dynamodb_policy.json

FOR GENETRATING ACCESS KEY CLI 

aws iam create-access-key --user-name joseph

