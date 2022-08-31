
var newwindow = '';

// ------------------------------------------------------------------------

// opens a new window centered on the screen, WITH menu bar, navigation, etc
function centeredFullWindow(url, myname, w, h, scroll)
{
   var winl = (screen.width - w) / 2;
   var wint = (screen.height - h) / 2;

   winprops = 'height=' + h + ',width=' + w + ',top=' + wint + ',left=' + winl + ',scrollbars=yes,resizable=yes,toolbar=yes,menubar=yes,location=yes toolbar=yes'

   if ( ! newwindow.closed && newwindow.location)
   {
      newwindow.location.href = url;
   }
   else
   {
      newwindow = window.open(url, myname, winprops);
      if ( ! newwindow.opener) newwindow.opener = self;
   }
   if (window.focus)
   {
      newwindow.focus()
   }
   return false;
}

