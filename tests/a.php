 <html>
 <head>
 <script type="text/javascript" src="http://www.aishangfei.net/Public/js/jquery-1.8.3.min.js"></script>
 
 </head>
 <body>
 <div id="right_content">111</div>
 <script type='text/javascript' language='javascript'>
 $.getJSON("http://www.aishangfei.net/s.php?callback=?","k=bj",function(data){
	alert(data.code);
	alert(data.code.r[0][0].city);
 })
 </script>
 </body>
</html>