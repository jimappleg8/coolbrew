<?php
/*
 * htdig_search.php
 *
 * Purpose:  Search the database of indexed pages and present the results.
 *
 * Run this script from your Web server.  Rename it to something else with
 * an adequate extension (.php3 , .php , etc.) if you can not configure
 * your Web server to run this as a PHP script.
 *
 * @(#) $Header: /home/mlemos/cvsroot/PHPlibrary/htdig_search.php,v 1.4 2004/02/11 22:36:16 mlemos Exp $
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
	 * Set the secure search option to let the latest Ht:/Dig versions
	 * (3.1.6 or later) use configuration files stored in paths different
	 * from the default.
	 */
	$htdig->secure_search=1;

?><HTML>
<HEAD>
<TITLE>Search this site</TITLE>
<BODY>
<H1><CENTER>Search this site</CENTER></H1>
<HR>
<FORM METHOD="GET" ACTION="<? echo $PHP_SELF ?>" NAME="search_form">
<CENTER><TABLE BORDER>
<TR>
<TD>
<CENTER><TABLE>
<TR>
<TH ALIGN=right>Search for:</TH>
<TD><INPUT TYPE="text" NAME="words" VALUE="<?
	if(IsSet($words))
		echo HtmlEntities($words);
?>"></TD>
<TD><CENTER><INPUT TYPE="submit" VALUE="Go"</CENTER></TD>
</TR>
<TR>
<TH ALIGN=right>Match</LABEL>:</TH>
<TD><SELECT NAME="method">
<OPTION VALUE="or"<?
	if(IsSet($method)
	&& $method=="or")
		echo " SELECTED";
?>>Any word</OPTION>
<OPTION VALUE="and"<?
	if(IsSet($method)
	&& $method=="and")
		echo " SELECTED";
?>>All words</OPTION>
</SELECT>
</TD></TR>
</TABLE></CENTER>
</TD></TR>
</TABLE></CENTER>
<INPUT TYPE="hidden" NAME="go_search" VALUE="1" ID="go_search">
</FORM>
<?
	if(IsSet($go_search))
	{
		if(IsSet($page)
		&& intval($page)>0)
			$page=intval($page);
		else
			$page=1;

		/* How many matches per page? */
		$matchesperpage=10;

		/* What is the limit of Next and Previous result page links ? */
		$listpages=4;

		$options=array(
			"matchesperpage"=>$matchesperpage,
			"page"=>$page,
			"method"=>$method
		);
		$words=ereg_replace("[ ]+","|",$words);
		if(!strcmp($error=$htdig->Search($words,$options,$results),""))
		{
			$maximum_page=intval(($results["MatchCount"]+$matchesperpage-1)/$matchesperpage);
			if($results["MatchCount"])
			{
				if($page>$maximum_page)
				{
					$options["page"]=$page=$maximum_page;
					$error=$htdig->Search($words,$options,$results);
				}
			}
			if(!strcmp($error,""))
			{
				if($results["MatchCount"]>0)
				{
?>
<TABLE WIDTH="90%">
<TR>
<TD ALIGN=right WIDTH="5%"> </TD>
<TD><B>Pages found:</B> <?
					echo $results["MatchCount"];
?></TD>
</TR>
</TABLE>
<?
					if($results["MatchCount"]>$matchesperpage)
					{
?>
<TABLE WIDTH="90%">
<TR>
<TD WIDTH="5%"> </TD>
<TD><TABLE>
<TR>
<?
						$link_values="words=".UrlEncode($words)."&method=$method&go_search=1";
						if($page>1)
						{
							if(($link_page=$page-$listpages)<1)
								$link_page=1;
							for(;$link_page<$page;$link_page++)
							{
								$page_range=(($link_page-1)*$matchesperpage+1)."-".min($link_page*$matchesperpage,$results["MatchCount"]);
								$url="$PHP_SELF?page=$link_page&$link_values";
								echo "<TD><A HREF=\"$url\">$page_range</A></TD>\n";
							}
							echo "<TD><A HREF=\"$url\">&lt;&lt; Previous</A></TD>\n";
						}
						$page_range=(($page-1)*$matchesperpage+1)."-".min($page*$matchesperpage,$results["MatchCount"]);
						echo "<TD><B>$page_range</B></TD>\n";
						if($page<$maximum_page)
						{
							$link_page=$page+1;
							$url="$PHP_SELF?page=$link_page&$link_values";
							echo "<TD><A HREF=\"$url\">Next &gt;&gt;</TD>\n";
							if(($last_page=$page+$listpages)>$maximum_page)
								$last_page=$maximum_page;
							for(;$link_page<=$last_page;$link_page++)
							{
								$page_range=(($link_page-1)*$matchesperpage+1)."-".min($link_page*$matchesperpage,$results["MatchCount"]);
								$url="$PHP_SELF?page=$link_page&$link_values";
								echo "<TD><A HREF=\"$url\">$page_range</A></TD>\n";
							}
						}
?>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
<?
					}

					$first=$results["FirstMatch"];
					$last=$results["LastMatch"];
					for($match=$first;$match<=$last;$match++)
					{
?>
<BR>
<TABLE WIDTH="90%">
<TR>
<TD ALIGN=right WIDTH="5%">
<?					echo $match;
?>.</TD>
<TD><?
						echo "<A HREF=\"",$results["Matches"][$match]["URL"],"\">".$results["Matches"][$match]["Title"]," (",$results["Matches"][$match]["Percent"],"%)";
?></TD>
</TR>
<TR>
<TD> </TD>
<TD><FONT SIZE=-1><?
						echo $results["Matches"][$match]["Excerpt"]
?></FONT></TD>
</TR></TABLE>
<?
					}
				}
				else
				{
?>
<H2><CENTER>Sorry no pages were found.</CENTER></H2>
<?
				}
			}
		}
		if(strcmp($error,""))
		{
?>
<H2>Error: <?
			echo HtmlEntities($error);
?>.</H2>
<?
		}
	}
?>
<HR>
</BODY>
</HTML>
