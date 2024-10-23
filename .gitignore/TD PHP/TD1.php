<?php
echo "afficher une chaine de caractère";
$entier = 5;
$flottant = 5.5;
$boolen = true;
echo "$entier $flottant $boolen ";
$date_and_time = date("Y-m-d H:i:s"); 
echo "$date_and_time";



if ($entier == 5) {
    echo "peut mieux faire";
} else if ($entier == 18){
    echo "l'ensemble est compris";
} else {
    echo "bof";
}

switch ($entier) {
    case 5:
        echo "peut mieux faire";
        break;
    case 18:
        echo "l'ensemble est compris";
        break;
    default:
        echo "bof";
        break;
}

$k = 0;
while ($k++ <= 100) {
    echo "k = $k, ";
}

for ($i = 1; $i <= 100; $i++) {
    echo "$i, ";
}

$array_associatif = ["jean" => 5, "pierre" => 18, "paul" => 12, "mahmoud" => 'boum'];
$array_index = [5, 18, 12];

for ($i == 0; $i < count($array_index); $i++) {
    echo "$array_index[$i], ";
}

foreach ($array_index as $index) {
    echo $index;
}

foreach ($array_associatif as $key => $value) {
    echo "$key $value";
}

function borne_sup (int $lim): void {
    for ($i = 1; $i < $lim; $i++) {
        echo "$i";
    }
}

function inutile (string $mot): void {
    $maj = strtoupper(string: $mot);
    $shamble = str_shuffle(string: $maj);
    echo $shamble;
}

foreach ($array_associatif as $nom => $note) {
    echo "$nom a obtenu la note $note";
}

