<?php
require_once("ISKombat/ISKombat.php");

$isCombat = new ISKombat();

for($i=0; $i<3; $i++) { //делаем 3 шажка вправо
    $isCombat->move(0, "Fighter1" ,"right");
    echo "шагВправо ";
}
$isCombat->move(0, "Fighter1", "left"); echo "шагВлево ";
$isCombat->setState(0, "Fighter1", "DOWN"); echo "полежим \n";
print_r($isCombat); //проверяем
