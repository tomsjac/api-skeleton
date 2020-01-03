#!/bin/bash

# Init default database
init_default_database() {
    echo "
        CREATE DATABASE ${DB_DATABASE};
        GRANT ALL ON ${DB_DATABASE}.* TO ${DB_USERNAME}@'%' IDENTIFIED BY '${DB_PASSWORD}';
    " | mysql -u ${DOCKER_DB_ROOT_USERNAME} --password=${DOCKER_DB_ROOT_PASSWORD}
}


# Main execution:
main() {
  init_default_database
}

# Executes the main routine with environment variables
# passed through the command line. We don't use them in
# this script but now you know 🤓
main "$@"
