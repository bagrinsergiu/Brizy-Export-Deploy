#!/bin/bash

cd /tmp || exit;

mkdir "export-deploy";

cd /tmp/export-deploy || exit;

git clone --depth=1 --branch=master git@github.com:bagrinsergiu/Brizy-Export-Deploy.git brizy

cd brizy || exit;

composer install --optimize-autoloader --no-dev

#remove files for git
rm -rf .git
rm .gitignore

#set permissions
find . -type d -print0 | xargs -0 chmod 0775
find . -type f -print0 | xargs -0 chmod 0644

cd ../ || exit;

zip -r brizy.zip brizy

#upload zip to S3
aws s3 cp brizy.zip s3://brizy-export/

rm -rf /tmp/export-deploy