# jamp
Easy to use PHP scripts for cross platform scripting.

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
the current directory and its subdirectories into it.

