#/bin/sh

rsync -arvun --exclude '.git' --exclude 'templates_c' --exclude '*~' /home/asc/pua.spum.org/dev/ /home/asc/pua.spum.org/www/
