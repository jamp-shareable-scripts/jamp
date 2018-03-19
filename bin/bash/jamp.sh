#!/bin/bash

# Jamp - Command Line Apps for PHP
# Author: jamp-shareable-scripts <https://github.com/jamp-shareable-scripts>
# License: GPL-2.0

# Get the base directory as shown by
# https://stackoverflow.com/a/246128
export JAMP_BASE "$( dirname "$( dirname "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )" )" )/"

# Get the name of the script to run.
export JAMP_SCRIPT=$1

# Treat what was formerly $2 as $1, $3 as $2 and so on.
shift

# Use a common PHP entry point to wrap the script.
php "${JAMP_BASE}bin/entry.php" "$@"
