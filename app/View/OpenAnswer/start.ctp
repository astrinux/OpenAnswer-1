<?php
/**
 *
 * @author          VoiceNation, LLC
 * @copyright       2015-2016, VoiceNation LLC
 * @link            http://www.voicenation.com
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Affero General Public License as
 *   published by the Free Software Foundation, either version 3 of the
 *   License, or (at your option) any later version.

 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.

 *   You should have received a copy of the GNU Affero General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?>
<?php
$this->extend('/Common/view');
?>

<link href="http://fonts.googleapis.com/css?family=Lato:300" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=IM+Fell+English" rel="stylesheet" type="text/css">
  
<style type="text/css">
body {
	background-image: url(/img/wallpapers/bg-<?php echo rand(1,3)?>.jpg);
	background-size: cover;
	background-repeat:no-repeat;
	margin:0;
}
.logo {width:100%; height:100px; padding: 40px 0; text-align:center;}
.login {width:100%; height: 100px; text-align:center; clear:both;}

.button {
border-radius: 6px;
border-width: 1px;
border-color: #f7941d;
border-style: solid;
background-color: #f8a21a;
box-shadow: 2px 3px 5px 0px rgba(0, 0, 0, 0.13);
padding: 12px 40px 12px 20px;
font-family: Lato;
color: #ffffff;
font-size: 1.8em;
font-weight: 300;
background-image: url(/img/wallpapers/icon-chevron-right.png);
background-position: right 14px top 12px;
background-repeat: no-repeat;
-moz-animation-duration: 4s;
-moz-animation-delay: 4s;
-webkit-animation-duration: 4s;
-webkit-animation-delay: 4s;
animation-duration: 4s;
animation-delay: 4s;
}


.button:hover {box-shadow:none; text-decoration:none;}
a {text-decoration:none; color: #fff;}
a:hover {text-decoration:underline;}
.button:active {border:thin solid white;}
.quote {
	font-family: 'IM Fell English', Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", serif;
	color: #ffffff;
	font-size: 2.6em;
	font-weight: 700;
	width:50%;
	text-align:center;
	padding:40px 25% 120px 25%;
	line-height:1.1em;
	-moz-animation-duration: 3s;
  	-moz-animation-delay: 1s;
	-webkit-animation-duration: 3s;
  	-webkit-animation-delay: 1s;
	animation-duration: 3s;
	animation-delay: 1s;
}
.footer {
	background: rgba(0,0,0,.5); 
	width:100%;
	font-family: Lato;
	color: #0082cb;
	font-size: 15px;
	font-weight: 300;
	text-align:center;
	padding:14px 0;
	position:absolute;
	bottom:0px;
}

</style>
<link rel="stylesheet" type="text/css" href="/css/lib/animate.css" />

</head>

<body>
<div class="logo animated fadeInDown"><img src="/themes/vn/logo.png" width="403" height="75" alt=""/></div>
<div class="login animated fadeInLeft"><a href="#" onclick="window.open('/OpenAnswer','OpenAnswer', 'toolbar=no, menubar=no,resizable=yes,location=no,directories=no,status=no'); window.close(); return false;" title="log in" class="button">Launch OpenAnswer</a></div>
<div class="quote animated fadeIn"><?php echo $msg['WelcomeMsg']['note']; ?></div>
<div class="footer">&copy; 2014  |  OpenAnswer&reg;  |  Designed &  developed by <a href="http://www.qualityansweringservice.com/openanswer" title="Find out more">VoiceNation, LLC.</a></div>
</body>
</html>
