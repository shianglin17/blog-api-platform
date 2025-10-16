#!/usr/bin/env bash
# Wrapper for Composer scripts: strips unsupported -d flags then calls FrankenPHP
args=("$@")
index=0
for i in "$@"; do
    if [ "$i" = "-d" ]; then
        unset "args[$index]"
        unset "args[$((index+1))]"
    fi
    index=$((index+1))
done
exec /usr/local/bin/frankenphp php-cli "${args[@]}"
