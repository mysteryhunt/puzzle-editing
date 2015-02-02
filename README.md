# puzzletron

Software for editing/testing puzzles for the
[MIT Mystery Hunt](http://www.mit.edu/~puzzle/)

Currently being hacked by Team Luck in preparation for the 2016
Hunt. Many thanks to our predecessors Random Fish (2015) and Alice
Shrugged (2014).

## To initialize mysql database:

* Log into your mysql database server with full administrative
  priviliges.
* Create a puzzletron user
* Create a puzzletron database
* Grant the puzzletron user access to that database

        mysql -u <mysqlusername> -s <servername> -p <databasename> < schema.sql

  (enter password for the puzzletron DB user when prompted)

## Setup configuration:

* Copy `config.php.EXAMPLE` to `config.php` and edit appropriately.

* `DB_NAME` is the name of the database you created above

* `URL` is the URL what links referring back to this puzzletron
  instance should be

* `TRUST_REMOTE_USER` disables the internal puzzletron user database
  and trusts the apache `REMOTE_USER` variable (only do this if you
  have apache auth set up and a separate authentication database)
  (warning: Random Fish doesn't have this turned on and doesn't know
  if our changes have broken Puzzletron if it does get turned on)

* `DEVMODE` --- is the server in Development/Test mode?

* `PRACMODE` --- is the server in Practice / pre-hunt-writing mode?

* Copy `secret.php.EXAMPLE` to `secret.php` and edit appropriately

## Customize database stuff:

* `priv` table contains list of roles and what privileges they have

* `pstatus` table contains list of possible puzzle statuses and what
  can happen at each status

## To get email notifications working:

In order for puzzletron to actually send its email queue (comments on
puzzles, etc.)  there needs to be a cron job that runs
`email_cronjob.php` script with your php interpreter at some regular
frequency.

## File Permissions:

Make sure the uploads directory (and everything underneath it) is
writable and searchable by your web server

Last person to touch this code (2014 mystery hunt) if you need help:
benoc@alum.mit.edu
