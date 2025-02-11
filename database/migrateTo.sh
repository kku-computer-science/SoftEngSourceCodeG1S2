#!/bin/sh

if [ "$#" -ne 4 ]; then
    echo "Illegal number of parameters"
    echo "Usage: $0 <host> <user> <password> <database>"
    exit 1
fi


mysql -h $1 -u"$2" -p"$3" -D $4 < database/shcema.sql

mysql -h $1 -u"$2" -p"$3" -D $4 < database/data.sql