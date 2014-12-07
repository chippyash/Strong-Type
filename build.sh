#!/bin/bash
cd ~/Projects/Type
vendor/phpunit/phpunit/phpunit -c test/phpunit.xml --testdox-html contract.html test/
tdconv -t "Chippyash Strong Types" contract.html docs/Test-Contract.md
rm contract.html
vendor/bin/apigen --title "Chippyash Strong Types" --php no --source-code no --source src/chippyash/Type/ --destination docs/apigen/

