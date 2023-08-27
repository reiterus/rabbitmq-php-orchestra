#!/bin/sh

/usr/bin/composer install &&
chown -R 1000:1000 ./vendor &&

if [[ "$RMQ_IS_BUILDER" -eq 1 ]]; then
    exit 0
fi

if [[ -z "$RMQ_CASE" ]]; then
   echo ">>>>> Case is: \"hello\"" &&
   script="/app/$RMQ_CONSUMER"
else
   echo ">>>>> Case is: \"$RMQ_CASE\"" &&
   script="/app/case/$RMQ_CASE/$RMQ_CONSUMER"
fi

echo ">>>>> Script Path: $script" &&
php $script
