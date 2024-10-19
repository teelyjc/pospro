<?php

namespace Partial;

function Header(string $name)
{
?>
  <!DOCTYPE html>
  <html lang="th">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name ?> - PosPro</title>

    <link href="/includes/css/bootstrap.css" rel="stylesheet" />
    <link href="/includes/css/style.css" rel="stylesheet">
  </head>

  <body>
  <?php
}
  ?>
