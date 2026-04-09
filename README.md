# Homolog
  
Hello and welcome to Homolog!  
  
Homolog is a light and simple software on premise to log calendar or events.  
   
Homolog is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.  
  
Homolog name comes from a prank between two words: "homines" meaning our intention to put humans first and "log".  
  
First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.  
  
As you are going to run Homolog in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:  
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

Login with the password for the admin view.

For any need of software additions, plugins and improvements please write to <a href="mailto:info@numode.eu">info@numode.eu</a>  

To help please donate by clicking <a href="https://gaox.io/l/dona1">https://gaox.io/l/dona1</a> and filling the form.  
   
### Screenshots  
	   
 ![Homolog](/HL_res/screenshot1.png)  

Feedback: <a href="mailto:code@gaox.io" style="color:#e6d236;">code@gaox.io</a>
