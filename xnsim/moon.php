<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>XNova Game : Полезные утилиты : </title>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" type="text/css" href="/skins/default/formate.css">
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
	</head>
<body>
<SCRIPT type="text/javascript" language="JavaScript">
      function berechne() {
       var N = document.getElementById("N").value;
       var d = document.getElementById("d").value;
       var moonc = Math.round((100 - Math.sqrt(d)) * Math.sqrt(N)*100)/100;
       var ripc = Math.round(Math.sqrt(d)/2*100)/100;
    if(moonc>100)
    moonc=100;
    if(ripc>100)
    ripc=100;
       document.getElementById("moon").innerHTML=moonc+" %";
       document.getElementById("rip").innerHTML=ripc+" %";
      }
    </SCRIPT><br><br><DIV align="center">
      <TABLE border="0" width=300>
        <TR><TD class="c" colspan="2">Расчет уничтожения луны</TD></TR>
        <TR><TH>Диаметр луны:</TH><TH><INPUT id="d" maxlength="4" onkeyup="berechne()" type="text" size="6" value="7777"></TH></TR>
        <TR><TH>Кол-во ЗС:</TH><TH><INPUT id="N" maxlength="5" onkeyup="berechne()" type="text" size="6" value="1"></TH></TR>
        <TR><TH>Шанс уничтожения луны:</TH><TH><DIV id="moon">---</DIV></TH></TR>
        <TR><TH>Шанс взрыва ЗС:</TH><TH><DIV id="rip">---</DIV></TH></TR>
        <TR><TH colspan="2"><INPUT type="button" value="Расчет" onclick="berechne()"></TH></TR>
      </TABLE>
      <SCRIPT language="Javascript" type="text/javascript">
      berechne();
      </SCRIPT>
    </DIV>
</body>
</html>