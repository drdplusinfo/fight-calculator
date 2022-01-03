<!DOCTYPE html>
<html lang="cs">
  <head>
    <title>Just a test</title>
    <meta charset="utf-8">
  </head>
  <body>
    <div id="<?= \DrdPlus\RulesSkeleton\HtmlHelper::ID_TABLE_OF_CONTENTS ?>">
      <a>Tak tohle to by se mělo změnit na místní odkaz</a>
      <a href="">A tohle to taky</a>
      <a href=" ">Tohle taky ještě</a>
      <a href="#hotovo">Tohle by mělo zůstat beze změny</a>
      <a href="https://www.drdplus.info">A tohle taky beze změny</a>
    </div>
    <h1 id="hotovo">Hotovo</h1>
  </body>
</html>
