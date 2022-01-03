<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Partials;

interface TestsConfigurationReader
{
    public function canHaveTables(): bool;

    public function hasTables(): bool;

    public function getLocalTestingAddress(): string;

    public function getLocalUrl(): string;

    public function hasTablesRelatedContent(): bool;

    public function getSomeExpectedTableIds(): array;

    public function hasExternalAnchorsWithHashes(): bool;

    public function hasCustomBodyContent(): bool;

    public function hasNotes(): bool;

    public function hasIds(): bool;

    public function hasCalculations(): bool;

    public function hasButtons(): bool;

    public function hasShownHomeButtonOnHomepage(): bool;

    public function hasShownHomeButtonOnRoutes(): bool;

    public function hasMarkedContent(): bool;

    public function hasMarkedResult(): bool;

    public function hasLocalRepositories(): bool;

    public function areGenericAssetsVersioned(): bool;

    public function hasVendorDirVersioned(): bool;

    public function hasAnchorsToSameDocument(): bool;

    public function hasLocalLinks(): bool;

    public function hasLinksToAltar();

    public function getExpectedWebName(): string;

    public function getExpectedPageTitle(): string;

    public function getExpectedGoogleAnalyticsId(): string;

    public function getAllowedCalculationIdPrefixes(): array;

    public function hasHeadings(): bool;

    public function getExpectedPublicUrl(): string;

    public function hasProtectedAccess(): bool;

    public function hasPdf(): bool;

    public function canBeBoughtOnEshop(): bool;

    public function getExpectedEshopUrlRegexp(): string;

    public function hasCharacterSheet(): bool;

    public function hasLinksToJournals(): bool;

    public function hasLinkToSingleJournal(): bool;

    public function hasDebugContacts(): bool;

    public function hasDebugContactsWithEmail(): bool;

    public function getDebugContactsEmail(): string;

    public function hasAuthors(): bool;

    public function getExpectedLicence(): string;

    public function getTooShortFailureNames(): array;

    public function getTooShortSuccessNames(): array;

    public function getTooShortResultNames(): array;

    public function hasTableOfContents(): bool;

    public function hasHomeButtonOnHomepage(): bool;

    public function hasHomeButtonOnRoutes(): bool;

    public function getExpectedHomeButtonTarget(): string;

    public function getPublicToLocalUrlPartRegexp(): string;

    public function getPublicToLocalUrlReplacement(): string;

}
