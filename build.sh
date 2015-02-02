#!/bin/bash
cd ~/Projects/chippyash/source/Strong-Type
vendor/phpunit/phpunit/phpunit -c test/phpunit.xml --testdox-html contract.html test/
tdconv -t "Chippyash Strong Types" contract.html docs/Test-Contract.md
rm contract.html

