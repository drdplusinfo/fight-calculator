<?php
namespace DrdPlus\Fight;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

/** @noinspection PhpUnusedLocalVariableInspection */
$controller = new Controller();
?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/main.css" rel="stylesheet" type="text/css">
    <link href="css/socials.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="js/facebook.js" async></script>
</head>
<body>
<div id="fb-root"></div>
<form class="block delete" action="" method="post" onsubmit="return window.confirm('Opravdu smazat?')">
    <label>
        <input type="submit" value="Smazat" name="<?= $controller::DELETE_FIGHT_HISTORY ?>">
        <span class="hint">(včetně dlouhodobé paměti)</span>
    </label>
</form>
<form class="block" action="" method="get">
    <div class="block remember">
        <label><input type="checkbox" name="<?= $controller::REMEMBER ?>" value="1"
                      <?php if ($controller->shouldRemember()) { ?>checked="checked"<?php } ?>>
            Pamatovat <span class="hint">(i při zavření prohlížeče)</span></label>
    </div>
    <div class="block">
        <?php $fightProperties = $controller->getRangedFightProperties() ?>
        <div>Boj: <?= $fightProperties->getFight() ?> <span class="hint">(není ovlivněn výzbrojí)</span></div>
        <div>Útok: <?= $fightProperties->getAttack() ?> <span class="hint">(není ovlivněn výzbrojí)</span></div>
        <div>Obrana: <?= $fightProperties->getDefense() ?> <span class="hint">(není ovlivněna výzbrojí)</span></div>
        <div>Obranné číslo: <?= $fightProperties->getDefenseNumber() ?> <span class="hint">(je ovlivněno pouze akcí, oslněním a chybějící Převahou)</span>
        </div>
    </div>
    <div class="block">
        <h2 id="Na blízko"><a href="#Na blízko" class="inner">Na blízko</a></h2>
        <div class="panel">
            <?php include __DIR__ . '/melee_weapon.php' ?>
        </div>
    </div>
    <div class="block">
        <h2 id="Na dálku"><a href="#Na dálku" class="inner">Na dálku</a></h2>
        <div class="panel">
            <?php include __DIR__ . '/ranged_weapon.php'; ?>
        </div>
    </div>
    <div class="block">
        <h2 id="Štít"><a href="#Štít" class="inner">Štít</a></h2>
        <?php include __DIR__ . '/shield.php'; ?>
    </div>
    <div class="block">
        <h2 id="Zbroj"><a href="#Zbroj" class="inner">Zbroj</a></h2>
        <div class="block"><?php include __DIR__ . '/armor.php'; ?></div>
    </div>
    <div class="block"><?php include __DIR__ . '/properties.php'; ?></div>
</form>
<div class="block issues">
    <a href="https://github.com/jaroslavtyc/drd-plus-fight/issues">Máš nápad 😀? Vidíš chybu 😱?️ Sem s tím!</a>
</div>
<div class="block">
    <div class="fb-like facebook"
         data-href="https://boj.drdplus.info/<?= $_SERVER['QUERY_STRING'] ? ('?' . $_SERVER['QUERY_STRING']) : '' ?>"
         data-layout="button" data-action="recommend"
         data-size="small" data-show-faces="false" data-share="true"></div>
    <a href="https://github.com/jaroslavtyc/drd-plus-fight/"
       title="Fork me on GitHub"><img class="github" src="/images/GitHub-Mark-64px.png"></a>
</div>
</body>
</html>
