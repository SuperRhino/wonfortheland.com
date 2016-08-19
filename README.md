# WonForTheLand.com

## Environment Setup

**You must have PHP installed locally.**

1. Run the setup script: `bash ./setup.sh`

## Run Development Site

1. Start Dev server: `npm run start`
2. Visit [http://localhost:8080](http://localhost:8080), you should see something

## Run Migrations

1. Run migrations: `vendor/bin/phinx migrate`

## Gulp Tasks

**You must have [Browserify](http://browserify.org/) installed: `npm install -g browserify`**

* Build App: `gulp [--production]`
* Build App and Watch: `gulp watch`
* More: `gulp --tasks`