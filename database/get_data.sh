#!/bin/sh

if [ "$#" -ne 4 ]; then
    echo "Illegal number of parameters"
    echo "Usage: $0 <host> <user> <password> <database>"
    exit 1
fi

mysqldump -h $1 -u"$2" -p"$3" --no-create-info --complete-insert --skip-extended-insert $4 > database/data.sql