#/bin/sh

rsync -arvu --exclude '.git' --exclude 'templates_c' --exclude '*~' /home/asc/pua.spum.org/dev/ /home/asc/pua.spum.org/www/
