# My PHP Boilerplate

This is a custom framework for creating PHP sites that I decided create to
learn how to accomplish common tasks like routing, ORMs, server-side rendering, etc...

This is still a work in progress and is not intended to be used for a real web
site anytime soon.

## Setting Up This Project

 1. Clone this repo `git clone https://github.com/JoeBobMiles/WebBoilerplate.git`.
 2. Install Vagrant and a provisioner like VirtualBox or VMWare.
 3. Run `vagrant up` to spin up the development server and provision it with
    what it needs to properly run the site.

The Vagrant server can be accessed at `http://localhost:8080`.

## Running The Tests

**Prerequisites:**

 1. GNU Make

Use the command `make test` to run the tests.
