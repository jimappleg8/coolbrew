function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<ul>' +
		'<li><a href="'+base+'index.html">User Guide Home</a></li>' +	
		'<li><a href="'+base+'toc.html">Table of Contents Page</a></li>' +
		'</ul>' +	

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/requirements.html">Server Requirements</a></li>' +
			'<li><a href="'+base+'ci-license.html">CodeIgniter License Agreement</a></li>' +
			'<li><a href="'+base+'cb-license.html">CoolBrew License Agreement</a></li>' +
			'<li><a href="'+base+'changelog.html">Change Log</a></li>' +
			'<li><a href="'+base+'general/credits.html">Credits</a></li>' +
		'</ul>' +	
		
		'<h3>Installation</h3>' +
		'<ul>' +
			'<li><a href="'+base+'installation/downloads.html">Downloading CoolBrew</a></li>' +
			'<li><a href="'+base+'installation/install-guide.html">Installation Guide</a></li>' +
			'<li><a href="'+base+'installation/troubleshooting.html">Troubleshooting</a></li>' +
		'</ul>' +
		
		'<h3>Introduction</h3>' +
		'<ul>' +
			'<li><a href="'+base+'overview/explained.html">CoolBrew Explained</a></li>' +
			'<li><a href="'+base+'overview/features.html">Features</a></li>' +
			'<li><a href="'+base+'overview/tag-architecture.html">Tag-Based Architecture</a></li>' +
			'<li><a href="'+base+'overview/multiple-websites.html">Multiple Websites</a></li>' +
			'<li><a href="'+base+'overview/differences.html">Differences from CodeIgniter</a></li>' +
		'</ul>' +	
				
		'</td><td class="td_sep" valign="top">' +

		'<h3>General Topics</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/getting-started.html">Getting Started</a></li>' +
			'<li><a href="'+base+'general/urls.html">CoolBrew URLs</a></li>' +
			'<li><a href="'+base+'general/tags.html">Tags</a></li>' +
			'<li><a href="'+base+'general/modules.html">Modules</a></li>' +
			'<li><a href="'+base+'general/controllers.html">Controllers</a></li>' +
			'<li><a href="'+base+'general/local-controllers.html">Local Modules &amp; Controllers</a></li>' +
			'<li><a href="'+base+'general/views.html">Views</a></li>' +
			'<li><a href="'+base+'general/creating-libraries.html">Creating Your Own Libraries</a></li>' +
			'<li><a href="'+base+'general/autoloader.html">Auto-loading Resources</a></li>' +
			'<li><a href="'+base+'general/errors.html">Error Handling</a></li>' +
			'<li><a href="'+base+'general/emulating-ci.html">Emulating CodeIgniter</a></li>' +
			'<li><a href="'+base+'general/ci-applications.html">Using CodeIgniter Applications</a></li>' +
		'</ul>' +
		
		'</td><td class="td_sep" valign="top">' +

				
		'<h3>Class Reference</h3>' +
		'<ul>' +
		'<li><a href="'+base+'libraries/benchmark.html">Benchmarking Class</a></li>' +
		'<li><a href="'+base+'libraries/collector.html">Collector Class</a></li>' +
		'<li><a href="'+base+'libraries/config.html">Config Class</a></li>' +
		'<li><a href="'+base+'libraries/database.html">Database Class</a></li>' +
		'<li><a href="'+base+'libraries/language.html">Language Class</a></li>' +
		'<li><a href="'+base+'libraries/db-session.html">Session (DB) Class</a></li>' +
		'<li><a href="'+base+'libraries/tag.html">Tag Class</a></li>' +
		'<li><a href="'+base+'libraries/validation.html">Validation Class</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +

		'<h3>Core Modules</h3>' +
		'<ul>' +
		'<li><a href="'+base+'modules/calendar.html">Calendaring Module</a></li>' +
		'<li><a href="'+base+'modules/collect.html">Collector Module</a></li>' +
		'<li><a href="'+base+'modules/config.html">Config Module</a></li>' +
		'<li><a href="'+base+'modules/input.html">Input Module</a></li>' +
		'<li><a href="'+base+'modules/language.html">Language Module</a></li>' +
		'<li><a href="'+base+'modules/sessions.html">Session Module</a></li>' +
		'<li><a href="'+base+'modules/uri.html">URI Module</a></li>' +
		'<li><a href="'+base+'modules/user-agent.html">User Agent Module</a></li>' +
		'<li><a href="'+base+'modules/view.html">View Module</a></li>' +
		'</ul>' +	


		'<h3>Additional Resources</h3>' +
		'<ul>' +
		'<li><a href="https://github.com/jimappleg8/coolbrew/forum/">CoolBrew Forums</a></li>' +
		'<li><a href="http://www.codeigniter.com/">CodeIgniter Website</a></li>' +
		'</ul>' +	
		
		'</td></tr></table>');
}