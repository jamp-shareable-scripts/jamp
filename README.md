# jamp - Shareable Scripts
Easy to use and easy to share PHP scripts for cross platform scripting.

## Setup
### Prerequisites
* [PHP](https://www.php.net/) is installed and available in the PATH.

### Installation

1. Clone the project from https://github.com/jamp-shareable-scripts/jamp
1. Add the appropriate jamp bin path to your PATH: `jamp/bin/(your os)`
1. Open a new cli and test with `jamp show` to see available commands

## Purpose
This project aims to make it very easy to create, edit, share and retrieve PHP
scripts. It should come in handy for web developers and system administrators
who already have PHP installed on their system and work across multiple
environments and want to run the same scripts in these various environments. For
example, develop on a Mac or Windows computer and run a Linux server. It also
comes in handy for small teams of people who are all using different devices and
want to be able to share scripts among one another as well. PHP provides a
common interface to run the same scripts in all these different environments.

Since PHP is bundled with many extensions that accomplish all manner of things,
such as working with the filesystem, processing images and compressing and
decompressing files, among many other possibilities, it is possible to
accomplish many tasks without requiring the installation of additional software,
possibly from untrusted or unknown sources as may be the case in other scripting
languages.

## Examples
### Core functions
The first two examples, the `create` and `edit` commands, make it next to
frictionless to create, edit and run PHP scripts. It's much more efficient than
manually opening new files, using absolute paths to run scripts, trying to work
with PHP's interactive mode or manually making some PHP scripts auto run in your
environment. It's all handled by jamp, making the actual script itself the focus
of the work.

`jamp create hello`

Creates a new PHP script, `hello.php`, and opens it for editing in an editor.

`jamp hello` is now available as a command as well and it will run whatever
script you save in the `hello.php` file.

`jamp edit hello`

Continuing the previous example, this would open the `hello.php` script file in
a text editor for editing.

### Other functions
Beyond the core functions, scripts useful for scripting have been written as
well. Here is a collection of them. They will be automatically installed (after
asking for permission, of course!) if they are called while they are not
installed.

`jamp jitsi`

Generate a random URL for https://meet.jit.si/<random part goes here> so it is
quick and easy to setup a new video conference room with a hard-to-guess URL.

`jamp zip --password backup.zip`

This command will create a zip archive called backup.zip and add all items in
the current directory and its subdirectories into it. It will also request a
password from the user and use this password to encrypt all the files as they
are added to the zip archive.
