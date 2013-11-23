#!/bin/bash

#	this will automatically create the database for the wikiblog
#	and change the constants in the includes/constants.php

#take input for hostname
echo -e -n "MySQL hostname [empty string for default 'localhost']: "
read mysqlhostname
if [[ -z $mysqlhostname ]]; then
	mysqlhostname="localhost"
fi

#take input for username
echo -e -n "MySQL username: "
read username

#take input for hostname
echo -e -n "MySQL password: "
read -s password
if [[ -z $password ]]; then
	CMD1="mysql --host=$mysqlhostname --user=$username"	
else
	CMD1="mysql --host=$mysqlhostname --user=$username --password=$password"
fi

#echoing the inputs
#echo -e "\n$mysqlhostname $username $password"

#now using the dumps, create the database
echo 'Creating the database...'

echo 'creating tables...'
CMD2="database/tables.sql"
$CMD1 < $CMD2
echo 'Done.'

#add the sample data
echo 'inserting sample data...'
CMD2="database/sample_data.sql"
$CMD1 < $CMD2
echo 'Done.'

#changing the user permissions for userdata for file successful file uploads
echo 'Configuing user-permissions...'
sudo chown -R www-data:www-data userdata
sudo chmod -R 755 userdata
echo 'Done.'

#replace the contents of includes/constants.php
echo 'Configring Database constants...'
CMD1="includes/constants.php"
CMD2="includes/constants.php.new"
sed "s/\$USER = \"\"/\$USER = \"$username\"/" $CMD1 > $CMD2
mv $CMD2 $CMD1

sed "s/\$HOST = \"\"/\$HOST = \"$mysqlhostname\"/" $CMD1 > $CMD2
mv $CMD2 $CMD1

sed "s/\$PASS = \"\"/\$PASS = \"$password\"/" $CMD1 > $CMD2
mv $CMD2 $CMD1

#echo
echo 'Done.'