#!/usr/bin/env bash

# ================================
# Stores an object in the AWS S3 Secrets Bucket
# This script will install the AWS CLI and the CF CLI
# If you don't want these on your system, install using the docker helper script:
# ./develop bash cloudfoundry/store_env.sh
# ================================

# Set environment variables.
CF_API='https://api.cloud.service.gov.uk'
CF_SECRET_SERVICE=hys-secret
CF_SECRET_SERVICE_KEY=hys-secret-key
APPROOT=${APPROOT:-'/var/www/html'}

# Get the Cloud Foundry details
echo 'Cloudfoundry Username?'

read CF_USERNAME

echo 'Cloudfoundry Password?'

read CF_PASSWORD

echo 'Cloudfoundry Organisation?'

read CF_ORGANISATION

echo 'Cloudfoundry Space?'

read CF_SPACE

# Install AWS CLI
echo "Installing AWS CLI..."
rm -Rf ${APPROOT}/aws
wget -q -O awscliv2.zip https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip
unzip awscliv2.zip
${APPROOT}/aws/install
aws --version
rm  awscliv2.zip

# Install CF
echo "Installing CloudFoundry CLI..."
apt-get update && apt-get install -y --allow-unauthenticated gnupg
wget -q -O - https://packages.cloudfoundry.org/debian/cli.cloudfoundry.org.key | apt-key add -
echo "deb https://packages.cloudfoundry.org/debian stable main" | tee /etc/apt/sources.list.d/cloudfoundry-cli.list
apt-get update && apt-get install -y --allow-unauthenticated cf7-cli jq

# Get the upload details
echo 'Which environment is to be updated? (staging or production)'

read ENVIRONMENT

if [ "$ENVIRONMENT" != 'staging' ] && [ "$ENVIRONMENT" != 'production' ]
then
    echo 'The environment should be one of staging or production'
    exit
else
    ENV_SECRET_FILE=".env.api.${ENVIRONMENT}"
fi

echo 'What is the path to the new environment file? relative to the application root (e.g. .env)'

read FILE_PATH

if [ ! -e "$APPROOT/$FILE_PATH" ]
then
    echo 'The environment file does not exist'
    exit
fi

# Login to Cloud Foundry.
cf login -a $CF_API -u $CF_USERNAME -p $CF_PASSWORD -o $CF_ORGANISATION -s $CF_SPACE

# Get the .env file from the secret S3 bucket
cf service-key $CF_SECRET_SERVICE $CF_SECRET_SERVICE_KEY
cf service-key $CF_SECRET_SERVICE $CF_SECRET_SERVICE_KEY | sed -n '/{/,/}/p' | jq . > secret_access.json

# Export the AWS S3 access credentials for use by the AWS CLI
export AWS_ACCESS_KEY_ID=`jq -r .aws_access_key_id secret_access.json`
export AWS_DEFAULT_REGION=`jq -r .aws_region secret_access.json`
export AWS_SECRET_ACCESS_KEY=`jq -r .aws_secret_access_key secret_access.json`
export AWS_BUCKET_NAME=`jq -r .bucket_name secret_access.json`
export AWS_DEFAULT_OUTPUT=json

rm secret_access.json

echo "Uploading $APPROOT/$FILE_PATH to bucket $AWS_BUCKET_NAME as object $ENV_SECRET_FILE"

aws s3api put-object --bucket ${AWS_BUCKET_NAME} --key "$ENV_SECRET_FILE" --body "$APPROOT/$FILE_PATH"

# Remove the AWS client
rm -Rf ${PWD}/aws
