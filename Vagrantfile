# -*- mode: ruby -*-
# vi: set ft=ruby :

# This is a Vagrantfile, for configuring a local virtual machine
# that you can use to hack on Puzzletron from your laptop without
# installing PHP. See http://www.vagrantup.com

# Vagrantfile API/syntax version. Don't touch unless you know what
# you're doing!
VAGRANTFILE_API_VERSION ||= "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.define 'puzzletron.vm' do |app|
    app.vm.host_name = 'puzzletron.vm'
    app.vm.box = "ubuntu/trusty64"

    # Port 22 gets forwarded by default, but we redeclare it here to
    # ensure that auto_correct is on.
    app.vm.network :forwarded_port, guest: 22, host: 2222, id: 'ssh', auto_correct: true

    # I like using private networks at fixed IP addresses on my laptop.
    # Throw a line like this in your /etc/hosts:
    #
    #   192.168.33.31  puzzletron.vm
    #
    # ...and you can visit your development site at http://puzzletron.vm
    #
    # Also private networks let you use NFS for sharing files between this
    # directory and /vagrant inside the VM.
    app.vm.network :private_network, ip: "192.168.33.31"

    # For now we just use the simple approach
    app.vm.synced_folder '.', '/vagrant'
    # Alternatively, with a private network you can use NFS like this
    # if you have performance problems:
    #  app.vm.synced_folder '.', '/vagrant', nfs:true


    # The following block adjusts cores and memory, courtesy of
    # http://www.stefanwrobel.com/how-to-make-vagrant-performance-not-suck
    app.vm.provider "virtualbox" do |v|
      host = RbConfig::CONFIG['host_os']

      # Give VM 1/4 system memory, up to 2GB, & access to all cpu cores on the host
      if host =~ /darwin/
        cpus = `sysctl -n hw.ncpu`.to_i
        # sysctl returns Bytes and we need to convert to MB
        mem = `sysctl -n hw.memsize`.to_i / 1024 / 1024 / 4
      elsif host =~ /linux/
        cpus = `nproc`.to_i
        # meminfo shows KB and we need to convert to MB
        mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i / 1024 / 4
      else # sorry Windows folks, I can't help you
        cpus = 2
        mem = 1024
      end
      mem = 2048 if mem > 2048
      v.customize ["modifyvm", :id, "--memory", mem]
      v.customize ["modifyvm", :id, "--cpus", cpus]
    end

    # Use Ansible to install stuff on our VM.
    # We do this in two stages: stage one sets up the `ubuntu` user, edits
    # your local known_hosts file, and does other useful preliminaries.
    # For this purpose, host_key_checking is off.
    app.vm.provision "ansible" do |ansible|
      ansible.playbook = "ansible/initialize-vagrant.yml"
      ansible.host_key_checking = false
      ansible.extra_vars = { sudoer_ssh_key_local_paths: [ "~/.ssh/id_rsa.pub" ] }
    end
    # Stage two does all the real work.
    app.vm.provision "ansible" do |ansible|
      ansible.playbook = "ansible/configure.yml"
      ansible.inventory_path = 'ansible/development'
      ansible.extra_vars = { upgrade_packages: "true", deploy_name: "dev" }
    end
  end
end
