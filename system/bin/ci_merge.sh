#!/bin/sh

# Does the complex merging needed to integrate updates from CodeIgniter 
# to CoolBrew. It takes into account the places where I moved files or
# renamed them. It may require updates dependng on how extreme the 
# CodeIgniter updates are from version to version.

if [ $# -eq 0 ]
then
        echo "usage:  ci_merge.sh <old_ci_version> <new_ci_version>"
        echo ""
        exit 1
fi

if [ $1 = 'undo' ]
then
        echo "undoing merge..."
        cd ~/Desktop/websites/system
        svn revert . -R
        echo ""
        exit 1
fi

if [ $# -gt 2 ]
then
        echo "Error: too many parameters."
        echo ""
        exit 1
fi

if [ $# -eq 2 ]
then
   old=$1
   new=$2
fi

ci_old='svn+ssh://bolwebdev1/opt/svn-repos/vendorsrc/ellislab/codeigniter/'$old'/system/'
ci_new='svn+ssh://bolwebdev1/opt/svn-repos/vendorsrc/ellislab/codeigniter/'$new'/system/'

cd ~/Desktop/websites/system

# These files were moved from codeigniter/ to coolbrew/
svn merge -x --ignore-eol-style $ci_old'codeigniter/Base4.php' $ci_new'codeigniter/Base4.php' coolbrew/Base4.php
svn merge -x --ignore-eol-style $ci_old'codeigniter/Base5.php' $ci_new'codeigniter/Base5.php' coolbrew/Base5.php
svn merge -x --ignore-eol-style $ci_old'codeigniter/Common.php' $ci_new'codeigniter/Common.php' coolbrew/Common.php
svn diff -x --ignore-eol-style $ci_old'codeigniter/CodeIgniter.php' $ci_new'codeigniter/CodeIgniter.php' > coolbrew/CodeIgniter.patch

# These folders should all have the correct contents
svn merge -x --ignore-eol-style $ci_old'database' $ci_new'database' database
svn merge -x --ignore-eol-style $ci_old'fonts' $ci_new'fonts' errors
svn merge -x --ignore-eol-style $ci_old'helpers' $ci_new'helpers' helpers
svn merge -x --ignore-eol-style $ci_old'libraries' $ci_new'libraries' libraries
svn merge -x --ignore-eol-style $ci_old'plugins' $ci_new'plugins' plugins

# The way languages are defined was changed
svn merge -x --ignore-eol-style $ci_old'language/english' $ci_new'language/english' language/en_US

# These folders should be updated from the application/ folder
svn merge -x --ignore-eol-style $ci_old'application/errors' $ci_new'application/errors' errors

# The scaffolding feature is depricated in CodeIgniter, but check for updates
svn diff -x --ignore-eol-style $ci_old'scaffolding' $ci_new'scaffolding' > scaffolding.patch


# Remind the user about manual updates.
echo "--------------------------------------------"
echo " You will need to make some manual updates:"
echo ""
echo " 1) See changes in coolbrew/CodeIgniter.patch
echo "    and apply them to coolbrew/CoolBrew.php
echo ""
echo " 2) Check for changes in the main index.php"
echo "    file and apply them to coolbrew.inc.php"
echo ""
echo " 3) Read the Upgrade instructions in the "
echo "    CodeIgniter User Guide for additional "
echo "    manual updates."
echo ""
echo "--------------------------------------------"
echo ""

exit 0