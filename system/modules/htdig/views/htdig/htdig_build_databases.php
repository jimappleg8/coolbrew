<?php
/*
 * htdig_build_databases.php
 *
 * Purpose: build the databases that contain the information about the
 * indexed documents.
 *
 * Run this script from the command line use PHP standalone CGI 
 * executable program. Execute this script after executing
 * htdig_setup_configuration.php .
 *
 * @(#) $Header: /home/mlemos/cvsroot/PHPlibrary/htdig_build_databases.php,v 1.2 2002/10/17 11:22:53 mlemos Exp $
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
	 * Make Htdig programs traverse the pages specified in the configuration
	 * file and generate the databases with information about them.
	 */
	$error=$htdig->Dig("endings",$log);
	if(strcmp($error,""))
	{
		echo implode("\n",$log),"\n";
		echo "Error: $error\n";
	}
	else
		echo implode("\n",$log),"\n";
?>