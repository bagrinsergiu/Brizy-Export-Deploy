#!/bin/bash

cd /tmp

git clone --depth=1 git@github.com:bagrinsergiu/Brizy-Export-Deploy.git

cd Brizy-Export-Deploy

composer install --optimize-autoloader --no-dev

#remove files for git
rm -rf .git
rm .gitignore

timestamp=$(date +%s)

#create tar.gz
tar -czvf Brizy-Export-Deploy-${timestamp}.tar.gz *