<?php

// =========================================================================
// emploc.inc.php
//
// =========================================================================


// -------------------------------------------------------------------------
// function getJpegphoto
//   This function gets a jpeg photo from the directory and saves it in a
//   temporary directory. It then returns the path so the HTML file can access
//   the image.
//
// -------------------------------------------------------------------------

function getJpegphoto ($linkIdentifier, $dn, $uid) 
{

	$jpeg_file = "/var/opt/httpd/ctdocs/images/employees/" . $uid . ".jpg";

	if (file_exists($jpeg_file)) {
		$jpeg_link = "/images/employees/" . $uid . ".jpg";
	} else {
		set_error_handler("photoError");
		$searchFilter = "(objectClass=*)";
		$sr=ldap_read($linkIdentifier, $dn, $searchFilter, array("jpegphoto"));
	
		$info = ldap_first_entry($linkIdentifier, $sr);
		$data = ldap_get_values_len($linkIdentifier, $info, "jpegphoto");
	
		if (!$data[0]) {
			$jpeg_link = "/images/employees/no_picture.jpg";
		} else {
			$jpeg_link = "/images/employees/" . $uid . ".jpg";
			$f = fopen($jpeg_file,"w");
			fwrite($f, $data[0]);
			fclose($f);
		}
	}
	return $jpeg_link;
	
}


// -------------------------------------------------------------------------
// function photoError
//   Empty function to avoid getting warnings displayed.
//
// -------------------------------------------------------------------------

function photoError ($error_type, $error_msg) {

}


// -------------------------------------------------------------------------
// function createSearchFilter
//   Given a search criteria string, this function creates a search
//   filter expression: 
//
// -------------------------------------------------------------------------

function createSearchFilter($searchCriteria, $logic, $wildcard) {

    $noOfFieldsSet = 0;
    if ($searchCriteria["givenName"]) {
        $searchFilter = "(givenName=" . $wildcard . 
                         $searchCriteria["givenName"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["cn"]) {
        $searchFilter .= "(cn=" . $wildcard . $searchCriteria["cn"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["sn"]) {
        $searchFilter .= "(sn=" . $wildcard . $searchCriteria["sn"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["title"]) {
        $searchFilter .= "(title=" . $wildcard . $searchCriteria["title"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["mail"]) {
        $searchFilter .= "(mail=" . $wildcard . $searchCriteria["mail"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["employeenumber"]) {
        $searchFilter .= "(employeenumber=" . $wildcard .
                          $searchCriteria["employeenumber"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["ou"]) {
        $searchFilter .= "(ou=" . $wildcard . $searchCriteria["ou"] . "*)";
        ++$noOfFieldsSet;
    }

    if ($searchCriteria["telephonenumber"]) {
        $searchFilter .= "(telephonenumber=" . $wildcard .
                         $searchCriteria["telephonenumber"] . "*)";
        ++$noOfFieldsSet;
    }

    // We perform a logical AND  or OR (depending on $logic) on all
    // specified search criteria to create the final search filter: 

    if ($noOfFieldsSet >= 2) {
        $searchFilter = "(" . $logic . " " . $searchFilter . ")";
    }
    return $searchFilter;
}

?>