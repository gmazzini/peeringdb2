<?php

// drop port with capacity over 10Tbps and less of 100Mbps
function mylist($mycountry,$mycontinent){
  global $arr1,$arr2,$arr3;
  unset($res);
  $i=0;
  foreach ($arr1["data"] as $elm1){
    if($elm1["status"]=="ok" && (($mycontinent=="" && ($mycountry=="all" || $elm1["country"]==$mycountry)) || ($mycontinent!="" && $mycontinent==$elm1["region_continent"]))){
      $cap4=0;
      $cap6=0;
      unset($asn);
      $asn=array();
      foreach ($arr2["data"] as $elm2){
        if($elm2["status"]=="ok" && $elm1["id"]==$elm2["ix_id"] && $elm2["speed"]<10000000 && $elm2["speed"]>=100){
          if(!empty($elm2["ipaddr4"]))$cap4+=$elm2["speed"];
          if(!empty($elm2["ipaddr6"]))$cap6+=$elm2["speed"];
          if(empty($asn[$elm2["asn"]]))$asn[$elm2["asn"]]=$elm2["speed"];
          else $asn[$elm2["asn"]]+=$elm2["speed"];
        }
      }
      if(count($asn)>0){
        $res[$i]["name"]=$elm1["name"];
        $res[$i]["ases"]=count($asn);
        $res[$i]["v4capacity"]=$cap4;
        $res[$i]["v6capacity"]=$cap6;
        $res[$i]["city"]=$elm1["city"];
        $res[$i]["country"]=$elm1["country"];
        arsort($asn);
        $topasn="";
        $topvasn=0;
        $j=0;
        foreach ($asn as $key3 => $elm3){
          $asnname="";
          foreach ($arr3["data"] as $elm4){
            if($elm4["status"]=="ok" && $elm4["asn"]==$key3){
              $asnname=$elm4["name"];
              break;
            }
          }
          $topasn.=$key3."(".$asnname.")"." ";
          $topvasn+=$elm3;
          if(++$j>9)break;
        }
        $res[$i]["topasn"]=$topasn;
        $res[$i]["topvasn"]=$topvasn;
        $i++;
      }
    }
  }
  return $res;
}

function outtable($res,$filename){
  $myfile=fopen($filename,"w");
  fprintf($myfile,"<LINK REL=StyleSheet HREF=\"style.css\" TYPE=\"text/css\" MEDIA=screen>\n");
  fprintf($myfile,"<a href=\"index.php\">Home</a>\n");
  fprintf($myfile,"<br>Table created on %s GMT<br>\n",gmdate("Y-m-d H:i"));
  fprintf($myfile,"<TABLE BORDER=1>\n");
  fprintf($myfile,"<TR><TH>rank</TH><TH>name</TH><TH>ases</TH><TH>v4capacity</TH><TH>v6capacity</TH><TH>top10asn</TH><TH>top10asncapacity</TH><TH>city</TH><TH>country</TH></TR>\n");
  $i=1;
  foreach ($res as $elm3){
    fprintf($myfile,"<TR>");
    fprintf($myfile,"<TD>%d</TD>",$i);
    fprintf($myfile,"<TD>%s</TD>",$elm3["name"]);
    fprintf($myfile,"<TD>%s</TD>",number_format($elm3["ases"]));
    fprintf($myfile,"<TD>%s</TD>",number_format($elm3["v4capacity"]));
    fprintf($myfile,"<TD>%s</TD>",number_format($elm3["v6capacity"]));
    fprintf($myfile,"<TD>%s</TD>",$elm3["topasn"]);
    fprintf($myfile,"<TD>%s</TD>",number_format($elm3["topvasn"]));
    fprintf($myfile,"<TD>%s</TD>",$elm3["city"]);
    fprintf($myfile,"<TD>%s</TD>",$elm3["country"]);
    fprintf($myfile,"</TR>\n");
    $i++;
  }
  fprintf($myfile,"</TABLE>");
  fclose($myfile);
}

function cmpv4($a,$b){
  return $b["v4capacity"]-$a["v4capacity"];
}

function cmpv6($a,$b){
  return $b["v6capacity"]-$a["v6capacity"];
}

function cmpases($a,$b){
  return $b["ases"]-$a["ases"];
}

$js1=file_get_contents("/home/www/peeringdb.mazzini.org/peeringdb2/ix.my");
$js2=file_get_contents("/home/www/peeringdb.mazzini.org/peeringdb2/netixlan.my");
$js3=file_get_contents("/home/www/peeringdb.mazzini.org/peeringdb2/net.my");
$arr1=json_decode($js1,TRUE);
$arr2=json_decode($js2,TRUE);
$arr3=json_decode($js3,TRUE);

$out1=mylist("IT","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-it.html");

$out1=mylist("DE","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-de.html");

$out1=mylist("NL","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-nl.html");

$out1=mylist("FR","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-fr.html");

$out1=mylist("US","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-us.html");

$out1=mylist("GB","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-gb.html");

$out1=mylist("","Asia Pacific");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-AP.html");

$out1=mylist("","Europe");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-EU.html");

$out1=mylist("","South America");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-SA.html");

$out1=mylist("","North America");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-NA.html");

$out1=mylist("","Africa");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-AF.html");

$out1=mylist("","Middle East");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-ME.html");

$out1=mylist("","Australia");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers-AU.html");

$out1=mylist("all","");
usort($out1,"cmpases");
outtable($out1,"/home/www/peeringdb.mazzini.org/customers.html");
usort($out1,"cmpv4");
outtable($out1,"/home/www/peeringdb.mazzini.org/v4capacity.html");
usort($out1,"cmpv6");
outtable($out1,"/home/www/peeringdb.mazzini.org/v6capacity.html");

?>
