# The Secrets Directory

This is where we put YML files containing secret configuration
information for the Puzzletron website -- passwords, SSH keys, SSL
certificates, et cetera.

The rules are:

* Don't check these files into version control. The `.gitignore` file
  in this directory will help with this.

* In general, the secure way to manage files like these is to put them
  on an encrypted USB stick -- on the Mac, a tool like
  [Knox](https://agilebits.com/knox) can be helpful for managing this,
  but you can also just build encrypted volumes out of the box. Then
  you put symlinks to the files in this directory:

```shell
    ln -s /Volumes/mysecuredrive/secrets/puzzletron-dev.yml puzzletron-dev.yml
```

The symlinks won't work unless your USB key is inserted into your
computer and decrypted, but that's a feature.

Of course, we aren't running any Fortune 500 corporations based on
Puzzletron, so this kind of security might be overkill. But I do it
out of habit. It's a nice habit to have.

There can be up to three files here:

* `puzzletron-dev.yml` for the dev site
* `puzzletron-staging.yml` for the staging site
* `puzzletron-prod.yml` for the production site

