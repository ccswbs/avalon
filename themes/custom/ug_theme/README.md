## Requirements
* Node.js 4.2.1
* Bower [npm install -g bower](http://bower.io/#install-bower)
* Gulp `npm install gulp -g` (installs globally)

## Get Started
### Most of the packages should be downloaded with a git pull from pantheon if not follow steps below
* `bower install` to install packages under assets/bower_components
* Install dependencies using `npm install`
* run `gulp` to start the file watcher

## Browser auto refresh using [browsersync](https://browsersync.io) and [drupal module](https://www.drupal.org/project/browsersync)
* make sure that your settings.local.php is setup correctly and caching is disabled
* install/enable and configure drupal [browser-sync](https://www.drupal.org/project/browsersync) module
* Have fun!

## Good to have on
* Twig theme debug by enabling local development services(development.services.yml)

## Sass structure
* The sass follows the 7-1 architecture pattern in order to keep the project clean with so many people working on it. ([Read more about 7-1 here](https://sass-guidelin.es/#the-7-1-pattern))
