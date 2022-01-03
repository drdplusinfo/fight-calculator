<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\HtmlHelper;
use DrdPlus\RulesSkeleton\Web\DebugContacts\DebugContactsBody;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Gt\Dom\Element;

class ContactsContentTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function Proper_facebook_link_is_used_in_debug_contacts(): void
    {
        self::assertStringContainsString(
            '"https://www.facebook.com/drdplus.info"',
            $this->getDebugContactsContent(),
            'Link to facebook.com/drdplus.info has not been found in debug contacts template'
        );
    }

    private function getDebugContactsContent(): string
    {
        static $debugContactsContent;
        if ($debugContactsContent === null) {
            $debugContactsBodyClass = $this->getDebugContactsBodyClass();
            /** @var DebugContactsBody $debugContactsBody */
            $debugContactsBody = new $debugContactsBodyClass();
            $debugContactsContent = $debugContactsBody->getValue();
        }

        return $debugContactsContent;
    }

    /**
     * @return string|DebugContactsBody
     */
    protected function getDebugContactsBodyClass(): string
    {
        return DebugContactsBody::class;
    }

    /**
     * @test
     */
    public function Proper_rpg_forum_link_is_used_in_debug_contacts(): void
    {
        self::assertStringContainsString(
            '"https://rpgforum.cz/forum/viewtopic.php?f=238&t=14870"',
            $this->getDebugContactsContent(),
            'Link to RPG forum has not been found in debug contacts template'
        );
    }

    /**
     * @test
     */
    public function I_can_use_mail_to_link_to_drdplus_info_email(): void
    {
        $debugContactsElement = $this->getDebugContactsElement();
        if (!$this->getTestsConfiguration()->hasDebugContacts()) {
            self::assertNull(
                $debugContactsElement,
                sprintf(
                    "Debug contacts have not been expected as test configuration says by '%s'",
                    TestsConfiguration::HAS_DEBUG_CONTACTS
                )
            );

            return;
        }
        $this->guardDebugContactsAreNotEmpty($debugContactsElement);
        $anchors = $debugContactsElement->getElementsByTagName('a');
        self::assertGreaterThan(0, $anchors->count(), 'No anchors found in debug contacts');
        $mailTo = null;
        foreach ($anchors as $anchor) {
            $href = (string)$anchor->getAttribute('href');
            if (!$href || \strpos($href, 'mailto:') !== 0) {
                continue;
            }
            $mailTo = $href;
        }
        if (!$this->getTestsConfiguration()->hasDebugContactsWithEmail()) {
            self::assertNull(
                $mailTo,
                sprintf(
                    "Expected no 'mailto:' in debug contacts as test configuration says by '%s':\n%s",
                    TestsConfiguration::HAS_DEBUG_CONTACTS_WITH_EMAIL,
                    $debugContactsElement->prop_get_outerHTML()
                )
            );
        } else {
            self::assertNotEmpty(
                $mailTo,
                sprintf(
                    "Expected 'mailto:' in debug contacts as test configuration says by '%s' and '%s':\n%s",
                    TestsConfiguration::HAS_DEBUG_CONTACTS,
                    TestsConfiguration::HAS_DEBUG_CONTACTS_WITH_EMAIL,
                    $debugContactsElement->prop_get_outerHTML()
                )
            );
            self::assertSame('mailto:' . $this->getTestsConfiguration()->getDebugContactsEmail(), $mailTo);
        }
    }

    private function getDebugContactsElement(): ?Element
    {
        return $this->getHtmlDocument()->getElementById($this->getDebugContactsId());
    }

    private function getDebugContactsId(): string
    {
        return HtmlHelper::toId(HtmlHelper::ID_DEBUG_CONTACTS);
    }

    private function guardDebugContactsAreNotEmpty(?Element $debugContactsElement): void
    {
        self::assertNotEmpty($debugContactsElement, 'Debug contacts has not been found by ID debug_contacts (debugContacts)');
        self::assertNotEmpty($debugContactsElement->textContent, 'Debug contacts are empty');
    }

    /**
     * @test
     */
    public function I_am_not_confused_by_link_to_debug_contacts_element()
    {
        $debugContactsElement = $this->getDebugContactsElement();
        self::assertNotEmpty($debugContactsElement, 'Debug contacts are missing');
        $anchors = $debugContactsElement->getElementsByTagName('a');
        $debugContactsLocalLink = '#' . $this->getDebugContactsId();
        foreach ($anchors as $anchor) {
            $href = $anchor->href;
            self::assertNotSame(
                $debugContactsLocalLink,
                $href,
                sprintf(
                    "Internal link to debug contacts in contacts itself by '%s' is devastating user experience. Tag debug contacts element by '%s' class:\n'%s'",
                    $debugContactsLocalLink,
                    HtmlHelper::CLASS_WITHOUT_ANCHOR_TO_ID,
                    $debugContactsElement->prop_get_outerHTML()
                )
            );
        }
    }
}
