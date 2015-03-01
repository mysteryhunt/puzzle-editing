# The secrets.yml Configuration File

We store the configuration for Puzzletron in a `secrets.yml`
file. It's got passwords, SSH keys, SSL certificates, et cetera.

The rules are:

* Never, ever, ever check this file into version control. The `.gitignore` file
  in this directory should prevent this; don't work around that.

* In general, the secure way to manage a file like `secrets.yml` is to put it
  on an encrypted USB stick. On the Mac, a tool like
  [Knox](https://agilebits.com/knox) can be helpful for managing this,
  but you can also just build encrypted volumes out of the box. Then
  you put a symlink to the file in this directory:

```shell
    ln -s /Volumes/mysecuredrive/puzzletron-secrets.yml secrets.yml
```

The symlink won't work unless your USB key is inserted into your
computer and decrypted -- that's a feature, although you will have to
learn to recognize the cryptic error message that results and remember
to insert your key when you see that message.

Of course, we aren't running any Fortune 500 corporations based on
Puzzletron, so this kind of security might be overkill. But I do it
out of habit. It's a nice habit to have.


