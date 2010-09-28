#!/usr/bin/php
<?php

include("urls.php");

$header_replace = array(
'Surname'=>'sur_name',
'Surname/Nom de famille'=>'sur_name',
'Surname / Nom de famille'=>'sur_name',

'First Name'=>'given_name',
'Given Name'=>'given_name',
'Given Name/Pr&#233;nom'=>'given_name',
'Given Name/ Pr&#233;nom'=>'given_name',
'Given Name / Pr&#233;nom'=>'given_name',
'Given Name/Pr&eacute;nom'=>'given_name',
'Given Name / Pr&eacute;nom'=>'given_name',

'Ministry / Minist&#232;re'=>'ministry',

'Sector'=>'category',

'Seconded Position / Poste combl&#233; par la personne d&#233;tach&#233;e'=>'seconded_position',

'1997 Salary'=>'salary',
'97 Salary'=>'salary',
'Salary Paid'=>'salary',
'Salary Paid/Traitement'=>'salary',
'Salary Paid / Traitement'=>'salary',

'Benefits'=>'taxable_benefits',
'97 Benefits'=>'taxable_benefits',
'1997 Benefits'=>'taxable_benefits',

'TaxableBenefits'=>'taxable_benefits',
'Taxable Benefits'=>'taxable_benefits',
'TaxableBenefits/Avant.impos.'=>'taxable_benefits',
'TaxableBenefits / Avant. impos.'=>'taxable_benefits',
'Taxable Benefits / Avant.impos.'=>'taxable_benefits',
'Taxable Benefits / Avant. impos.'=>'taxable_benefits',
'Taxable Benefits/Avantages Imposables'=>'taxable_benefits',
'Taxable Benefits / Avantages imposables'=>'taxable_benefits',

'Employer'=>'employer',
'/Employeur'=>'employer',
'/ Employeur'=>'employer',
' / Employeur'=>'employer',
'Employer/Employeur'=>'employer',
'Employer/ Employeur'=>'employer',
'Employer / Employeur'=>'employer',
'Public Sector Organization Employer / Employeur - Organisme du secteur public'=>'employer',

'Position'=>'position',
'Position/Poste'=>'position',
'Position / Poste'=>'position',
);

if (!file_exists("cache")) system("mkdir cache");
if (!file_exists("output_sql")) system("mkdir output_sql");

system("rm output_sql/*.sql");

$fp_sal = fopen("output_sql/salaries.sql", "a");
fprintf($fp_sal, "DROP TABLE IF EXISTS salaries;\n");
fprintf($fp_sal, "CREATE TABLE salaries (id int primary key auto_increment, year int(10), source text, category text, employer text, ministry text, sur_name text, given_name text, position text, seconded_position text, salary text, taxable_benefits text);\n");

$fp_org = fopen("output_sql/organizations_with_no_salaries.sql", "a");
fprintf($fp_org, "DROP TABLE IF EXISTS organizations_with_no_salaries;\n");
fprintf($fp_org, "CREATE TABLE organizations_with_no_salaries (id int primary key auto_increment, year int(10), source text, category text, organization text);\n");

foreach($all_urls as $year => $urls)
{
  foreach($urls as $url)
  {
    print "$url\n";
    $local_file = "cache/$year" . "_" . basename($url);
    if (!file_exists($local_file))
    {
      file_put_contents($local_file, $contents);
    }
    $contents = file_get_contents($local_file);
    
    //$contents = substr($contents, stripos($contents, "<table"));
    //$contents = substr($contents, strpos($contents, ">")+1);
    
    $contents = str_replace("\n", '', $contents);
    $contents = str_replace("\r", '', $contents);
    $contents = str_replace("\t", '', $contents);
    $contents = str_ireplace(' align=right', '', $contents);
    $contents = str_ireplace(' border="0"', '', $contents);
    $contents = str_ireplace('<THEAD>', '', $contents);
    $contents = str_ireplace('</THEAD>', '', $contents);
    $contents = str_ireplace('<TBODY>', '', $contents);
    $contents = str_ireplace('</TBODY>', '', $contents);
    $contents = str_ireplace('<SPAN>', '', $contents);
    $contents = str_ireplace('</SPAN>', '', $contents);
    $contents = str_ireplace('<BR>', '', $contents);
    $contents = str_ireplace('<BR/>', '', $contents);
    $contents = str_ireplace('<BR />', '', $contents);
    $contents = str_ireplace(' NAME="C_OPS"', '', $contents);
    
    $contents = preg_replace('/ (id|headers)=\"\w+.\d*\w*.\w+\"/', '', $contents);
    $contents = preg_replace('/ (rowspan|colspan)=\"\d+\"/', '', $contents);
    $contents = preg_replace('/ (name|NAME)=\"\w*-? ?\w*\"/', '', $contents);
    $contents = preg_replace('/ (id|ALIGN|VALIGN|WIDTH|align|valign|cellspacing|cellpadding|class|lang|LANG|scope|SCOPE|width|height)=\"\w*%?\"/', '', $contents);
    
    $contents = preg_replace('/ (width)=\d+/', '', $contents);
    $contents = preg_replace('/<a href="\/en\/publications\/salarydisclosure\/200\d\/addenda_0\d.html\#\w+"( title="addendum")?>/', '', $contents);
    
    $contents = str_replace('<CAPTION>', '<caption>', $contents);
    $contents = str_replace('</CAPTION>', '</caption>', $contents);
    $contents = str_replace('<ABBR title', '<abbr title', $contents);
    $contents = str_replace('<ABBR TITLE', '<abbr title', $contents);
    $contents = str_replace('</ABBR', '</abbr', $contents);
    $contents = str_replace('<ACRONYM TITLE', '<acronym title', $contents);
    $contents = str_replace('</ACRONYM', '</acronym', $contents);
    
    $contents = str_ireplace('<SPAN LANG="en">', '', $contents);
    $contents = str_ireplace('<SPAN LANG="fr">', '', $contents);
    $contents = str_ireplace('<SPAN CLASS="fr-ca">', '', $contents);
    $contents = str_ireplace(' LANG="fr-ca"', '', $contents);
    $contents = str_ireplace(' LANG="en"', '', $contents);
    $contents = str_ireplace(' LANG="en-ca"', '', $contents);
    $contents = str_ireplace(' xml:lang="en"', '', $contents);
    $contents = str_ireplace(' xml:lang="fr-ca"', '', $contents);
    $contents = str_ireplace('<span xml:lang="en-ca">', '', $contents);
    $contents = str_ireplace('<span>', '', $contents);
    $contents = str_ireplace('</span>', '', $contents);
    $contents = str_ireplace('<a></a>', '', $contents);
    $contents = str_ireplace('<sup>', '', $contents);
    $contents = str_ireplace('</sup>', '', $contents);
    $contents = str_ireplace('<strong>', '', $contents);
    $contents = str_ireplace('</strong>', '', $contents);
    
    $contents = str_replace('<spanlang="en">', '', $contents);
    
    $contents = str_replace(' style="vertical-align: bottom;"', '', $contents);
    
    $contents = str_replace(' href="#sftn1"', '', $contents);
    $contents = str_replace(' href="#sftn2"', '', $contents);
    $contents = str_replace(' name="sftn_1"', '', $contents);
    $contents = str_replace(' name="sftn_2"', '', $contents);
    
    /*$contents = str_replace(' and ', ' & ', $contents);
    $contents = str_replace(' And ', ' & ', $contents);
    $contents = str_replace('Manager of ', 'Manager, ', $contents);
    $contents = str_replace('Manager Of ', 'Manager, ', $contents);
    $contents = str_replace('Director of ', 'Director, ', $contents);
    $contents = str_replace('Director Of ', 'Director, ', $contents);
    $contents = str_replace('Professor of ', 'Professor, ', $contents);
    $contents = str_replace('Professor Of ', 'Professor, ', $contents);*/
    
    $contents = str_replace('<!--', '', $contents);
    $contents = str_replace('-->', '', $contents);
    
    while (strpos($contents, '  ') !== false)
      $contents = str_replace('  ', ' ', $contents);
    
    $contents = str_replace('<td >', '<td>', $contents);
    
    $contents = trim(str_replace('> <', '><', $contents));
    $contents = str_replace(' >', '>', $contents);
    
    $contents = str_replace('</TD', '</td', $contents);
    $contents = str_replace('<TD', '<td', $contents);
    $contents = str_replace('</TR', '</tr', $contents);
    $contents = str_replace('<TR', '<tr', $contents);
    $contents = str_replace('</TH', '</th', $contents);
    $contents = str_replace('<TH', '<th', $contents);
    $contents = str_ireplace('<td> none paid $100,000.00</td>', '', $contents);
    
    $contents = str_ireplace('<SPAN>', '', $contents);
    $contents = str_replace('<td><u>[1]</u></td>', '', $contents);
    $contents = str_replace('<u>', '', $contents);
    $contents = str_replace('</u>', '', $contents);
    $contents = str_replace('<div>', '', $contents);
    $contents = str_replace('</div>', '', $contents);
    $contents = str_replace('<td>&nbsp;</td>', '', $contents);
    $contents = str_replace('<tr></tr>', '', $contents);
    $contents = str_replace('</tr></tr>', '</tr>', $contents);
    $contents = str_replace('<a>*</a>', '', $contents);
    $contents = str_replace('<a>', '', $contents);
    $contents = str_replace('</a>', '', $contents);
    $contents = str_replace('<a/>', '', $contents);
    $contents = str_replace('</td><tr><td>', '</td></tr><tr><td>', $contents);
    
    $contents = str_replace('<tr><td><table><tr><td>', '<table><tr><td>', $contents);
    
    while (strpos($contents, '<th> ') !== false)
      $contents = str_replace('<th> ', '<th>', $contents);
    while (strpos($contents, ' </th>') !== false)
      $contents = str_replace(' </th>', '</th>', $contents);
    
    while (strpos($contents, '<td> ') !== false)
      $contents = str_replace('<td> ', '<td>', $contents);
    while (strpos($contents, ' </td>') !== false)
      $contents = str_replace(' </td>', '</td>', $contents);
    
    while (is_string($contents) && stripos($contents, "<table") !== FALSE)
    {
      $contents = substr($contents, stripos($contents, "<table"));
      $contents = substr($contents, strpos($contents, ">")+1);
      
      if (strpos(substr($contents,0,100), "<caption>")!==FALSE)
      {
        $start_pos = strpos($contents, "<caption>")+strlen("<caption>");
        $category = trim(addslashes(substr($contents, $start_pos, strpos($contents, "</caption>")-$start_pos)));
        $contents = substr($contents, strpos($contents, "</caption>")+strlen("</caption><tr><td>"));
        print "found $category\n";
      }
      else $category = '';
      
      $headers = array();
      if (strpos(substr($contents,0,100), "<th>")!==FALSE)
      {
        $headers = explode("</th><th>", substr($contents, 8, stripos($contents, '</th></tr><tr><td>')-8));
        foreach ($headers as $i => $header)
          if (isset($header_replace[$header]))
            $headers[$i] = $header_replace[$header];
        $headers = array_merge(array('year', 'source'), $headers);
        $contents = substr($contents, stripos($contents, '</th></tr><tr><td>')+strlen('</th></tr><tr><td>'));
      }
      
      if (strpos($contents, "<table><tr><td>")!==FALSE)
      {
        $contents = substr($contents, stripos($contents, '<table><tr><td>')+strlen('<table><tr><td>'));
      }
      $c_temp = substr($contents, 0, stripos($contents, '</td></tr></table>'));
      
      $c = explode('</td></tr><tr><td>', $c_temp);
      
      if (strpos($url, "nosal")!==FALSE && strpos(substr($contents,0,100), "<th>")===FALSE)
      {
        $category = trim(addslashes($c[0]));
        unset($c[0]);
        $headers = array('category','year','source','organization');
      }
      
      $q = array();
      $q_nosal = array();
      foreach ($c as $row)
      {
        //$row = str_replace('</abbr>.', '</abbr>', $row);
        //$row = str_replace('</acronym>.', '</acronym>', $row);
        //$row = preg_replace('/<abbr title="(.*?)">\S+?<\/abbr>/i', '${1} ', $row);
        //$row = preg_replace('/<acronym title="(.*?)">\S+?<\/acronym>/i', '${1} ', $row);
        //$row = str_replace('  ', ' ', $row);
        //$row = str_replace(' ,', ',', $row);
        
        $cols = array_merge(array($year, $url), explode('</td><td>', $row));
        
        foreach($cols as $col)
          if ((strpos($col, "<") !== FALSE || strpos($col, ">") !== FALSE) && strpos($col, "<abbr") === FALSE && strpos($col, "addenda") === FALSE && strpos($col, "<acro") === FALSE)
          {
            print "total count is ".count($q)." - ".count($q_nosal)."\n";
            print_r($cols);
            die();
          }
        
        if (strpos($url, "nosal")!==false)
        {
          if (isset($cols[1])) $cols[1] = trim(addslashes($cols[1]));
          if (isset($cols[2])) $cols[2] = trim(addslashes($cols[2]));
          if (isset($cols[3])) $cols[3] = trim(addslashes($cols[3]));
          if (isset($category) && $category!='') $cols = array_merge(array($category), $cols);
          $q_nosal[] = "('" . implode("', '", $cols) . "')";
        }
        else
        {
          if ($headers[2] == 'ID') array_splice($cols, 2, 1); 
          
          $cols[2] = addslashes($cols[2]);
          $cols[3] = addslashes($cols[3]);
          $cols[4] = addslashes($cols[4]);
          $cols[5] = addslashes($cols[5]);
          if (isset($cols[6])) $cols[6] = addslashes($cols[6]);
          
          if ($headers[6] == 'taxable_benefits' || $headers[6] == 'salary') $cols[6] = str_replace(',', '', str_replace('$', '', $cols[6]));
          if ($headers[7] == 'taxable_benefits' || $headers[7] == 'salary') $cols[7] = str_replace(',', '', str_replace('$', '', $cols[7]));
          if ($headers[8] == 'taxable_benefits' || $headers[8] == 'salary') $cols[8] = str_replace(',', '', str_replace('$', '', $cols[8]));
          
          //if (count($cols) != count($headers)) { print "count aint right\n"; print_r($headers); print_r($cols); die(); }
          
          //if (trim($cols[7]) == '') { print "col 7 is ''\n"; print_r($cols); die(); }
          
          $q[] = "('" . implode("', '", $cols) . "')";
        }
      }
      $contents = substr($contents, stripos($contents, '</td></tr></table>')+strlen('</td></tr></table>'));
      //print $contents . "\n";
      
      if ($headers[2] == 'ID') array_splice($headers, 2, 1);
      
      //print "total count is ".count($q)." - ".count($q_nosal)."\n";
      foreach (array_chunk($q, 500) as $s)
      {
        fprintf($fp_sal, "INSERT INTO salaries (".implode(",",$headers).") VALUES " . implode(",", $s) . ";\n");
        //mysql_query($query);
        //print mysql_error();
        //print "  " . mysql_affected_rows() . " affected rows\n";
        //if (mysql_affected_rows() == -1) die($query);
        //if (mysql_affected_rows() != count($s)) print "FUCK FUCK mysql_affected_rows() = " . mysql_affected_rows() . " and count(s) = " . count($s) . "\n";
      }
      
      foreach (array_chunk($q_nosal, 500) as $s)
      {
        fprintf($fp_org, "INSERT INTO organizations_with_no_salaries (".implode(",",$headers).") VALUES " . implode(",", $s) . ";\n");
        //mysql_query($query);
        //print mysql_error();
        //print "  " . mysql_affected_rows() . " affected rows\n";
        //if (mysql_affected_rows() == -1) die($query);
        //if (mysql_affected_rows() != count($s)) print "FUCK FUCK mysql_affected_rows() = " . mysql_affected_rows() . " and count(s) = " . count($s) . "\n";
      }
    }
    if (count($q)==0 && count($q_nosal)==0) die("");
  }
}



?>