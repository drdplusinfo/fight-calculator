<?php declare(strict_types=1);
/** @var $menuConfiguration \DrdPlus\RulesSkeleton\Configurations\MenuConfiguration */
$fixed = $menuConfiguration->isPositionFixed()
    ? 'fixed'
    : '';
$homeButton = '';
/** @var $homepageDetector \DrdPlus\RulesSkeleton\HomepageDetector */
if (($menuConfiguration->getHomeButtonConfiguration()->isShownOnHomePage() && $homepageDetector->isHomepageRequested())
    || ($menuConfiguration->getHomeButtonConfiguration()->isShownOnRoutes() && !$homepageDetector->isHomepageRequested())
) {
    $homeButtonId = \DrdPlus\RulesSkeleton\HtmlHelper::ID_HOME_BUTTON;
    $homeButton = <<<HTML
<span class="home-menu">
    <a id="{$homeButtonId}" class="internal-url" href="{$menuConfiguration->getHomeButtonConfiguration()->getTarget()}">
        <img class="home" alt="Small dragon menu" src="/images/generic/skeleton/drdplus-dragon-menu-2x22.png">
    </a>
</span>
HTML;
}
?>

<div class="contacts visible top permanent <?= $fixed ?>" id="menu">
  <div class="container">
      <?= $homeButton ?>
      <?php
      $contactHtml = '';
      foreach ($menuConfiguration->getItems() as $key => $item) {
          $key = trim((string)$key);
          $item = trim($item);
          switch ($key) {
              case 'email' :
                  $email = $item;
                  if (strpos($item, 'mailto:') !== 0) {
                      $item = 'mailto:' . $item;
                  } else {
                      $email = preg_replace('~^mailto:~', '', $item);
                  }
                  $escapedEmail = htmlentities($email);
                  $contactHtml = <<<HTML
<a href="{$item}">
  <span class="small-screen"><i class="fas fa-envelope"></i></span>
  <span class="wide-screen"><i class="fas fa-envelope"></i> {$escapedEmail}</span>
</a>
HTML;
                  break;
              case 'rpgforum' :
                  $contactHtml = <<<HTML
<a target="_blank" class="rpgforum-contact" href="{$item}">
  <span class="small-screen"><i class="fas fa-dice-six"></i></span>
  <span class="wide-screen"><i class="fas fa-dice-six"></i> RPG fórum</span>
</a>
HTML;
                  break;
              case 'discord' :
                  $contactHtml = <<<HTML
<a target="_blank" class="discord-contact" href="{$item}">
  <span class="small-screen"><i class="fab fa-discord"></i></span>
  <span class="wide-screen"><i class="fab fa-discord"></i> Discord</span>
</a>
HTML;
                  break;
              case 'twitter' :
                  $contactHtml = <<<HTML
<a target="_blank" class="twitter-contact" href="{$item}">
  <span class="small-screen"><i class="fab fa-twitter-square"></i></span>
  <span class="wide-screen"><i class="fab fa-twitter-square"></i> Twitter</span>
</a>
HTML;
                  break;
              case 'facebook' :
                  $contactHtml = <<<HTML
<a target="_blank" class="facebook-contact" href="{$item}">
  <span class="small-screen"><i class="fab fa-facebook-square"></i></span>
  <span class="wide-screen"><i class="fab fa-facebook-square"></i> Facebook</span>
</a>
HTML;
                  break;
              case 'trello' :
                  $contactHtml = <<<HTML
<a target="_blank" class="trello-contact" href="{$item}">
  <span class="small-screen"><i class="fab fa-trello"></i></span>
  <span class="wide-screen"><i class="fab fa-trello"></i> Trello</span>
</a>
HTML;
                  break;
              default :
                  throw new \DrdPlus\RulesSkeleton\Web\Exceptions\UnrecognizedMenuItemKey(
                      sprintf("Unexpected menu item key '%s' (with value '%s')", $key, $item)
                  );
          } ?>
        <span class="contact"><?= $contactHtml ?></span>
      <?php } ?>
  </div>
</div>
<div class="contacts-placeholder invisible">
  Placeholder for contacts
</div>
