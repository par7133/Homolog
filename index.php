<?php

/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of Homolog.
 *
 * Homolog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Homolog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with Homolog. If not, see <https://www.gnu.org/licenses/>.
 *
 * index.php
 * 
 * Homolog home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2024, 5 Mode      
 */
 
 require "init.inc";
 
 $contextType = PUBLIC_CONTEXT_TYPE;
 
 $signHistory = [];
 $cmd = PHP_STR;
 $opt = PHP_STR;
 $param1 = PHP_STR;
 $param2 = PHP_STR;
 $param3 = PHP_STR;
   
 $curLocale = APP_LOCALE;
 $lastSign = PHP_STR;


 function showHistory() {
   global $signHistory;
   global $curPath;
   global $CONFIG;
   global $curLocale;
   global $LOCALE;
   global $lastSign;
   global $password;
   global $contextType;
   
   $signHistoryCopy = $signHistory;
   
   rsort($signHistoryCopy);

   $m = 1;
   foreach($signHistoryCopy as $val) {
     
     $val = rtrim($val, "\n");
     
     $ipos=mb_stripos($val, "|");
     $mydate = left($val,$ipos);
     
     $ipos2=mb_strripos($val, "|");
     $mydesc = substr($val, $ipos+1, (($ipos2 - $ipos) - 1));
     
     $aflag = substr($val, $ipos2+1, 1);

     // If I'm in admin
     if ($contextType === PERSONAL_CONTEXT_TYPE) {
       
       $adminFnc = PHP_STR;
       if ($aflag === "u") {
         $adminFnc = "<a href='#' onclick=\"confSign('" . $val . "')\"><img src='/HL_res/confirm.png' style='width:36px;'></a>";
       } else {
         $adminFnc = "<a href='#' onclick=\"delSign('" . $val . "')\"><img src='/HL_res/del.png' style='width:36px;'></a>";
       }    
     
       // Display event list   
       echo("<table style='width:75%; border:0;' align='center'>");
       echo("<tr>");
       echo("<td style='width:24%; text-align:left; vertical-align:top; padding-top:2px; padding-left:8px;'>");
       echo("<span style='font-family:".DISPLAY_DATE_FONT.";font-size:22px; font-weight:900;'>".$mydate."</span>");
       echo("</td>");
       echo("<td style='width:66%; text-align:left; vertical-align:top; padding-top:5px; padding-left:15px; font-size:18px;'>");
       echo($mydesc);
       echo("</td>");
       echo("<td style='width:10%; text-align:center;padding-left:8px;padding-top:2px'>");
       echo($adminFnc);
       echo("</td>");
       echo("</tr>");   
       echo("</table>");
       
     // If I'm not in admin
     } else {   
       
       if ($aflag !== "u") {

         // Display event list
         echo("<table style='width:75%; border:0;' align='center'>");
         echo("<tr>");
         echo("<td style='width:24%; text-align:left; vertical-align:top; padding-top:2px; padding-left:8px;'>");
         echo("<span style='font-family:".DISPLAY_DATE_FONT.";font-size:22px; font-weight:900;'>".$mydate."</span>");
         echo("</td>");
         echo("<td style='width:76%; text-align:left; vertical-align:top; padding-top:5px; padding-left:15px; font-size:18px;'>");
         echo($mydesc);
         echo("</td>");
         echo("</tr>");   
         echo("</table>");

         //echo("<span style='font-family:".DISPLAY_DATE_FONT.";font-size:38px;'>".$mydate."</span>,".PHP_SPACE.$mydesc."<br>"); 
       }  
     }
     
     $m++;
   }
 }

 function updateHistory(&$update, $maxItems) {
   global $signHistory;
   global $curPath;
   
   // Making enough space in $signHistory for the update..
   $shift = (count($signHistory) + count($update)) - $maxItems;
   if ($shift > 0) {
     $signHistory = array_slice($signHistory, $shift, $maxItems); 
   }		  
   // Adding $signHistory update..
   if (count($update) > $maxItems) {
     $beginUpd = count($update) - ($maxItems-1);
   } else {
	   $beginUpd = 0;
   }	        
   $update = array_slice($update, $beginUpd, $maxItems); 
   foreach($update as $val) {  
	   $signHistory[] = $val;   
   }
 
   // Writing out $signHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".HL_history";
   file_put_contents($filepath, implode('', $signHistory));	 
 }


 function updatecaptchaHistory(&$update) {
   global $captchaHistory;
   global $curPath;
   	        
   foreach($update as $val) {  
     $captchaHistory[] = $val;     
   }
 
   // Writing out $captchaHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".HL_captchahistory";
   file_put_contents($filepath, implode('', $captchaHistory));	 
 }


 function parseCommand() {
   global $command;
   global $cmd;
   global $opt;
   global $param1;
   global $param2;
   global $param3;
   
   $str = trim($command);
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $cmd = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	   $cmd = $str;
	   return;
   }	     
   
   if (left($str, 1) === "-") {
	 $ipos = stripos($str, PHP_SPACE);
	 if ($ipos > 0) {
	   $opt = left($str, $ipos);
	   $str = substr($str, $ipos+1);
	 } else {
	   $opt = $str;
	   return;
	 }	     
   }
   
   if (left($str, 1) === "'") {
     $ipos = stripos($str, "'", 1);
     if ($ipos > 0) {
       $param1 = substr($str, 0, $ipos+1);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }  
   } else {   
     $ipos = stripos($str, PHP_SPACE);
     if ($ipos > 0) {
       $param1 = left($str, $ipos);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }	     
   }
     
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param2 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param2 = $str;
	 return;
   }
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param3 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param3 = $str;
	 return;
   }	     
 	     
 }

 function signParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $date;
  global $desc;
  global $captchacount; 
  global $captchasign;
  global $captchaHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	//param1==""  
	if ($param1!==PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }

  //date!=""
  if ($date===PHP_STR || strlen($date)<4) {
    //echo("WARNING: invalid date<br>");
    return false;
  }  

  //place!=""
  if ($desc===PHP_STR || strlen($desc)<4) {
    //echo("WARNING: invalid desc<br>");
    return false;
  }  
  
  $rescaptcha1=$captchacount>=4;
  $rescaptcha2=count(array_filter($captchaHistory, "odd")) > (APP_MAX_FROM_IP - 1);
  if ($rescaptcha1) {
    echo("WARNING: captcha expired #1<br>");
  }  
  
  //if ($rescaptcha2) {
  //  echo("WARNING: captcha expired #2<br>");
  //}  
  
  //if ($rescaptcha1 || $rescaptcha2) {
  if ($rescaptcha1) {
    return false;
  }  
  
  return true;
 } 


 function odd($val) {
   
   global $captchasign;
   
   return rtrim($val,"\n") == $captchasign;   
 }   
 
  
 function myExecSignCommand() {
   
   global $date;
   global $desc;
   global $curPath;
   global $lastMessage;
   global $captchacount;
   global $captchasign;
   global $captchaHistory;
   
   $newSign = HTMLencodeF($date,false) . "|" . HTMLencodeF($desc,false) . "|u";

   //echo("array_filter=".count(array_filter($captchaHistory, "odd"))."<br>");
   //echo("new_sign?=".((hash("sha256", $newSign . APP_SALT, false) !== $lastMessage)?"true":"false")."<br>");

   if (hash("sha256", $newSign . APP_SALT, false) !== $lastMessage) {

     // Updating message history..
     $output = [];
     $output[] = $newSign . "\n";
     updateHistory($output, HISTORY_MAX_ITEMS);

     // Updating captcha history..
     $output = [];
     $output[] = $captchasign . "\n";
     updatecaptchaHistory($output);

     $lastMessage = hash("sha256", $newSign . APP_SALT, false);
   }
   
 }  


 function confParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $signHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
	if ($myval===PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param1 in $signHistory  
	if (!in_array($myval."\n",$signHistory)) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 


 function myExecConfSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecConfSignCommand()");
     
     $newval = left($mysign, strlen($mysign)-2) . "|v"; 
     
     $key = array_search($mysign."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".HL_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }

 function delParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $signHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
	if ($myval===PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param1 in $signHistory
	if (!in_array($myval."\n",$signHistory)) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 


 function myExecDelSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecDelSignCommand()");
     
     $newval = left($mysign, strlen($mysign)-2) . "|u"; 
     
     $key = array_search($mysign."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".HL_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }


 $curPath = APP_DATA_PATH;
 chdir($curPath);

 $signHistory = file($curPath . DIRECTORY_SEPARATOR . ".HL_history");
 $captchaHistory = file($curPath . DIRECTORY_SEPARATOR . ".HL_captchahistory");

 $password = filter_input(INPUT_POST, "Password");
 if ($password==PHP_STR) {
   $password = filter_input(INPUT_POST, "Password2");
 }  
 $command = filter_input(INPUT_POST, "CommandLine");
 
 //$pwd = filter_input(INPUT_POST, "pwd"); 
 $hideSplash = filter_input(INPUT_POST, "hideSplash");
 $hideHCSplash = filter_input(INPUT_POST, "hideHCSplash");

 $date = filter_input(INPUT_POST, "date");
 $desc = filter_input(INPUT_POST, "desc");

 $captchasign = hash("sha256", $_SERVER["REMOTE_ADDR"] . date("Y") . APP_SALT, false);
 
 $lastMessage = filter_input(INPUT_POST, "last_message");
 $totsigns = count($signHistory);
 //print_r($totsigns);
 //exit(0);
 if ($totsigns > 0) {
   $lastMessage = hash("sha256", rtrim($signHistory[$totsigns-1],"\n") . APP_SALT, false);
 }   

 $captchacount = (int)filter_input(INPUT_POST, "captcha_count");
 //if ($captchacount === 0) {
 //  $captchacount = 1;
 //}  

 if ($password !== PHP_STR) {	
	$hash = hash("sha256", $password . APP_SALT, false);

	if ($hash !== APP_HASH) {
	  $password=PHP_STR;	
    }	 
 } 
  
 parseCommand($command);
 //echo("cmd=" . $cmd . "<br>");
 //echo("opt=" . $opt . "<br>");
 //echo("param1=" . $param1 . "<br>");
 //echo("param2=" . $param2 . "<br>");
 
 
 if ($password !== PHP_STR) {
   
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
 
     if ($cmd === "sign") {
       $captchacount = $captchacount + 1;
       if (signParamValidation()) {
         myExecSignCommand();
       }	     	     
     } else if ($command === "refresh") {
       // refreshing Msg Board..
     }
 
   } else if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {
     
     if ($cmd === "del") {
       if (delParamValidation()) {
         myExecDelSignCommand();
       }	     
     } else if ($cmd === "conf") {
       if (confParamValidation()) {
         myExecConfSignCommand();
       }	     	     
     }       
   } else {
     
   }
   
   $contextType = PERSONAL_CONTEXT_TYPE;
      
 } else {
 
  /*
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
     if ($cmd === "sign") {
       $captchacount = $captchacount + 1;
       if (signParamValidation()) {
         myExecSignCommand();
       }	
     }   
   }*/
 }
 
?>

<!DOCTYPE html>
<head>
	
  <meta charset="UTF-8"/>
  
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  
<!--
    Copyright 2021, 2024 5 Mode

    This file is part of Homolog.

    Homolog is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Homolog is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Homologs. If not, see <https://www.gnu.org/licenses/>.
 -->
  
    
  <title><?php echo(APP_TITLE); ?></title>
	
  <link rel="shortcut icon" href="/favicon.ico?v=<?php echo(time()); ?>>" />
    
  <meta name="description" content="<?php echo(APP_DESCRIPTION); ?>"/>
  <meta name="keywords" content="<?php echo(APP_KEYWORDS); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="index,follow"/>
  
  <script src="/HL_js/jquery-3.1.0.min.js" type="text/javascript"></script>
  <script src="/HL_js/common.js" type="text/javascript"></script>
  <script src="/HL_js/bootstrap.min.js" type="text/javascript"></script>
  
  <script src="/HL_js/index.js" type="text/javascript" defer></script>
  
  <link href="/HL_css/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="/HL_css/style.css" type="text/css" rel="stylesheet">
  
<style>
@import url('https://fonts.googleapis.com/css2?family=<?php echo(str_ireplace(" ","+",DISPLAY_DATE_FONT));?>');
</style>
     
</head>
<body style="<?php echo(DISPLAY_BODY_CSS);?>">

<form id="frmHC" method="POST" action="/" target="_self" enctype="multipart/form-data">

<?php if(APP_USE === "PRIVATE"): ?>
<div class="header" style="background-color:#ffffff;z-index:90;">
   <a id="burger-menu" href="#" style="display:none;"><img src="/HL_res/burger-menu2.png" style="width:58px;"></a><a id="ahome" href="http://homolog.org" target="_blank" style="color:black; text-decoration: none;"><img id="logo-hmm" src="/HL_res/HLlogo.png" style="width:48px;">&nbsp;Homolog</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="agithub" href="https://github.com/par7133/Homolog" style="color:#000000"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="afeedback" href="mailto:info@homolog.org" style="color:#000000"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="asupport" href="tel:+39-331-4029415" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:black;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a><div id="pwd2" style="float:right;position:relative;top:+13px;display:none"><input type="password" id="Password2" name="Password2" placeholder="password" style="font-size:13px; background:#393939; color:#ffffff; width: 125px; border-radius:3px;" value="" autocomplete="off"></div>
</div>
<?php else: ?>
<div class="header2" style="margin:0;padding:0;border-bottom:0px;text-align:center;">
   <?php echo(APP_CUSTOM_HEADER); ?>
</div>  
<?php endif; ?>

<div style="clear:both;margin:auto">&nbsp;</div>

<?php
  $callSideBarTOP = 1; 
  if(APP_USE === "PRIVATE") {
    $callSideBarTOP = 65;   
  }    
?>

<div id="call-sidebar" style="position:absolute; top:<?php echo($callSideBarTOP);?>px; left:1px; clear:both; float:left; width:1.5%; max-width:5px; height:100%; min-height:1900px; text-align:center; border-right: 1px solid #2c2f34;z-index:1;">
    &nbsp;
</div>

<div id="sidebar" style="position:absolute; left:6px; top:0px; clear:both; background-color:#FFFFFF; padding:8px; width:25%; max-width:250px; height:100%; text-align:center; border-right: 1px solid #2c2f34; display:none;z-index:91;">
    
    <button type="button" class="close" aria-label="Close" onclick="closeSideBar();" style="position:relative; left:-10px;">
      <span aria-hidden="true">&times;</span>
    </button>
    
    <br><br>
    <img src="/HL_res/HLgenius.png" alt="HL Genius" title="HL Genius" style="position:relative; left:+6px; width:90%; border: 1px dashed #EEEEEE;">
    &nbsp;<br><br>
    <div style="text-align:left;white-space:nowrap;">
    &nbsp;&nbsp;<input type="password" id="Password" name="Password" placeholder="password" style="font-size:13px; background:#393939; color:#ffffff; width: 60%; border-radius:3px;" value="<?php echo($password);?>" autocomplete="off">&nbsp;<input type="submit" value="<?php echo(getResource("Go", $curLocale));?>" style="text-align:left;width:25%;"><br>
    &nbsp;&nbsp;<input type="text" id="Salt" placeholder="salt" style="position:relative; top:+5px; font-size:13px; background:#393939; color:#ffffff; width: 90%; border-radius:3px;" autocomplete="off"><br>
    <div style="text-align:center;">
    <a href="#" onclick="showEncodedPassword();" style="position:relative; left:-2px; top:+5px; color:#000000; font-size:12px;"><?php echo(getResource("Hash Me", $curLocale));?>!</a>     
    
    <br><br><br>

    </div>
    </div>
</div>

<div id="contentbar" style="width:100%;float:left;">

	<?php if (APP_SPLASH): ?>
	<?php if ($hideSplash !== PHP_STR): ?>
	<div id="splash" style="color:black; border-radius:20px; position:relative; left:+3px; width:98%; background-color: #33aced; padding: 20px; margin-bottom:8px;">	
	
	   <button type="button" class="close" aria-label="Close" onclick="closeSplash();" style="position:relative; left:-10px;">
        <span aria-hidden="true">&times;</span>
     </button>
	
	   Hello and welcome to Homolog!<br><br>
	   
	   Homolog is a light and simple software on premise to log calendar and events.<br><br>
	   
	   Homolog is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.<br><br>
	   
     Homolog name comes from a prank between two words: "homines" meaning our intention to put humans first and "log".<br><br>
     
	   First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.<br><br>
	   
	   As you are going to run Homolog in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:<br>
	   <ol>
	   <li>Check the permissions of your "data" folder in your web app private path; and set its path in the config file.</li>
	   <li>In the data path create a ".HL_history" and ".HL_captchahistory" files and give them the write permission.</li>
     <li>Finish to setup the configuration file apporpriately, in the specific:</li>
     <ul>
       <li>Configure the APP_USE and APP_CONTEXT appropriately.</li>
       <li>Configure the DISPLAY attributes as required.</li>
       <li>Configure the max history items as required (default: 1000).</li>	      
	   </ul>
     </ol>
	   
	   <br>	
     
	   Hope you can enjoy it and let us know about any feedback: <a href="mailto:info@homolog.org" style="color:#e6d236;">info@homolog.org</a>
	   
	</div>	
	<?php endif; ?>
	<?php endif; ?>

  <div style="width:100%; padding: 8px; text-align:center; font-size:26px; border:0px solid red;">
   
    <br>
  
    <?php if (APP_DEFAULT_CONTEXT === "PRIVATE"): ?>
     
      <?php if ($contextType === PUBLIC_CONTEXT_TYPE): ?>
     
        <div style="padding:30px; font-size:23px;margin-bottom:23px;font-weight:900;"><h1><?php echo(APP_GUEST_MSG??"&nbsp;"); ?></h1></div>
      
      <?php else: ?>
      
        <div style="font-size:23px;margin-bottom:23px;font-weight:900;"><h1><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></h1></div>
        
        <br>
        
        <input type="text" id="date" name="date" placeholder="Date" value="<?php echo(date("Y-m-d"));?>" style="width:170px">&nbsp;<input type="text" id="desc" name="desc" placeholder="Description" style="width:36%;max-width:430px;" maxlength="300"><br>
        
        <input type="button" id="send" name="send" value="&nbsp;<?php echo(DISPLAY_SUBMIT_BUTTON);?>&nbsp;" title="<?php echo(DISPLAY_SUBMIT_BUTTON);?>" style="position:relative;top:+28px;margin-top:25px;height:50px;background-color:red;border:1px solid black;color:white;font-size:medium;">
        
        <br><br>
        
        <hr style="color:black; margin-top:30px;">
        
        <br>
        
        <?php showHistory(); ?>
   
      <?php endif; ?>
    
    <?php else: ?>
    
      <?php if ($contextType === PUBLIC_CONTEXT_TYPE): ?>
    
        <div style="font-size:23px;margin-bottom:23px;font-weight:900;"><h1><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></h1></div>
        
        <br>
        
        <hr style="color:black; margin-top:30px;">

        <br>
        
        <?php showHistory(); ?>
        
      <?php else: ?>

        <div style="font-size:23px;margin-bottom:23px;font-weight:900;"><h1><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></h1></div>
        
        <br>
        
        <input type="text" id="date" name="date" placeholder="Date" value="<?php echo(date("Y-m-d"));?>" style="width:170px">&nbsp;<input type="text" id="desc" name="desc" placeholder="Description" style="width:36%;max-width:430px;" maxlength="300"><br>
        
        <input type="button" id="send" name="send" value="&nbsp;<?php echo(DISPLAY_SUBMIT_BUTTON);?>&nbsp;" title="<?php echo(DISPLAY_SUBMIT_BUTTON);?>" style="position:relative;top:+28px;margin-top:25px;height:50px;background-color:red;border:1px solid black;color:white;font-size:medium;">
        
        <br><br>
        
        <hr style="color:black; margin-top:30px;">
        
        <br>
        
        <?php showHistory(); ?>

      <?php endif; ?>
    
    <?php endif; ?>
    
    <br><br><br>

    <?php if(APP_USE === "BUSINESS"): ?>    
    <div style="font-size:23px">
      <a id="ahome" href="http://homolog.org" target="_blank" style="color:black;"><img id="logo-hl" src="/HL_res/HLlogo.png" style="position:relative;top:-25px;width:48px;margin:5px;">Powered by Homolog</a>
    </div>
    <?php endif; ?>&nbsp;
       
  </div>     

</div>

<input type="hidden" id="CommandLine" name="CommandLine">
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">
<input type="hidden" name="hideHCSplash" value="1">
<input type="hidden" name="captcha_count" value="<?php echo($captchacount); ?>">
<input type="hidden" name="last_message" value="<?php echo($lastMessage); ?>">

</form>

<!--
<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project <span class="no-sm">and <a href="http://wysiwyg.systems">WYSIWYG</a> system</span>. Some rights reserved.</span></div>	
</div>
-->

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "metrics.html")): ?>
<?php include("metrics.html"); ?> 
<?php endif; ?>

</body>
</html>
