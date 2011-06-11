#!/bin/bash
date="date -I"
mysqldump --all-databases -u root -ppassword | gzip -c | cat > /pos/archives/dbackup`$date`.sql.gz

