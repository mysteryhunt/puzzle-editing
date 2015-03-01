# Puzzletron

Software for editing/testing puzzles for the
[MIT Mystery Hunt](http://www.mit.edu/~puzzle/)

Currently being hacked by Team Luck in preparation for the 2016
Hunt. Many thanks to our predecessors Random Fish (2015), Alice
Shrugged (2014), and the long line of programmers who came before
them.

# Developing Puzzletron

You've got a couple of options.

## Virtual Machine (recommended for Macs and Linux)

This project is configured to set itself up in a virtual machine on
your computer. I've tested this on the Mac. Alas, the setup code uses
Ansible which does not work on Windows right now.

- If you have not done so already, use `ssh-keygen` to create your
personal SSH key and store it at `~/.ssh/id_rsa.pub` -- or, if you
store it elsewhere, edit the Vagrantfile to point to it.

- Install [Virtualbox](https://www.virtualbox.org).

- Install [Vagrant](http://www.vagrantup.com).

- Install
  [Ansible](http://docs.ansible.com/intro_installation.html#installing-the-control-machine). e.g. on
  the Mac with Homebrew: `brew update`, `brew install ansible`.

- Copy `ansible/secrets.yml.example` to `ansible/secrets.yml`. Then,
if you like, edit the configuration settings in there. Read
`ansible/README-secrets.md` for more instructions.

- From the directory with the Vagrantfile, run `vagrant up`.

- Use `vagrant ssh` to connect to the box, or add this line to your `/etc/hosts`:

```
192.168.33.31  puzzletron.vm
```

...and try `ssh ubuntu@puzzletron.vm`.

### Mail

The Vagrant box doesn't include a mail server because they're a pain,
and because transactional email services are free at small
volumes. Set up a Mailgun account and point Puzzletron at it using the
`MAILGUN` configuration settings in `ansible/secrets.yml`.


## On your local machine using a LAMP stack

If you're willing to contemplate running PHP, MySQL, and Apache on
your local machine you can use this code directly. You can use
projects like MAMP to help set up your computer with these things.

- Initialize the MySQL database:

    - Log into your mysql database server with full administrative
      priviliges.
    - Create a puzzletron user
    - Create a puzzletron database
    - Grant the puzzletron user access to that database

          mysql -u <mysqlusername> -s <servername> -p <databasename> < schema.sql

      (enter password for the puzzletron DB user when prompted)

- Copy `dotenv.example` to `.env` and edit
  appropriately. `PTRON_DB_NAME`, `PTRON_DB_USER`, and
  `PTRON_DB_PASSWORD` are the name, user, and password of the database
  you created above. `PTRON_URL` is the URL that will appear in links
  which point back at your app.

### File Permissions:

Make sure that both the `uploads` directory (and everything underneath
it) and the `tmp` directory (and everything underneath it) are
writable and searchable by your web server.


### To get email notifications working:

In order for puzzletron to actually send its email queue (comments on
puzzles, etc.) there needs to be a cron job that runs
`email_cronjob.php` script with your php interpreter at some regular
frequency. The Vagrant box and the deployment scripts set this up
automatically, but you might have to set it up by hand.


# How does Puzzletron work?

## Customize database stuff:

* `priv` table contains list of roles and what privileges they have

* `pstatus` table contains list of possible puzzle statuses and what
  can happen at each status


The last person to touch this code (2016 mystery hunt) if you need
help: mike@mechanicalfish.net
