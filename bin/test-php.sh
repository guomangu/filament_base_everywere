#!/bin/bash
ARGS=()
skip_next=0
for arg in "$@"; do
    if [ $skip_next -eq 1 ]; then
        skip_next=0
        continue
    fi
    if [[ "$arg" == "-d" ]]; then
        skip_next=1
        continue
    fi
    if [[ "$arg" == -d* ]]; then
        continue
    fi
    ARGS+=("$arg")
done
echo "ARGS: ${ARGS[@]}"
