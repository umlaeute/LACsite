function externalLinks(){if(!document.getElementsByTagName){return}var c=document.getElementsByTagName("a");for(var b=0;b<c.length;b++){var a=c[b];if(a.getAttribute("href")&&a.getAttribute("rel")=="_blank"){a.target="_blank"}if(a.getAttribute("href")&&a.getAttribute("rel")=="external"){a.target="_blank"}if(a.getAttribute("href")&&a.getAttribute("rel")=="wiki"){a.target="lac2011-wiki"}if(a.getAttribute("href")&&a.getAttribute("rel")=="supporter"){a.target="lac2011-sponsor"}if(a.getAttribute("href")&&a.getAttribute("rel")=="registration"){a.target="lac2011-registration"}}}window.onload=externalLinks;function inlineInfoBox(a){document.getElementById("infobox").style.display="inline";document.getElementById("dimmer").style.display="inline";document.getElementById("infobox").scrollTop=0}function showInfoBox(a){if(document.getElementById("ieframe")){document.getElementById("ieframe").src="raw.php?pdb_filterid="+a}else{document.getElementById("infoframe").data="raw.php?pdb_filterid="+a}inlineInfoBox()}function showInfoPix(a){if(document.getElementById("ieframe")){document.getElementById("ieframe").src="pix.php?id="+a}else{document.getElementById("infoframe").data="pix.php?id="+a}inlineInfoBox()}function hideInfoBox(){if(document.getElementById("ieframe")){document.getElementById("ieframe").src="raw.php"}else{document.getElementById("infoframe").data="raw.php"}document.getElementById("infobox").style.display="none";document.getElementById("dimmer").style.display="none"}function formsubmit(a){if(document.getElementById(a)){document.getElementById(a).submit()}}function adminjump(a){if(document.getElementById(a)){window.location.hash=a}}function admingo(a,c,b){document.getElementById("page").value=a;document.getElementById("mode").value=c;document.getElementById("param").value=b;formsubmit("myform")};