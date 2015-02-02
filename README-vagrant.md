# Vagrant for Puzzletron development

This project now features a Vagrantfile and an Ansible configuration
that can automatically set you up with a virtual machine for hacking
on Puzzletron.

Out of the box, you may need an SSH key on your local machine at
`~/.ssh/id_rsa.pub` to make this work. You can generate one using
`ssh-keygen`.

- Install [Virtualbox](https://www.virtualbox.org).

- Install [Vagrant](http://www.vagrantup.com).

- Install
  [Ansible](http://docs.ansible.com/intro_installation.html#installing-the-control-machine). e.g. on
  the Mac with Homebrew: `brew update`, `brew install ansible`.

- From the directory with the Vagrantfile, run `vagrant up`.

- Use `vagrant ssh` to connect to the box, or add its hostname to
  `/etc/hosts` and try `ssh ubuntu@puzzletron.vm`.

