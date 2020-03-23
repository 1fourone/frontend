#!/bin/bash

# Script that pushes current contents of repo to 1fourone.io/webgrader for testing.
# Will always kept the most recent changes to source.
# Once satisfied with changes, should put appropriate files in AFS.
# Moves all files on every change

for f in ./front/ ./back ./mid; do
    if [[ $f != *.sh ]]
    then
        scp -r $f pi@1fourone.io:/var/www/html/webgrader
    fi
done