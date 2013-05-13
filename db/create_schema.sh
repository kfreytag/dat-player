#!/bin/bash

SCHEMA_PATH=`pwd`

DB_PATH=/dat/local/db
DB_NAME=player.db

cd ${DB_PATH}
sqlite3 ${DB_NAME} < ${SCHEMA_PATH}/tables.sql