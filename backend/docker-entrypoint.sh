#!/bin/bash

# Start Apache in the background
apache2-foreground &

# Start Supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
