#!/bin/bash

./propel-gen app/Config reverse

php nsgen.php

./propel-gen app/Config main