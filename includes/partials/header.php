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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  </head>

  <body>
  <?php
}
  ?>
