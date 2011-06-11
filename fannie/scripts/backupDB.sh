#!/bin/bash
date="date -I"
mysqldump --all-databases -u root -pccm | gzip -c | cat > /pos/archives/dbackup`$date`.sql.gz

