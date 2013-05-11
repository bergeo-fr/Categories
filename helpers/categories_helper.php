<?php
if(!function_exists('rand_string'))
 {
    function rand_string($length = 10)
    {
     $chars = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz0123456789';
     $max = strlen($chars)-1;
     $string = '';
     mt_srand((double)microtime() * 1000000);
     while (strlen($string) < $length)
     {
      $string .= $chars{mt_rand(0, $max)};
     }
     return $string;
    }
 } 