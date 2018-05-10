<?php
namespace DrdPlus\Calculator\Fight;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

/** @noinspection PhpUnusedLocalVariableInspection */
$controller = new Controller();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/generic/vendor/bootstrap.4.0.0/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
    <link href="css/generic/vendor/bootstrap.4.0.0/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
    <link href="css/generic/vendor/bootstrap.4.0.0/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/generic/graphics.css" rel="stylesheet" type="text/css">
    <link href="css/generic/skeleton.css" rel="stylesheet" type="text/css">
    <link href="css/generic/issues.css" rel="stylesheet" type="text/css">
    <link href="css/attack/attack-skeleton.css" rel="stylesheet" type="text/css">
    <link href="css/fight.css" rel="stylesheet" type="text/css">
    <noscript>
      <link href="css/generic/no_script.css" rel="stylesheet" type="text/css">
    </noscript>
  </head>
  <body class="container">
    <div class="background"></div>
      <?php include __DIR__ . '/vendor/drd-plus/calculator-skeleton/history_deletion.php' ?>
    <div class="row">
      <hr class="col">
    </div>
    <form action="" method="get">
        <?php
        include __DIR__ . '/parts/basic_fight_properties.php';
        include __DIR__ . '/parts/profession_and_body_properties.php';
        include __DIR__ . '/parts/melee_weapon.php';
        include __DIR__ . '/parts/ranged_weapon.php';
        include __DIR__ . '/parts/shield.php';
        include __DIR__ . '/parts/armor_and_helm.php';
        ?>
      <div class="col">
        <h2 id="Prostředí"><a href="#Prostředí" class="inner">Prostředí</a></h2>
        <fieldset>
            <?php include __DIR__ . '/parts/ride_and_animal_enemy.php'; ?>
        </fieldset>
      </div>
    </form>
      <?php
      /** @noinspection PhpUnusedLocalVariableInspection */
      $sourceCodeUrl = 'https://github.com/jaroslavtyc/drd-plus-fight';
      include __DIR__ . '/vendor/drd-plus/calculator-skeleton/issues.php' ?>
    <script type="text/javascript" src="js/generic/skeleton.js"></script>
  </body>
</html>
