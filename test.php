<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>
 
<body>
<h1>Hi result </h1>
 
<?
 $hostname = "speedo2424.db.10570228.hostedresource.com";
 $username = "speedo2424";
 $dbname = "speedo2424";
 $password="Abhishek@2424";
 
 
 mysql_connect($hostname, $username, $password) OR DIE ("Unable to 
            connect to database! Please try again later.");
            mysql_select_db($dbname);
$query="SELECT * FROM Sales WHERE EmpName='Akash Gupta'";
$result=mysql_query($query);
 
$num=mysql_numrows($result);
 
mysql_close();
 
echo "<b><center>Disclaimer:This report was prepared as a result of PHP codes  TEST RUN only </center></b><br><br>";
 
$i=0;
while ($i < $num) {
 
$EmpCode=mysql_result($result,$i,"EmpCode");
$EmpName=mysql_result($result,$i,"EmpName");
$TeamName=mysql_result($result,$i,"TeamName");
$AnnTarget=mysql_result($result,$i,"AnnTarget");
$BreakEven=mysql_result($result,$i,"BreakEven");
$CurrBand=mysql_result($result,$i,"CurrBand");
$Buss=mysql_result($result,$i,"Buss");
$Coll=mysql_result($result,$i,"Coll");
$AchBuss=mysql_result($result,$i,"AchBuss");
$AchColl=mysql_result($result,$i,"AchColl");
$Buss_Apr=mysql_result($result,$i,"Buss_Apr");
$Buss_May=mysql_result($result,$i,"Buss_May");
$Buss_Jun=mysql_result($result,$i,"Buss_Jun");
$Buss_Jul=mysql_result($result,$i,"Buss_Jul");
$Buss_Aug=mysql_result($result,$i,"Buss_Aug");
$Buss_Sep=mysql_result($result,$i,"Buss_Sep");
$Buss_Oct=mysql_result($result,$i,"Buss_Oct");
$Buss_Nov=mysql_result($result,$i,"Buss_Nov");
$Buss_Dec=mysql_result($result,$i,"Buss_Dec");
$Buss_Jan=mysql_result($result,$i,"Buss_Jan");
$Buss_Feb=mysql_result($result,$i,"Buss_Feb");
$Buss_Mar=mysql_result($result,$i,"Buss_Mar");
$Coll_Apr=mysql_result($result,$i,"Coll_Apr");
$Coll_May=mysql_result($result,$i,"Coll_May");
$Coll_Jun=mysql_result($result,$i,"Coll_Jun");
$Coll_Jul=mysql_result($result,$i,"Coll_Jul");
$Coll_Aug=mysql_result($result,$i,"Coll_Aug");
$Coll_Sep=mysql_result($result,$i,"Coll_Sep");
$Coll_Oct=mysql_result($result,$i,"Coll_Oct");
$Coll_Nov=mysql_result($result,$i,"Coll_Nov");
$Coll_Dec=mysql_result($result,$i,"Coll_Dec");
$Coll_Jan=mysql_result($result,$i,"Coll_Jan");
$Coll_Feb=mysql_result($result,$i,"Coll_Feb");
$Coll_Mar=mysql_result($result,$i,"Coll_Mar");
 
 
 
 
 
 
 
echo "<b>$EmpCode: $EmpName</b><br>TeamName: $TeamName<br>AnnTarget: $AnnTarget<br>BreakEven: $BreakEven<br>CurrBand: $CurrBand<br>Bussiness: $Buss<br>Collection: $Coll<br>AchBuss: $AchBuss<br>AchColl: $AchColl<br>Buss_Apr: $Buss_Apr<br>Buss_May: $Buss_May<br>Buss_Jun: $Buss_Jun<br>Buss_Jul: $Buss_Jul;<br>Buss_Aug: $Buss_Aug<br>Buss_Sep: $Buss_Sep<br>Buss_Oct: $Buss_Oct<br>Buss_Nov: $Buss_Nov<br>Buss_Dec: $Buss_Dec<br>Buss_Jan: $Buss_Jan<br>Buss_Feb: $Buss_Feb<br>Buss_Mar: $Buss_Mar<br>Coll_Apr: $Coll_Apr<br>Coll_May: $Coll_May<br>Coll_Jun: $Coll_Jun<br>Coll_Jul: $Coll_Jul<br>Coll_Aug: $Coll_Aug<br>Coll_Sep: $Coll_Sep<br>Coll_Oct: $Coll_Oct<br>Coll_Nov: $Coll_Nov<br> Coll_Dec: $Coll_Dec<br>Coll_Jan: $Coll_Jan<br>Coll_Feb: $Coll_Feb<br>Coll_Mar: $Coll_Mar<br> ";
 
$i++;
}
 
?>
 
 
 
 
</body>
</html>
 
