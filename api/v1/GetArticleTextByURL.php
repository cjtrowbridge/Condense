<?php

function GetArticleTextByURL($URL){
  
  include('CacheURL.php');
  $LongformText=CacheURL($_REQUEST['LongformURL']);
  
  if($LongformText==false){
    die('<p>Unable to fetch URL.</p>');
  }
  
  
  $Article = getplaintextfromhtml($LongformText);
  
  if($Article==''){  
    //Get article text only
    $doc = new DOMDocument();
    $doc->loadHTML($LongformText);

    $Items = $doc->getElementById('article-text');
    foreach($Items as $Item){
      $Article = trim($Item->textContent);
    }
  }
  
  if($Article==''){
    $Divs = $doc->getElementsByTagName('div');
    foreach($Divs as $Div){
      $Class = $Div->getAttribute('class');
      if(!(strpos($Class,'dateline-storybody')===false)){
        $Article = $Div->textContent;
      }
    }
  }
  
  if($Article==''){
    $Divs = $doc->getElementsByTagName('div');
    foreach($Divs as $Div){
      $Class = $Div->getAttribute('class');
      if(!(strpos($Class,'article-text')===false)){
        $Article = $Div->textContent;
      }
    }
  }
  
  
  if($Article==''){
    $Divs = $doc->getElementsByTagName('article');
    foreach($Divs as $Div){
      $Article = $Div->textContent;
      var_dump($Div);
    }
    if($Article==''){
      
      exit;
    }
  }

  //Clean up article text
  
  if($Article==''){
    mail('chris.j.trowbridge@gmail.com','IDK HOW TO PARSE THIS','<a href="view-source:'.$_REQUEST['LongformURL'].'">'.$_REQUEST['LongformURL'].'</a>');
    die('Unable to parse');
  }

  
  
  //Remove a single space at the beginning of a line
  $Article = str_replace(PHP_EOL.' ',PHP_EOL,$Article);
  
  //remove any repeated PHP_EOL
  $StillHaveGaps = true;
  while($StillHaveGaps){
    $Temp = str_replace(PHP_EOL.PHP_EOL,PHP_EOL,$Article);
    if($Article == $Temp){
      $StillHaveGaps = false;
    }
    $Article = $Temp;
    unset($Temp);
  }
  
  //convert tabs, new lines, and repeated spaces to periods
  $Article = str_replace('  ','. ',$Article);
  $Article = str_replace('	','. ',$Article);
  $Article = str_replace(PHP_EOL,'. ',$Article);
  
  $Article = strip_tags($Article);
  
  return $Article;
}

function getplaintextfromhtml($html){
  include 'simple_html_dom.php';
  $html = str_get_html($html);
  $data = $html->find('body', 0)->innertext;
  return $data;
}
