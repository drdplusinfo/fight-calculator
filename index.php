<?php
namespace DrdPlus\Calculators\Fight;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

$controller = new Controller();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/generic/graphics.css" rel="stylesheet" type="text/css">
    <link href="css/generic/main.css" rel="stylesheet" type="text/css">
    <link href="css/fight.css" rel="stylesheet" type="text/css">
    <link href="css/generic/issues.css" rel="stylesheet" type="text/css">
    <noscript>
        <link href="css/generic/no_script.css" rel="stylesheet" type="text/css">
    </noscript>
</head>
<body>
<div id="fb-root"></div>
<div class="background"></div>
<form class="block delete" action="/" method="post" onsubmit="return window.confirm('Opravdu smazat?')">
    <label>
        <input type="submit" value="Smazat" name="<?= $controller::DELETE_HISTORY ?>">
        <span class="hint">(včetně dlouhodobé paměti)</span>
    </label>
</form>
<form class="block" action="" method="get">
    <input type="hidden" name="<?= $controller::SCROLL_FROM_TOP ?>" id="scrollFromTop"
           value="<?= $controller->getScrollFromTop() ?>">
    <div class="block remember">
        <label><input type="checkbox" name="<?= $controller::REMEMBER_CURRENT ?>" value="1"
                      <?php if ($controller->shouldRemember()) { ?>checked="checked"<?php } ?>>
            Pamatovat <span class="hint">(i při zavření prohlížeče)</span></label>
    </div>
    <div class="block">
        <div class="panel">
            <?php include __DIR__ . '/parts/basic_fight_properties.php' ?>
        </div>
        <div class="panel">
            <h2 id="Na blízko"><a href="#Na blízko" class="inner">Na blízko</a></h2>
            <fieldset class="panel">
                <?php include __DIR__ . '/parts/melee_weapon.php' ?>
            </fieldset>
        </div>
        <div class="panel">
            <h2 id="Na dálku"><a href="#Na dálku" class="inner">Na dálku</a></h2>
            <fieldset class="panel">
                <?php include __DIR__ . '/parts/ranged_weapon.php'; ?>
            </fieldset>
        </div>
        <div class="panel">
            <h2 id="Štít"><a href="#Štít" class="inner">Štít</a></h2>
            <fieldset class="panel">
                <?php include __DIR__ . '/parts/shield.php'; ?>
            </fieldset>
        </div>
        <div class="panel">
            <h2 id="Zbroj"><a href="#Zbroj" class="inner">Zbroj a helma</a></h2>
            <fieldset class="panel">
                <?php include __DIR__ . '/parts/armor_and_helm.php'; ?>
            </fieldset>
        </div>
        <div class="panel">
            <h2 id="Vlastnosti"><a href="#Vlastnosti" class="inner">Vlastnosti</a></h2>
            <fieldset class="panel">
                <?php include __DIR__ . '/parts/profession_and_body_properties.php'; ?>
            </fieldset>
        </div>
        <div class="panel">
            <h2 id="Prostředí"><a href="#Prostředí" class="inner">Prostředí</a></h2>
            <fieldset class="panel">
                <?php include __DIR__ . '/parts/ride_and_animal_enemy.php'; ?>
            </fieldset>
        </div>
    </div>
</form>

<div class="block issues">
    <a href="https://rpgforum.cz/forum/viewtopic.php?f=238&t=14870">
        <img src="images/generic/rpgforum-ico.png">
        Máš nápad 😀? Vidíš chybu 😱?️ Sem s tím!
    </a>
    <a class="float-right" href="https://github.com/jaroslavtyc/drd-plus-fight/"
       title="Fork me on GitHub"><img class="github" src="/images/generic/GitHub-Mark-64px.png"></a>
</div>
<script type="text/javascript" src="js/generic/main.js"></script>
</body>
</html>
