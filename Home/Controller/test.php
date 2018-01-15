<?php


  $accountPrice[] = 111;
  $accountPrice[] = 1112.;
  $accountPrice[] = -2324.1;
  $accountPrice[] = 2324.15;
  $accountPrice[] = 2324.157;//wrong
  $accountPrice[] = 0.57;

  foreach ($accountPrice as $k => $v) {

    if (preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $accountPrice[$k])) {
      echo '整数或小数二位的正则<br>';
    } else {
      echo '错: ' . $accountPrice[$k] . '<br>';
    }
  }
?>