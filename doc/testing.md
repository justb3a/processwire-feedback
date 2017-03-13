# Testing

Using **[behat](http://behat.org/)**: A php framework for autotesting your business expectations.

First you've to install the required composer packages:

```
composer install
```

Then copy `behat.yml.example` and rename it to `behat.yml`.  
Now edit the copied file and replace everything below `AuthenticationContext`:

1. the baseurl containing `http[s]`
2. valid apiKey
3. valid apiSecret
4. json including all needed parameters including valid values

If you're using **Basic HTTP Authentication** use test suite **basichttp**, otherwise use **hashed**.

Now you should be able to execute the following command:

```
$ vendor/bin/behat --suite=basichttp
$ vendor/bin/behat --suite=hashed
```

You can also run behat scenarios using tag filters:

```
$ vendor/bin/behat --suite=hashed --tags @send
$ vendor/bin/behat --suite=hashed --tags @receive
$ vendor/bin/behat --suite=hashed --tags "@send&&@success"
$ vendor/bin/behat --suite=hashed --tags "@send,@success"
$ vendor/bin/behat --suite=hashed --tags "~@success"
```

As you see tag filters supports different logical operators:

1. **AND**: separation by `&&`
2. **OR**: separation by `comma`
3. **NOT**: prefixing a `~`

Or for example you can run a scenario by using a part of the name:

```
$ vendor/bin/behat --suite=hashed --name 'Send feedback'
$ vendor/bin/behat --suite=hashed --name 'Unsuccessfull api request'
```
