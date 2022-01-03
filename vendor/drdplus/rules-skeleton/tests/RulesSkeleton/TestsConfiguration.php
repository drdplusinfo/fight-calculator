<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\HtmlHelper;
use Tests\DrdPlus\RulesSkeleton\Exceptions\InvalidUrl;
use Tests\DrdPlus\RulesSkeleton\Partials\TestsConfigurationReader;
use Granam\Strict\Object\StrictObject;
use Granam\YamlReader\YamlFileReader;

class TestsConfiguration extends StrictObject implements TestsConfigurationReader
{
    public const LICENCE_BY_ACCESS = '*by access*';
    public const LICENCE_MIT = 'MIT';
    public const LICENCE_PROPRIETARY = 'proprietary';

    public const CAN_HAVE_TABLES = 'can_have_tables';
    public const HAS_TABLES = 'has_tables';
    public const HAS_TABLE_OF_CONTENTS = 'has_table_of_contents';
    public const HAS_TABLES_RELATED_CONTENT = 'has_tables_related_content';
    public const HAS_HEADINGS = 'has_headings';
    public const HAS_AUTHORS = 'has_authors';
    public const HAS_EXTERNAL_ANCHORS_WITH_HASHES = 'has_external_anchors_with_hashes';
    public const HAS_CUSTOM_BODY_CONTENT = 'has_custom_body_content';
    public const HAS_NOTES = 'has_notes';
    public const HAS_IDS = 'has_ids';

    /** @see \DrdPlus\RulesSkeleton\HtmlHelper::CLASS_CALCULATION */
    public const HAS_CALCULATIONS = 'has_calculations';

    /** @see \DrdPlus\RulesSkeleton\HtmlHelper::CLASS_CONTENT */
    public const HAS_MARKED_CONTENT = 'has_marked_content';

    /** @see \DrdPlus\RulesSkeleton\HtmlHelper::CLASS_RESULT */
    public const HAS_MARKED_RESULT = 'has_marked_result';

    public const HAS_ANCHORS_TO_SAME_DOCUMENT = 'has_anchors_to_same_document';
    public const HAS_LOCAL_LINKS = 'has_local_links';
    public const HAS_LINKS_TO_ALTAR = 'has_links_to_altar';
    public const HAS_PROTECTED_ACCESS = 'has_protected_access';
    public const HAS_CHARACTER_SHEET = 'has_character_sheet';
    public const HAS_LINKS_TO_JOURNALS = 'has_links_to_journals';
    public const HAS_LINK_TO_SINGLE_JOURNAL = 'has_link_to_single_journal';
    public const HAS_PDF = 'has_pdf';
    public const HAS_DEBUG_CONTACTS = 'has_debug_contacts';
    public const HAS_DEBUG_CONTACTS_WITH_EMAIL = 'has_debug_contacts_with_email';
    public const DEBUG_CONTACTS_EMAIL = 'debug_contacts_email';
    public const HAS_BUTTONS = 'has_buttons';
    public const HAS_SHOWN_HOME_BUTTON_ON_HOMEPAGE = 'has_shown_home_button_on_homepage';
    public const HAS_SHOWN_HOME_BUTTON_ON_ROUTES = 'has_shown_home_button_on_routes';
    public const HAS_LOCAL_REPOSITORIES = 'has_local_repositories';
    public const ARE_GENERIC_ASSETS_VERSIONED = 'are_generic_assets_versioned';
    public const HAS_VENDOR_DIR_VERSIONED = 'has_vendor_dir_versioned';

    public const SOME_EXPECTED_TABLE_IDS = 'some_expected_table_ids';
    public const LOCAL_TESTING_ADDRESS = 'local_testing_address';
    public const EXPECTED_PUBLIC_URL = 'expected_public_url';
    public const EXPECTED_WEB_NAME = 'expected_web_name';
    public const ALLOWED_CALCULATION_ID_PREFIXES = 'allowed_calculation_id_prefixes';
    public const EXPECTED_PAGE_TITLE = 'expected_page_title';
    public const EXPECTED_GOOGLE_ANALYTICS_ID = 'expected_google_analytics_id';
    public const CAN_BE_BOUGHT_ON_ESHOP = 'can_be_bought_on_eshop';
    public const EXPECTED_ESHOP_URL_REGEXP = 'expected_eshop_url_regexp';
    public const EXPECTED_LICENCE = 'expected_licence';
    public const TOO_SHORT_FAILURE_NAMES = 'too_short_failure_names';
    public const TOO_SHORT_SUCCESS_NAMES = 'too_short_success_names';
    public const TOO_SHORT_RESULT_NAMES = 'too_short_result_names';
    public const HAS_HOME_BUTTON_ON_HOMEPAGE = 'has_home_button_on_homepage';
    public const HAS_HOME_BUTTON_ON_ROUTES = 'has_home_button_on_routes';
    public const EXPECTED_HOME_BUTTON_TARGET = 'expected_home_button_target';
    public const PUBLIC_TO_LOCAL_URL_PART_REGEXP = 'public_to_local_url_part_regexp';
    public const PUBLIC_TO_LOCAL_URL_PART_REPLACEMENT = 'public_to_local_url_part_replacement';

    public static function createFromYaml(string $yamlConfigFile): TestsConfiguration
    {
        return new static((new YamlFileReader($yamlConfigFile))->getValues());
    }

    private bool $canHaveTables = true;
    private bool $hasTables = true;
    private bool $hasTablesRelatedContent = true;
    private array $someExpectedTableIds = [];
    private bool $hasTableOfContents = true;
    private bool $hasHeadings = true;
    private bool $hasAuthors = true;
    private bool $hasExternalAnchorsWithHashes = true;
    private bool $hasCustomBodyContent = true;
    private bool $hasNotes = true;
    private bool $hasIds = true;
    private bool $hasCalculations = true;
    private bool $hasAnchorsToSameDocument = true;
    private bool $hasLocalLinks = true;
    private bool $hasLinksToAltar = true;
    private bool $hasButtons = true;
    private bool $hasShownHomeButtonOnHomepage = true;
    private bool $hasShownHomeButtonOnRoutes = true;
    private bool $hasMarkedContent = true;
    private bool $hasMarkedResult = true;
    private bool $hasLocalRepositories = false;
    private bool $areGenericAssetsVersioned = true;
    private bool $hasVendorDirVersioned = true;

    private ?string $expectedWebName = null;
    private ?string $expectedPageTitle = null;
    private string $expectedGoogleAnalyticsId = 'UA-121206931-1';
    private array $allowedCalculationIdPrefixes = ['Hod proti', 'Hod na', 'Výpočet'];
    private ?string $expectedPublicUrl = null;
    private ?string $localTestingAddress = null;
    private ?string $localUrl = null;
    private bool $hasProtectedAccess = true;
    private bool $hasPdf = true;
    private bool $canBeBoughtOnEshop = true;
    private string $expectedEshopUrlRegexp = '~^https://obchod\.altar\.cz/[^/]+\.html$~';
    private bool $hasCharacterSheet = true;
    private bool $hasLinksToJournals = true;
    private bool $hasLinkToSingleJournal = true;
    private bool $hasDebugContacts = true;
    private bool $hasDebugContactsWithEmail = true;
    private string $debugContactsEmail = 'info@drdplus.info';
    private string $expectedLicence = self::LICENCE_BY_ACCESS;
    private array $tooShortFailureNames = ['nevšiml si'];
    private array $tooShortSuccessNames = ['všiml si'];
    private array $tooShortResultNames = ['Bonus', 'Postih'];
    private bool $hasHomeButtonOnHomepage = true;
    private bool $hasHomeButtonOnRoutes = true;
    private string $expectedHomeButtonTarget = 'https://www.drdplus.info';
    private ?string $publicToLocalUrlPartRegexp = null;
    private ?string $publicToLocalUrlReplacement = null;

    /**
     * @param array $values
     * @throws \Tests\DrdPlus\RulesSkeleton\Exceptions\InvalidLocalUrl
     * @throws \Tests\DrdPlus\RulesSkeleton\Exceptions\InvalidPublicUrl
     * @throws \Tests\DrdPlus\RulesSkeleton\Exceptions\PublicUrlShouldUseHttps
     */
    public function __construct(array $values)
    {
        $this->setCanHaveTables($values);
        $this->setHasTables($values, $this->canHaveTables());
        $this->setHasTablesRelatedContent($values, $this->hasTables());
        $this->setSomeExpectedTableIds($values, $this->hasTables());
        $this->setHasTableOfContents($values);
        $this->setHasHeadings($values);
        $this->setHasAuthors($values);
        $this->setExpectedPublicUrl($values);
        $this->setLocalTestingAddress($values);
        $this->setLocalUrl($this->getLocalTestingAddress());
        $this->setHasExternalAnchorsWithHashes($values);
        $this->setHasCustomBodyContent($values);
        $this->setHasNotes($values);
        $this->setHasIds($values);
        $this->setHasAnchorsToSameDocument($values);
        $this->setHasLocalLinks($values);
        $this->setHasLinksToAltar($values);
        $this->setExpectedWebName($values);
        $this->setAllowedCalculationIdPrefixes($values);
        $this->setExpectedPageTitle($values);
        $this->setExpectedGoogleAnalyticsId($values);
        $this->setHasProtectedAccess($values);
        $this->setHasPdf($values);
        $this->setCanBeBoughtOnEshop($values);
        $this->setExpectedEshopUrlRegexp($values, $this->canBeBoughtOnEshop());
        $this->setHasDebugContacts($values);
        $this->setHasDebugContactsWithMail($values);
        $this->setDebugContactsEmail($values, $this->hasDebugContactsWithEmail());
        $this->setExpectedLicence($values);
        $this->setHasCharacterSheet($values);
        $this->setHasLinksToJournals($values);
        $this->setHasLinkToSingleJournal($values);
        $this->setTooShortFailureNames($values);
        $this->setTooShortSuccessNames($values);
        $this->setTooShortResultNames($values);
        $this->setHasCalculations($values);
        $this->setHasShownHomeButtonOnHomepage($values);
        $this->setHasShownHomeButtonOnRoutes($values);
        $this->setHasButtons($values);
        $this->setHasMarkedContent($values);
        $this->setHasMarkedResult($values);
        $this->setHasLocalRepositories($values);
        $this->setGenericAssetsVersioned($values);
        $this->setHasVendorDirVersioned($values);
        $this->setHasHomeButtonOnHomepage($values);
        $this->setHasHomeButtonOnRoutes($values);
        $this->setExpectedHomeButtonTargetFromRoutes($values);
        $this->setPublicTLocalUrlPartRegexp($values);
        $this->setPublicToLocalUrlReplacement($values);
    }

    private function setCanHaveTables(array $values): void
    {
        $this->canHaveTables = (bool)($values[self::CAN_HAVE_TABLES] ?? $this->canHaveTables);
    }

    private function setHasTables(array $values, bool $canHaveTables): void
    {
        if (!$canHaveTables) {
            $this->hasTables = false;
            return;
        }
        $this->hasTables = (bool)($values[self::HAS_TABLES] ?? $this->hasTables);
    }

    private function setHasTablesRelatedContent(array $values, bool $hasTables): void
    {
        if (!$hasTables) {
            $this->hasTablesRelatedContent = false;
            return;
        }
        $this->hasTablesRelatedContent = (bool)($values[self::HAS_TABLES_RELATED_CONTENT] ?? $this->hasTablesRelatedContent);
    }

    public function canHaveTables(): bool
    {
        return $this->canHaveTables;
    }

    public function hasTables(): bool
    {
        return $this->hasTables;
    }

    public function hasTablesRelatedContent(): bool
    {
        return $this->hasTablesRelatedContent;
    }

    /**
     * @param array $values
     * @param bool $hasTables
     * @throws \Tests\DrdPlus\RulesSkeleton\Exceptions\MissingSomeExpectedTableIdsInTestsConfiguration
     */
    private function setSomeExpectedTableIds(array $values, bool $hasTables): void
    {
        if (!$hasTables) {
            $this->someExpectedTableIds = [];

            return;
        }
        $someExpectedTableIds = $values[self::SOME_EXPECTED_TABLE_IDS] ?? null;
        if (!\is_array($someExpectedTableIds)) {
            throw new Exceptions\MissingSomeExpectedTableIdsInTestsConfiguration(
                sprintf(
                    "Expected some '%s' as tests config says by '%s'. Got %s",
                    self::SOME_EXPECTED_TABLE_IDS,
                    self::HAS_TABLES,
                    $someExpectedTableIds === null
                        ? 'nothing'
                        : var_export($someExpectedTableIds, true)
                )
            );
        }
        $structureOk = true;
        foreach ($someExpectedTableIds as $someExpectedTableId) {
            if (!\is_string($someExpectedTableId)) {
                $structureOk = false;
                break;
            }
        }
        if (!$structureOk) {
            throw new Exceptions\MissingSomeExpectedTableIdsInTestsConfiguration(
                "Expected flat array of strings for '" . self::SOME_EXPECTED_TABLE_IDS . "', got "
                . var_export($someExpectedTableIds, true)
            );
        }
        $this->someExpectedTableIds = $someExpectedTableIds;
    }

    private function setHasTableOfContents(array $values): void
    {
        $this->hasTableOfContents = (bool)($values[self::HAS_TABLE_OF_CONTENTS] ?? true);
    }

    private function setHasHeadings(array $values): void
    {
        $this->hasHeadings = (bool)($values[self::HAS_HEADINGS] ?? $this->hasHeadings);
    }

    public function getSomeExpectedTableIds(): array
    {
        return $this->someExpectedTableIds;
    }

    public function hasTableOfContents(): bool
    {
        return $this->hasTableOfContents;
    }

    public function hasHeadings(): bool
    {
        return $this->hasHeadings;
    }

    private function setHasAuthors(array $values): void
    {
        $this->hasAuthors = (bool)($values[self::HAS_AUTHORS] ?? $this->hasAuthors);
    }

    public function hasAuthors(): bool
    {
        return $this->hasAuthors;
    }

    private function setExpectedPublicUrl(array $values)
    {
        $expectedPublicUrl = trim($values[self::EXPECTED_PUBLIC_URL] ?? '');
        try {
            $this->guardValidUrl($expectedPublicUrl);
        } catch (InvalidUrl $invalidUrl) {
            throw new Exceptions\InvalidPublicUrl(
                sprintf(
                    "Given expected public URL under key '%s' is not valid: '%s'",
                    self::EXPECTED_PUBLIC_URL,
                    $expectedPublicUrl
                ),
                $invalidUrl->getCode(),
                $invalidUrl
            );
        }
        if (!str_starts_with($expectedPublicUrl, 'https://')) {
            throw new Exceptions\PublicUrlShouldUseHttps("Given public URL should use HTTPS: '$expectedPublicUrl'");
        }
        $this->expectedPublicUrl = $expectedPublicUrl;
    }

    /**
     * @param string $url
     * @throws \Tests\DrdPlus\RulesSkeleton\Exceptions\InvalidUrl
     */
    private function guardValidUrl(string $url): void
    {
        if (!filter_var($url, \FILTER_VALIDATE_URL, \FILTER_FLAG_HOSTNAME)) {
            throw new Exceptions\InvalidUrl("Given URL is not valid: '$url'");
        }
    }

    private function setLocalUrl(string $localTestingUrl)
    {
        $localUrl = "http://$localTestingUrl";
        $this->guardValidUrl($localUrl);
        $this->localUrl = $localUrl;
    }

    private function setLocalTestingAddress(array $values)
    {
        $localTestingAddress = trim($values[self::LOCAL_TESTING_ADDRESS] ?? 'localhost:9999');
        if (!$localTestingAddress || preg_match('~^[a-z]*://~', $localTestingAddress)) {
            throw new Exceptions\InvalidLocalUrl(
                "Given local testing address should not use a protocol as `php -S {address}` refuses that: '$localTestingAddress'"
            );
        }
        $this->localTestingAddress = $localTestingAddress;
    }

    private function setHasExternalAnchorsWithHashes(array $values)
    {
        $this->hasExternalAnchorsWithHashes = (bool)($values[self::HAS_EXTERNAL_ANCHORS_WITH_HASHES] ?? $this->hasExternalAnchorsWithHashes);
    }

    private function setHasCustomBodyContent(array $values)
    {
        $this->hasCustomBodyContent = (bool)($values[self::HAS_CUSTOM_BODY_CONTENT] ?? $this->hasCustomBodyContent);
    }

    private function setHasNotes(array $values)
    {
        $this->hasNotes = (bool)($values[self::HAS_NOTES] ?? $this->hasNotes);
    }

    private function setHasIds(array $values)
    {
        $this->hasIds = (bool)($values[self::HAS_IDS] ?? $this->hasIds);
    }

    private function setHasCalculations(array $values)
    {
        $this->hasCalculations = (bool)($values[self::HAS_CALCULATIONS] ?? $this->hasCalculations);
    }

    private function setHasShownHomeButtonOnHomepage(array $values)
    {
        $this->hasShownHomeButtonOnHomepage = (bool)($values[self::HAS_SHOWN_HOME_BUTTON_ON_HOMEPAGE] ?? $this->hasShownHomeButtonOnHomepage);
    }

    private function setHasShownHomeButtonOnRoutes(array $values)
    {
        $this->hasShownHomeButtonOnRoutes = (bool)($values[self::HAS_SHOWN_HOME_BUTTON_ON_ROUTES] ?? $this->hasShownHomeButtonOnRoutes);
    }

    private function setHasButtons(array $values)
    {
        $this->hasButtons = (bool)($values[self::HAS_BUTTONS] ?? $this->hasButtons);
    }

    private function setHasMarkedContent(array $values)
    {
        $this->hasMarkedContent = (bool)($values[self::HAS_MARKED_CONTENT] ?? $this->hasMarkedContent);
    }

    private function setHasMarkedResult(array $values)
    {
        $this->hasMarkedResult = (bool)($values[self::HAS_MARKED_RESULT] ?? $this->hasMarkedResult);
    }

    private function setHasLocalRepositories(array $values)
    {
        $this->hasLocalRepositories = (bool)($values[self::HAS_LOCAL_REPOSITORIES] ?? $this->hasLocalRepositories);
    }

    private function setGenericAssetsVersioned(array $values)
    {
        $this->areGenericAssetsVersioned = (bool)($values[self::ARE_GENERIC_ASSETS_VERSIONED] ?? $this->areGenericAssetsVersioned);
    }

    private function setHasVendorDirVersioned(array $values)
    {
        $this->hasVendorDirVersioned = (bool)($values[self::HAS_VENDOR_DIR_VERSIONED] ?? $this->hasVendorDirVersioned);
    }

    private function setHasAnchorsToSameDocument(array $values)
    {
        $this->hasAnchorsToSameDocument = (bool)($values[self::HAS_ANCHORS_TO_SAME_DOCUMENT] ?? $this->hasAnchorsToSameDocument);
    }

    private function setHasLocalLinks(array $values)
    {
        $this->hasLocalLinks = (bool)($values[self::HAS_LOCAL_LINKS] ?? $this->hasLocalLinks);
    }

    private function setHasLinksToAltar(array $values)
    {
        $this->hasLinksToAltar = (bool)($values[self::HAS_LINKS_TO_ALTAR] ?? $this->hasLinksToAltar);
    }

    private function setExpectedWebName(array $values)
    {
        $expectedWebName = trim($values[self::EXPECTED_WEB_NAME] ?? '');
        if ($expectedWebName === '') {
            throw new Exceptions\MissingExpectedWebName('Expected some web name under key ' . self::EXPECTED_WEB_NAME);
        }
        $this->expectedWebName = $expectedWebName;
    }

    private function setAllowedCalculationIdPrefixes(array $values)
    {
        if (!isset($values[self::ALLOWED_CALCULATION_ID_PREFIXES])) {
            return;
        }
        $this->allowedCalculationIdPrefixes = [];
        foreach ($values[self::ALLOWED_CALCULATION_ID_PREFIXES] as $allowedCalculationIdPrefix) {
            if (!\preg_match('~^[[:upper:]]~u', $allowedCalculationIdPrefix)) {
                throw new Exceptions\AllowedCalculationPrefixShouldStartByUpperLetter(
                    "First letter of allowed calculation prefix should be uppercase, got '$allowedCalculationIdPrefix'"
                );
            }
            $this->allowedCalculationIdPrefixes[] = $allowedCalculationIdPrefix;
        }
    }

    private function setExpectedPageTitle(array $values)
    {
        $expectedPageTitle = trim($values[self::EXPECTED_PAGE_TITLE] ?? '');
        if ($expectedPageTitle === '') {
            throw new Exceptions\MissingExpectedPageTitle('Expected some page title under key ' . self::EXPECTED_PAGE_TITLE);
        }
        $this->expectedPageTitle = $expectedPageTitle;
    }

    private function setExpectedGoogleAnalyticsId(array $values)
    {
        $expectedGoogleAnalyticsId = trim($values[self::EXPECTED_GOOGLE_ANALYTICS_ID] ?? '');
        if ($expectedGoogleAnalyticsId === '') {
            throw new Exceptions\MissingExpectedGoogleAnalyticsId('Expected some Google analytics ID under key ' . self::EXPECTED_GOOGLE_ANALYTICS_ID);
        }
        $this->expectedGoogleAnalyticsId = $expectedGoogleAnalyticsId;
    }

    private function setHasProtectedAccess(array $values)
    {
        $this->hasProtectedAccess = (bool)($values[self::HAS_PROTECTED_ACCESS] ?? $this->hasProtectedAccess);
    }

    private function setHasPdf(array $values)
    {
        $this->hasPdf = (bool)($values[self::HAS_PDF] ?? $this->hasPdf);
    }

    private function setCanBeBoughtOnEshop(array $values)
    {
        $this->canBeBoughtOnEshop = (bool)($values[self::CAN_BE_BOUGHT_ON_ESHOP] ?? $this->canBeBoughtOnEshop);
    }

    private function setExpectedEshopUrlRegexp(array $values, bool $canBeBoughtOnEshop)
    {
        $expectedEshopUrlRegexp = (string)($values[self::EXPECTED_ESHOP_URL_REGEXP] ?? $this->expectedEshopUrlRegexp);
        if ($canBeBoughtOnEshop && !preg_match('~^(.).+\1$~', $expectedEshopUrlRegexp)) {
            throw new Exceptions\InvalidExpectedEshopUrlRegexp(
                sprintf("Expected valid regexp with same opening and ending character, got '%s'", $values[self::EXPECTED_ESHOP_URL_REGEXP])
            );
        }
        $this->expectedEshopUrlRegexp = $expectedEshopUrlRegexp;
    }

    private function setHasDebugContacts(array $values)
    {
        $this->hasDebugContacts = (bool)($values[self::HAS_DEBUG_CONTACTS] ?? $this->hasDebugContacts);
    }

    private function setHasDebugContactsWithMail(array $values)
    {
        $this->hasDebugContactsWithEmail = (bool)($values[self::HAS_DEBUG_CONTACTS_WITH_EMAIL] ?? $this->hasDebugContactsWithEmail);
    }

    private function setDebugContactsEmail(array $values, bool $hasDebugContactsWithEmail)
    {
        $debugContactsEmail = (string)($values[self::DEBUG_CONTACTS_EMAIL] ?? $this->debugContactsEmail);
        if ($hasDebugContactsWithEmail && !filter_var($debugContactsEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exceptions\InvalidDebugContactsEmail("Expected valid email for debut contacts, got '$debugContactsEmail'");
        }
        $this->debugContactsEmail = $debugContactsEmail;
    }

    private function setExpectedLicence(array $values)
    {
        $this->expectedLicence = (string)($values[self::EXPECTED_LICENCE] ?? $this->expectedLicence);
    }

    private function setHasCharacterSheet(array $values)
    {
        $this->hasCharacterSheet = (bool)($values[self::HAS_CHARACTER_SHEET] ?? $this->hasCharacterSheet);
    }

    private function setHasLinksToJournals(array $values)
    {
        $this->hasLinksToJournals = (bool)($values[self::HAS_LINKS_TO_JOURNALS] ?? $this->hasLinksToJournals);
    }

    private function setHasLinkToSingleJournal(array $values)
    {
        $this->hasLinkToSingleJournal = (bool)($values[self::HAS_LINK_TO_SINGLE_JOURNAL] ?? $this->hasLinkToSingleJournal);
    }

    private function setTooShortFailureNames(array $values)
    {
        if (!isset($values[self::TOO_SHORT_FAILURE_NAMES])) {
            return;
        }
        $this->tooShortFailureNames = [];
        foreach ($values[self::TOO_SHORT_FAILURE_NAMES] as $tooShortFailureName) {
            if (!\in_array($tooShortFailureName, $this->tooShortFailureNames, true)) {
                $this->tooShortFailureNames[] = $tooShortFailureName;
            }
        }
    }

    private function setTooShortSuccessNames(array $values)
    {
        if (!isset($values[self::TOO_SHORT_SUCCESS_NAMES])) {
            return;
        }
        $this->tooShortSuccessNames = [];
        foreach ($values[self::TOO_SHORT_SUCCESS_NAMES] as $tooShortSuccessName) {
            if (!\in_array($tooShortSuccessName, $this->tooShortSuccessNames, true)) {
                $this->tooShortSuccessNames[] = $tooShortSuccessName;
            }
        }
    }

    private function setTooShortResultNames(array $values)
    {
        if (!isset($values[self::TOO_SHORT_RESULT_NAMES])) {
            return;
        }
        $this->tooShortResultNames = [];
        foreach ($values[self::TOO_SHORT_RESULT_NAMES] as $tooShortResultName) {
            if (!\in_array($tooShortResultName, $this->tooShortResultNames, true)) {
                $this->tooShortResultNames[] = $tooShortResultName;
            }
        }
    }

    private function setHasHomeButtonOnHomepage(array $values)
    {
        $this->hasHomeButtonOnHomepage = $values[self::HAS_HOME_BUTTON_ON_HOMEPAGE]
            ?? $this->hasHomeButtonOnHomepage;
    }

    private function setHasHomeButtonOnRoutes(array $values)
    {
        $this->hasHomeButtonOnRoutes = $values[self::HAS_HOME_BUTTON_ON_ROUTES]
            ?? $this->hasHomeButtonOnRoutes;
    }

    private function setExpectedHomeButtonTargetFromRoutes(array $values)
    {
        $this->expectedHomeButtonTarget = $values[self::EXPECTED_HOME_BUTTON_TARGET] ?? $this->expectedHomeButtonTarget;
    }

    private function setPublicTLocalUrlPartRegexp(array $values)
    {
        $publicToLocalUrlPartRegexp = $values[self::PUBLIC_TO_LOCAL_URL_PART_REGEXP] ?? null;
        if (!$publicToLocalUrlPartRegexp || !preg_match('~^(.).+\1$~', $publicToLocalUrlPartRegexp)) {
            throw new Exceptions\InvalidPublicUrlPartRegexp(
                sprintf(
                    "Expected valid regexp, got %s for tests configuration key '%s'",
                    var_export($publicToLocalUrlPartRegexp, true),
                    self::PUBLIC_TO_LOCAL_URL_PART_REGEXP
                )
            );
        }
        $this->publicToLocalUrlPartRegexp = $publicToLocalUrlPartRegexp;
    }

    private function setPublicToLocalUrlReplacement(array $values)
    {
        $publicToLocalUrlReplacement = $values[self::PUBLIC_TO_LOCAL_URL_PART_REPLACEMENT] ?? null;
        if (!is_string($publicToLocalUrlReplacement) || $publicToLocalUrlReplacement === '') {
            throw new Exceptions\InvalidPublicUrlPartRegexpReplacement(
                sprintf(
                    "Expected some valid replacement for a public-to-local regexp match, got %s for tests configuration key '%s'",
                    var_export($publicToLocalUrlReplacement, true),
                    self::PUBLIC_TO_LOCAL_URL_PART_REPLACEMENT
                )
            );
        }
        $this->publicToLocalUrlReplacement = $publicToLocalUrlReplacement;
    }

    public function getExpectedPublicUrl(): string
    {
        return $this->expectedPublicUrl;
    }

    public function getLocalTestingAddress(): string
    {
        return $this->localTestingAddress;
    }

    public function getLocalUrl(): string
    {
        return $this->localUrl;
    }

    public function hasExternalAnchorsWithHashes(): bool
    {
        return $this->hasExternalAnchorsWithHashes;
    }

    public function hasCustomBodyContent(): bool
    {
        return $this->hasCustomBodyContent;
    }

    public function hasNotes(): bool
    {
        return $this->hasNotes;
    }

    public function hasIds(): bool
    {
        return $this->hasIds;
    }

    public function hasCalculations(): bool
    {
        return $this->hasCalculations;
    }

    public function hasButtons(): bool
    {
        return $this->hasButtons;
    }

    public function hasMarkedContent(): bool
    {
        return $this->hasMarkedContent;
    }

    public function hasMarkedResult(): bool
    {
        return $this->hasMarkedResult;
    }

    public function hasLocalRepositories(): bool
    {
        return $this->hasLocalRepositories;
    }

    public function areGenericAssetsVersioned(): bool
    {
        return $this->areGenericAssetsVersioned;
    }

    public function hasVendorDirVersioned(): bool
    {
        return $this->hasVendorDirVersioned;
    }

    public function hasShownHomeButtonOnHomepage(): bool
    {
        return $this->hasShownHomeButtonOnHomepage;
    }

    public function hasShownHomeButtonOnRoutes(): bool
    {
        return $this->hasShownHomeButtonOnRoutes;
    }

    public function hasAnchorsToSameDocument(): bool
    {
        return $this->hasAnchorsToSameDocument;
    }

    public function hasLocalLinks(): bool
    {
        return $this->hasLocalLinks;
    }

    public function hasLinksToAltar(): bool
    {
        return $this->hasLinksToAltar;
    }

    public function getExpectedWebName(): string
    {
        return $this->expectedWebName;
    }

    public function getExpectedPageTitle(): string
    {
        return $this->expectedPageTitle;
    }

    public function getExpectedGoogleAnalyticsId(): string
    {
        return $this->expectedGoogleAnalyticsId;
    }

    /** @return array|string[] */
    public function getAllowedCalculationIdPrefixes(): array
    {
        return $this->allowedCalculationIdPrefixes;
    }

    public function hasProtectedAccess(): bool
    {
        return $this->hasProtectedAccess;
    }

    public function hasPdf(): bool
    {
        return $this->hasPdf;
    }

    public function canBeBoughtOnEshop(): bool
    {
        return $this->canBeBoughtOnEshop;
    }

    public function getExpectedEshopUrlRegexp(): string
    {
        return $this->expectedEshopUrlRegexp;
    }

    public function hasCharacterSheet(): bool
    {
        return $this->hasCharacterSheet;
    }

    public function hasLinksToJournals(): bool
    {
        return $this->hasLinksToJournals;
    }

    public function hasLinkToSingleJournal(): bool
    {
        return $this->hasLinkToSingleJournal;
    }

    public function hasDebugContacts(): bool
    {
        return $this->hasDebugContacts;
    }

    public function hasDebugContactsWithEmail(): bool
    {
        return $this->hasDebugContactsWithEmail;
    }

    public function getDebugContactsEmail(): string
    {
        return $this->debugContactsEmail;
    }

    public function getExpectedLicence(): string
    {
        if ($this->expectedLicence !== self::LICENCE_BY_ACCESS) {
            return $this->expectedLicence;
        }

        return $this->hasProtectedAccess()
            ? self::LICENCE_PROPRIETARY
            : self::LICENCE_MIT;
    }

    /** @return array|string[] */
    public function getTooShortFailureNames(): array
    {
        return $this->tooShortFailureNames;
    }

    /** @return array|string[] */
    public function getTooShortSuccessNames(): array
    {
        return $this->tooShortSuccessNames;
    }

    /** @return array|string[] */
    public function getTooShortResultNames(): array
    {
        return $this->tooShortResultNames;
    }

    public function hasHomeButtonOnHomepage(): bool
    {
        return $this->hasHomeButtonOnHomepage;
    }

    public function hasHomeButtonOnRoutes(): bool
    {
        return $this->hasHomeButtonOnRoutes;
    }

    public function getExpectedHomeButtonTarget(): string
    {
        return $this->expectedHomeButtonTarget;
    }

    public function getPublicToLocalUrlPartRegexp(): string
    {
        return $this->publicToLocalUrlPartRegexp;
    }

    public function getPublicToLocalUrlReplacement(): string
    {
        return $this->publicToLocalUrlReplacement;
    }
}
