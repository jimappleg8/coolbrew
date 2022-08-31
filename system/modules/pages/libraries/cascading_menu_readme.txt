cascading_menu.class
Scott Mattocks
mttcks@hotmail.com

This class will create a horizontal cascading menu in HTML.  The
class also generates the needed javascript code for image rollovers
and hiding of the sub-menus.  The main menu is positioned statically
while the sub-menus are all positioned absolutely.

A cascading menu consists of the main menu and several choices.  
Each choice may have sub-choices which will appear either below 
or to the right of the parent-choice.  A menu may have as many choices 
as desired.  The width of each choice (and sub-choice) will be the 
width of the menu divided by the number of choices.  Each choice
(and sub-choice) can have as many children as desired.  The main
menu and each choice object can be given its own css class to control
the style and appearence.  

How to use cascading_menu.class:
1  Create a cascading_menu object...
   You need: the top and left coordinates (main menu is static)
             the height you want the menu to be
             the total width menu (should be divisible by # choices)
             the name of the css class for the main menu
   $menu = new cascading_menu($top, $left, $height, $width, $css);

2  Create some top level choices
   These are the choices that will always be visible
   You need: the inactive (up) image
             the active (down) image
             the URL to link to
             the alternate text for the image
             the css class for the choice
   $choice_N = new choice($up, $down, $url, $alt, $css);

3  Create some sub-level choices
   These choices will only appear when the mouse is over 
   their respective parent
   You need: same as #2
   $sub_N = new choice($up, $down, $url, $alt, $css);

4  Add the choices to the menu and to parent choices
   You must add choices from the parent down!
   You can create as many levels as you like.
   Good:
   $menu->add($choice_1);
   $choice_1->add($sub_1);
   Bad:
   $choice_1->add($sub_1);
   $menu->add($choice_1);   // Sub-choice will not show up!!

5  Write the menu to the page
   This will output script tags with the javascript for the 
   rollovers and hiding of the menus as well as the menus
   themselves
   echo $menu->write();

Please send any questions, comments, or suggestions to
mttcks@hotmail.com with cascading_menu in the subject.  Any 
hints for optimizing and cleaning up this class are gladly
welcomed.  I am also willing to help with any projects.
(especially if paid :)