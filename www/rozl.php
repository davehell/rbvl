<?php
/*
rozl.txt
#16
/2011-10-16
1-16 2-15 3-14
7-10 5-12 6-11
...
*/
$casy = array('08:30:00', '09:20:00', '10:10:00', '11:00:00', '12:00:00', '12:50:00', '13:40:00', '14:30:00', '15:20:00', '16:10:00');

$radek = "";
@$f = fopen("rozl2021.txt","r");
while (!feof ($f)) {
  $radek = fgets($f, 4096);

  if($radek{0} == "#") {
    $skupina = trim(substr($radek, 1));
  }
  else if($radek{0} == "/") {
    $datum = trim(substr($radek, 1));
    $cas = 0;
  }
  else {
    $zapasy = explode(" ", $radek);
    $i = 1;
    foreach ($zapasy as $zapas) {
      //preg_match  ("([0-9]{1,2})([A-Z]{1})-([0-9]{1,2})([A-Z]{1})", $zapas, $regs);
      preg_match  ("/([0-9]{1,2})-([0-9]{1,2})/", $zapas, $regs);
      $pozn="kurt " . $i++;
      //(int)$regs[1] < 11 ? $skupina = "17" : $skupina = "18";
      echo 'insert into rozlosovani values (0, '.$skupina.', "'.$datum.'", "'.$casy[$cas].'", "'.$pozn.'", '.$regs[1].', '.$regs[2].");<br>\n";
    }
    $cas++;
  }
}
@fclose ($f);



?>
