#!/bin/bash
set -e

docker pull quay.io/keboola/aws-cli
eval $(docker run --rm -i -e AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID -e AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY quay.io/keboola/aws-cli ecr get-login --region eu-central-1)
docker tag keboola/docker-runner-2-poc:latest 061240556736.dkr.ecr.eu-central-1.amazonaws.com/keboola/docker-runner-2-poc:latest
docker push 061240556736.dkr.ecr.eu-central-1.amazonaws.com/keboola/docker-runner-2-poc:latest