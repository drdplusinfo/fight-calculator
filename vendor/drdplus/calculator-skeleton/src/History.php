<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use Granam\Strict\Object\StrictObject;

class History extends StrictObject
{
    private StorageInterface $storage;
    private DateTimeProvider $dateTimeProvider;
    private ?int $ttl;

    private ?array $historyValues = null;
    private ?\DateTimeImmutable $ttlDate = null;

    public function __construct(StorageInterface $storage, DateTimeProvider $dateTimeProvider, ?int $ttl)
    {
        $this->storage = $storage;
        $this->ttl = $ttl;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function saveHistory(array $valuesToRemember): void
    {
        $this->loadsHistoryValues(); // loads previous history as they would be overwritten now
        $this->storage->storeValues($valuesToRemember, $this->getTtlDate());
    }

    protected function getTtlDate(): \DateTimeInterface
    {
        if ($this->ttlDate === null) {
            $this->ttlDate = $this->ttl !== null
                ? $this->dateTimeProvider->getNow()->modify('+' . $this->ttl . ' seconds')
                : $this->dateTimeProvider->getNow()->modify('+ 1 year');
        }
        return $this->ttlDate;
    }

    private function loadsHistoryValues(): void
    {
        $this->historyValues = $this->storage->getValues();
    }

    public function deleteHistory(): void
    {
        $this->storage->deleteAll();
        $this->historyValues = [];
    }

    public function getValue(string $name)
    {
        return $this->getHistoryValues()[$name] ?? null;
    }

    private function getHistoryValues(): array
    {
        if ($this->historyValues === null) {
            $this->loadsHistoryValues();
        }
        return $this->historyValues;
    }
}
