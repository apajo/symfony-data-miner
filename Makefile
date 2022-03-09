.PHONY: jwt-keys assets tests kill theme translations install update clean dev webr webr-w setup db-dump db-update classifier

arg = "$(filter-out $@,$(MAKECMDGOALS))"
jwtpass = $(shell cat .env | grep "^JWT_PASSPHRASE=" | cut -d '=' -f 2-)
git_branch = $(shell git rev-parse --symbolic-full-name --abbrev-ref HEAD)

RED='\033[0;31m'
NC='\033[0m' # No Color

%:
	@:

help:           ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

tests:
	php tests/$(arg).php
