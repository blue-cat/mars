#!/bin/bash

git pull
git add .
git ci -m "fixed something"

if test "$(git rev-parse --abbrev-ref HEAD)" != master; then
  if test "$(git rev-parse --abbrev-ref HEAD)" != dev; then
    git push origin test
    git push gz test
  else
    #dev目录下，只合并到master或者test
    if [[ $1 == "m" ]]; then
      git co master
    else
      git co test
    fi
    git merge dev
  fi
else
  git push origin master-2x
  git push sh master-2x
  #git push gz master
fi
