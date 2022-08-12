<?php

ini_set('display_errors', 1);

error_reporting(E_ALL);

$to = $argv[1];
$project = $argv[2];
$mail_from = 'oxirredutase@oxirredutase.speed.dcc.ufmg.br';
$subject = "Proteus | Your project is ready";

$headers = "From:". $mail_from;


$message = "From: $mail_from 
To: $to 
Suject: $subject 


Hello,

Your project has been successfully finished.

You can access it by the link: 
http://proteus.dcc.ufmg.br/result/id/$project

Thanks for using Proteus.


Sincerely,
Proteus team.";


mail($to, $subject, $message, $headers);
