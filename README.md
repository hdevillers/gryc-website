![Gryc](https://github.com/mpiot/gryc/blob/master/assets/images/logo-40.png)

### Summary
1. Install the development app
2. Follow the best practice
3. How to control your code syntax ?
4. About docker images

## 1. Install the development app

### 1. Install Docker and docker-compose
The development app use docker and docker-compose, before continue to follow the guide, please install these requirements.
* https://docs.docker.com/install/
* https://docs.docker.com/compose/install/

### 2. Fork the app
1. Fork

    Click on the fork button at the top of the page.

2. Clone your repository (after fork)

        git clone git@github.com:USERNAME/gryc-website.git

3. Create the upstream remote

        cd gryc-website
        git remote add upstream git://github.com/gryc-inra/gryc-website.git

4. Some infos, to work with upstream and origin remote

    https://symfony.com/doc/current/contributing/code/patches.html

### 3. Configure the app

Now, we will configure the application on your machine, there is 2 files that permit it:
 - .env: configure credential for db, Google ReCaptcha, SMTP credentials, ...
 - docker-compose.override.ym: configure daemon access like the forwarded ports of nginx to access your app, and db ports
 for debug.


    cp .env.dist .env
    vi .env

    cp docker-compose.override.yml.dist docker-compose.override.yml
    vi docker-compose.override.yml

### 4. Edit the host configuration

Several system variables have to be modified to run properly some of the services.
To do so, add the following lines in `/etc/sysctl.conf` file:

```bash
# vm max for elasticsearch
vm.max_map_count=262144
# vm overcommit for redis
vm.overcommit_memory=1
```

In addition, to avoir warnings and latencies with `redis`, add in the host
`/etc.rc.local` file the following lines:

```bash
# THP (transparent huge page) issue with Redis
echo never > /sys/kernel/mm/transparent_hugepage/enabled
echo never > /sys/kernel/mm/transparent_hugepage/defrag
```

__Note__: On current Debian systems, `rc-local.service` is disabled and should be
started/enabled.

### 5. Install

That's finish in a few time, now, just execute:

    make install

And voilà !!! Your app is installed and ready to use.


### 6. ElasticSearch license

To get and/or update your free ElasticSearch license, you should visite this
[page](https://medium.com/@ospaarmann/tidbits-solving-the-elasticsearch-x-pack-license-issue-in-docker-d15bb22d82fd).

## 2. Follow the best practice
There is a **beautiful** guide about the best practice :) You can find it on the [Symfony Documentation - Best Practice](http://symfony.com/doc/current/best_practices/index.html).

## 3. How to control your code syntax ?
For a better structure of the code, we use Coding standards: PSR-0, PSR-1, PSR-2 and PSR-4.
You can found some informations on [the synfony documentation page](http://symfony.com/doc/current/contributing/code/standards.html).

In the project you have a php-cs-fixer.phar file, [the program's documentation](http://cs.sensiolabs.org/).

Some commands:
   * List files with mistakes

    make php-cs

   * Fix files:

    make php-cs-fix

## 3. About docker images

In the project, docker images are automatically generated. There is a *.travis-ci.yml* file, that execute some test on each
PullRequest to validate the code. When the code is merged in the **master branch** of the project, then it save a new **dev** image
and a **dev-{docker-folder-hash}**. If we add a tag on a commit, then Travis-Ci generate the **prod** image and a **prod-{TAG}** image.

The **dev** and **dev-{docker-folder-hash}** are the same image, **dev** is the latest dev image. And in prod it's the
same **prod** and **prod-{TAG}** are identical. The **prod** image are always the latest prod image.

All this images are pushed on the docker hub repository:  https://hub.docker.com/r/mapiot/gryc/
