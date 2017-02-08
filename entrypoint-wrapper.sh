#!/bin/bash

# Os diretórios `languages`, `plugins`, `themes` e `uploads` só funcionarão
# corretamente no wordpress caso pertençam ao usuário do servidor web.

WP_DIR='/var/www/html/wp-content/'
CONTENT_DIRS='languages plugins themes uploads'

for directory in ${CONTENT_DIRS}
do
  if [ -e ${WP_DIR}/${directory} ]; then
    chown -hR www-data:www-data ${WP_DIR}/${directory}
  fi
done

source docker-entrypoint.sh $@
