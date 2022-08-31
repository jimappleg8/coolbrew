<?php
/*
 * htdig.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/PHPlibrary/htdig.php,v 1.6 2002/10/17 11:20:10 mlemos Exp $
 *
 */

class Search
{
   var $htdig_path="/usr/local/htdig/bin";
   var $htsearch_path="";
   var $configuration="/usr/local/htdig/conf/htdig.conf";
   var $database_directory="/usr/local/htdig/db";
   var $version="";
   var $secure_search=0;

    function Search()
    {
    
    }
    
    // -------------------------------------------------------------------------

   Function GenerateConfiguration($options)
   {
      if(!($directory=@opendir($this->database_directory)))
         return("it was not specified a valid database directory");
      $options["database_dir"]=$this->database_directory;
      closedir($directory);

      if(!IsSet($options["start_url"])
      || $options["start_url"]=="")
         return("it was not specified a valid start url");

      $defaults=array(
         "bad_extensions"=>".wav .gz .z .sit .au .zip .tar .hqx .exe .com .gif .jpg .jpeg .aiff .class .map .ram .tgz .bin .rpm .mpg .mov .avi",
         "max_head_length"=>"10000",
         "max_doc_size"=>"200000",
         "no_excerpt_show_top"=>"true",
         "valid_punctuation"=>": .-_/!#$%^&*«»"
      );

      for($option=0,Reset($defaults);$option<count($defaults);Next($defaults),$option++)
      {
         $option_name=Key($defaults);
         if(!IsSet($options[$option_name]))
            $options[$option_name]=$defaults[$option_name];
      }

      if(IsSet($options["template_path"]))
      {
         $template_path=$options["template_path"];
         if(!($directory=@opendir($template_path)))
            return("it was not specified an existing template path directory");
         closedir($directory);
         UnSet($options["template_path"]);
         $template_path.="/";
      }
      else
         $template_path="";

      if(!file_exists($template_path."htdig_template.html"))
         return("it was open the htdig_template.html file in template path directory");
      $options["template_map"]="htdig htdig ".$template_path."htdig_template.html";

      if(!file_exists($template_path."htdig_header.html"))
         return("it was open the htdig_header.html file in template path directory");
      $options["search_results_header"]=$template_path."htdig_header.html";

      $options["search_results_footer"]=$template_path."htdig_footer.html";

      if(!file_exists($template_path."htdig_nomatch.html"))
         return("it was open the htdig_nomatch.html file in template path directory");
      $options["nothing_found_file"]=$template_path."htdig_nomatch.html";

      if(!file_exists($template_path."htdig_syntaxerror.html"))
         return("it was open the htdig_syntaxerror.html file in template path directory");
      $options["syntax_error_file"]=$template_path."htdig_syntaxerror.html";

      for($configuration="",$option=0,Reset($options);$option<count($options);Next($options),$option++)
         $configuration.=Key($options).": ".$options[Key($options)]."\n";
      if(!($file=fopen($this->configuration,"w")))
         return("could not open the configuration file \"".$this->configuration."\" for writing");
      if(strcmp($configuration,"")
      && (!fwrite($file,$configuration)
      || !fclose($file)))
         return("could not write to the configuration file");
      return("");
   }
   
    // -------------------------------------------------------------------------

   Function Dig($fuzzy_algorithm="",&$log)
   {
      $log=array();

      if(!strcmp($this->version,""))
      {
         $command=$this->htdig_path."/htdig 2>/dev/null --help";
         $log[]=strftime("%Y-%m-%d %H:%M:%S")." Figuring htdig version... ($command)";
         $version=array();
         Exec($command,$version,$result);
         if($result)
         {
            $log[]=strftime("%Y-%m-%d %H:%M:%S")." htdig failed with result code $result";
            return("execution of the htdig program failed ($command)");
         }
         $match="^This program is part of ht://Dig ([0-9](\.[0-9]+)+)";
         for($line=0;$line<count($version);$line++)
         {
            if(ereg($match,$version[$line],$matches))
            {
               $this->version=$matches[1];
               break;
            }
         }
         if($line>=count($version))
         {
            for($line=0;$line<count($version);$line++)
               $log[]=$version[$line];
            $log[]=strftime("%Y-%m-%d %H:%M:%S")." could not figure what is the htdig program version";
            return("could not figure what is the htdig program version");
         }
         $log[]=strftime("%Y-%m-%d %H:%M:%S")." htdig version is ".$this->version;
      }

      $command=$this->htdig_path."/htdig -v -s -a ".($this->configuration=="" ? "" : " -c ".$this->configuration);
      $log[]=strftime("%Y-%m-%d %H:%M:%S")." Starting htdig... ($command)";
      Exec($command,$log,$result);
      if($result)
      {
         $log[]=strftime("%Y-%m-%d %H:%M:%S")." htdig failed with result code $result";
         return("execution of the htdig program failed ($command)");
      }
      $log[]=strftime("%Y-%m-%d %H:%M:%S")." htdig done...";

      $command=$this->htdig_path."/htmerge -v -s -a ".($this->configuration=="" ? "" : " -c ".$this->configuration);
      $log[]=strftime("%Y-%m-%d %H:%M:%S")." Starting htmerge... ($command)";
      Exec($command,$log,$result);
      if($result)
      {
         $log[]=strftime("%Y-%m-%d %H:%M:%S")." htmerge failed with result code $result";
         return("execution of the htmerge program failed ($command)");
      }
      $log[]=strftime("%Y-%m-%d %H:%M:%S")." htmerge done...";

      if(strcmp($fuzzy_algorithm,""))
      {
         $command=$this->htdig_path."/htfuzzy".($this->configuration=="" ? "" : " -c ".$this->configuration." $fuzzy_algorithm");
         $log[]=strftime("%Y-%m-%d %H:%M:%S")." Starting htfuzzy... ($command)";
         Exec($command,$log,$result);
         if($result)
         {
            $log[]=strftime("%Y-%m-%d %H:%M:%S")." htfuzzy failed with result code $result";
            return("execution of the htfuzzy program failed ($command)");
         }
         $log[]=strftime("%Y-%m-%d %H:%M:%S")." htfuzzy done...";
      }

      $log[]=strftime("%Y-%m-%d %H:%M:%S")." Updating htdig database files";
      if(strcmp($this->version,"3.2")<0)
      {
         $files=array(
            "db.wordlist.work"=>"db.wordlist",
            "db.docdb.work"=>"db.docdb",
            "db.docs.index.work"=>"db.docs.index",
            "db.words.db.work"=>"db.words.db"
         );
      }
      else
      {
         $files=array(
            "db.docdb.work"=>"db.docdb",
            "db.docs.index.work"=>"db.docs.index",
            "db.excerpts.work"=>"db.excerpts",
            "db.words.db.work"=>"db.words.db",
            "db.words.db.work_weakcmpr"=>"db.words.db_weakcmpr"
         );
      }
      for(Reset($files),$file=0;$file<count($files);Next($files),$file++)
      {
         $from_file=$this->database_directory."/".Key($files);
         $to_file=$this->database_directory."/".$files[Key($files)];
         if(!file_exists($from_file))
         {
            $log[]=strftime("%Y-%m-%d %H:%M:%S")." failed while checking htdig database file $from_file possibly because htdig program version is yet not supported";
            return("could not check htdig database file $from_file possibly because htdig program version is yet not supported");
         }
         if(!copy($from_file,$to_file))
         {
            $log[]=strftime("%Y-%m-%d %H:%M:%S")." failed while updating htdig database file $to_file";
            return("could not update htdig database file $to_file");
         }
      }
      $log[]=strftime("%Y-%m-%d %H:%M:%S")." Updated htdig database files";

      for(Reset($files),$file=0;$file<count($files);Next($files),$file++)
         unlink($this->database_directory."/".Key($files));
      return("");
   }
   
    // -------------------------------------------------------------------------

   function do_search($words, $options, &$results)
   {
      $path=$this->htsearch_path."/htsearch";
      if(strcmp($this->htsearch_path,""))
      {
         if(!file_exists($path))
            return("the htsearch program executable could not be found at $path");
      }
      else
      {
         $path=$this->htdig_path."/htsearch";
         if(!file_exists($path))
         {
            $cgi_path=$this->htdig_path."/../cgi-bin/htsearch";
            if(!file_exists($cgi_path))
               return("the htsearch program executable could not be found neither at $path nor at $cgi_path");
            $path=$cgi_path;
         }
      }
      $query_string="words=".UrlEncode($words)."&format=htdig";
      $option_names=array(
         "config",
         "exclude",
         "keywords",
         "matchesperpage",
         "method",
         "page",
         "restrict",
         "sort"
      );
      for($option=0;$option<count($option_names);$option++)
      {
         $option_name=$option_names[$option];
         if(IsSet($options[$option_name]))
            $query_string.="&$option_name=".UrlEncode($options[$option_name]);
      }

      if ($this->secure_search)
      {
         $command=$path.($this->configuration=="" ? "" : " -c ".$this->configuration)." \"$query_string\"";
      }
      else
      {
         $command="REQUEST_METHOD=GET QUERY_STRING=\"$query_string\" ".$path.($this->configuration=="" ? "" : " -c ".$this->configuration);
      }
      $output = array();
      Exec($command, $output, $result);
      
//      echo "<pre>"; print_r($output); echo "</pre>";
      
      $output_lines = implode("\n",$output);

      if ($result)
         return("execution of the htsearch program failed ($command) result $result (".$output_lines.")");
      if (count($output) < 3)
         return("unexpected htsearch program output ($output_lines)");

      switch ($output[2])
      {
         case "NOMATCH":
            $results=array(
               "MatchCount" => 0
            );
            break;
         case "SYNTAXERROR":
            return("unexpected htsearch program syntax error ($command) ($output_lines)");
         default:
            if(count($output)<6)
               return("unexpected htsearch program output ($output_lines)");
            $first=intval($output[3]);
            $last=intval($output[4]);
            $results=array(
               "MatchCount"=>intval($output[2]),
               "FirstMatch"=>$first,
               "LastMatch"=>$last,
               "Words"=>$output[5],
               "PageHeader"=>$output[6],
               "PrevPage"=>$output[7],
               "PageList"=>$output[8],
               "NextPage"=>$output[9],
            );
            for($match=$first;$match<=$last;$match++)
            {
               $line=10+($match-$first)*6;
               $results["Matches"][]=array(
                  "Title"=>$output[$line],
                  "URL"=>$output[$line+1],
                  "Percent"=>intval($output[$line+2]),
                  "Excerpt"=>$output[$line+3],
                  "Modified"=>$output[$line+4],
                  "Size"=>$output[$line+5],
               );
            }
            break;
      }
      return("");
   }
};

?>