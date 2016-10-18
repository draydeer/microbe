#!/bin/bash

if [ ! -z "$2" ]; then
    phpunit -v --bootstrap tests/bootstrap/bootstrap.php tests/test-$1/$2_Test
else
    for FILE in $(ls tests/tests-$1); do phpunit -v --bootstrap tests/bootstrap/bootstrap.php tests/tests-$1/$FILE; done
fi
