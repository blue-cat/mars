#!/bin/bash

note="fixed something"
if [ "$1" != "" ] && [ "$1" != "m" ];then
  note=$1
fi

git pull
git add .
git ci -m `echo $note`

if test "$(git rev-parse --abbrev-ref HEAD)" != master-2x; then
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
