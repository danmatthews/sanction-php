---
title: Installing and using Sanction
layout: default
---

Installation is done via [Composer](http://getcomposer.org), simply add this line to your `composer.json` file (PS - versioned release coming soon!):

```
require: {
    "curlymoustache/sanction" : "dev-master"
}
```

Then run `$ composer update` or `$ composer install` on the command line (if you have composer installed).

### Don't have composer installed?

You can download a copy of composer to your working directory by typing:

```
$ curl -S http://getcomposer.org/installer | php
```

Then you can run composer commands by using:

```
$ php composer.phar <command>
```

If you want to make the copy of composer 'global', then you can move the file to somewhere in your `$PATH`, like `/usr/local/bin`:

```
$ sudo mv composer.phar /usr/local/bin/composer
```
