#!/bin/sh

find . -type d | xargs chmod 771
find . -type d -name CVS | xargs chmod 770
find . -type f | xargs chmod 664
find . -type f -name "*.pl" | xargs chmod 775
find . -type f -name "*.sh" | xargs chmod 775
find . -type f -name "installer" | xargs chmod 775
find ./htdocs -type f | xargs chmod 774
find ./htdocs -type d | xargs chmod 771
