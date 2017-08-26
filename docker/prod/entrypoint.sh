#!/bin/bash
set -o errexit
set -o nounset
set -o pipefail

echo $PTRON_BASIC_AUTH > /etc/apache2/sites-available/puzzletron.htpasswd
apache2-foreground
