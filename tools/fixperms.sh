#!/bin/sh

find . -type d | xargs chmod 711
find . -type d -name CVS | xargs chmod 700
find . -type f | xargs chmod 644
find . -type f -name "*.pl" | xargs chmod 755
find . -type f -name "*.sh" | xargs chmod 755
find . -type f -name "installer" | xargs chmod 755
find ./htdocs -type f | xargs chmod 744
find ./htdocs -type d | xargs chmod 711
