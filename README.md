#WikiBlog

the required documentation is in the document and PDF files.

##How to setup

 * First u need an 'apache server' with 'php' and 'mysql' authentication modules.
 * Copy the CODE directory contents into your localhost root directory or any of its sub-directories.
 * Now setup the database:
		* Open mysql command line.
		* use the create_table.sql dump to create database and required tables.
		* Then for some sample data, use the table_data.sql dump to insert sample data into the tables.
 * Edit the CODE/includes/constants.php according to your mysql authentication parameters.