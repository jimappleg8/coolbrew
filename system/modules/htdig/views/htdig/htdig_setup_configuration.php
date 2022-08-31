<?php
/*
 * htdig_setup_configuration.php
 *
 * Purpose: create a configuration file for use by Htdig programs.
 *
 * Run this script from the command line use PHP standalone CGI
 * executable program.
 *
 * @(#) $Header: /home/mlemos/cvsroot/PHPlibrary/htdig_setup_configuration.php,v 1.4 2004/02/11 22:43:49 mlemos Exp $
 *
 */

	require("htdig.php");

	$htdig=new htdig_class;

	/*
	 * Where are the executables of htsearch, htdig, htmerge, htfuzzy
	 * located? They should be in the same directory. It does not need
	 * to be in the original instalation directory.
	 */
	$htdig->htdig_path="/usr/local/htdig/bin";

	/*
	 * Where this search engine configuration file should be stored? It
	 * does not need to be in the original htdig instalation directory.
	 * If you need to index more than one site in your server run this
	 * script as many times as need specifying different configuration file
	 * names.
	 */
	$htdig->configuration="/usr/local/htdig/conf/htdig.conf";

	/*
	 * Where this search engine database files hould be stored? It
	 * does not need to be in the original htdig instalation directory.
	 * If you need to index more than one site in your server run this
	 * script as many times as need specifying different database
	 * directories.
	 */
	$htdig->database_directory="/usr/local/htdig/db";

	/*
	 * Additional options that should be added to the configuration file.
	 * Consult htdig manual to learn about all of them.
	 */
	$options=array(

		/*
		 * List of one or more URLs that htdig should start digging. It
		 * will follow the links contained in these URL pages.
		 */
		"start_url"=>"http://en.static.phpclasses.org/reviews/",

		/*
		 * List of one or more URLs that htdig should restrict when
		 * following links.
		 */
		"limit_urls"=>"http://en.static.phpclasses.org/",

		/*
		 * List of search algoritms to use and the associated weights that will
		 * be used to compute the score of each match.
		 */
		"search_algorithm"=>"exact:1 endings:0.5",

		/*
		 * List of patterns that is used to exclude URLs from being indexed.
		 */
		"exclude_urls"=>"? browse/ user_options.html search.html",

		/*
		 * Where the special template files htdig_header.html
		 * htdig_nomatch.html htdig_syntaxerror.html htdig_template.html are
		 * located.  These are special template files used by the htdig_class
		 * to parse htsearch program results. Do not change the template files.
		 * Install them to the path specified by this option.
		 */
		"template_path"=>"/usr/local/htdig/common"

	);

	/*
	 * Generate and save the configuration file in path specified in
	 * $htdig->configuration variable.
	 */
	$error=$htdig->GenerateConfiguration($options);
	if(strcmp($error,""))
		echo "Error: $error\n";
?>