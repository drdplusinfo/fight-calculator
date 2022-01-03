<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Web\DebugContacts;

use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\DebugContacts\DebugContactsBody;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Gt\Dom\HTMLDocument;

class DebugContactsBodyTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_get_debug_contacts_content()
    {
        $debugContactsBody = new DebugContactsBody();
        $html = <<<HTML
        <!DOCTYPE html>
<html lang="cs">
<head>
  <title>Just a test</title>
  <meta charset="utf-8">
</head>
<body>
  $debugContactsBody
</body>
</html>
HTML;
        $HTMLDocument = new HTMLDocument($html);
        $debugContactsId = HtmlHelper::toId(HtmlHelper::ID_DEBUG_CONTACTS);
        $debugContacts = $HTMLDocument->getElementById($debugContactsId);
        self::assertNotEmpty($debugContacts);
        $anchors = $debugContacts->getElementsByTagName('a');
        self::assertGreaterThan(0, $anchors->count());
    }
}
