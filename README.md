# Moodle Page skin tool plugin by Marcus Green

Page skins, slightly like a mini theme.
Site admins can create skins containing javascript and css
and which page types they can apply to. The pagetype is taken from
the global $PAGE variable.

Techers can cause the skin to be applied by adding a tag in the settings,e.g
in the tags for a quiz.


## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/admin/tool/skin

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.
