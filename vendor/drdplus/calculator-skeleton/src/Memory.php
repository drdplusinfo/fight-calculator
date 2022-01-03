<?php declare(strict_types=1);

namespace DrdPlus\CalculatorSkeleton;

use Granam\Strict\Object\StrictObject;

class Memory extends StrictObject
{
    private StorageInterface $storage;
    private ?int $ttl;
    private DateTimeProvider $dateTimeProvider;

    private ?\DateTimeInterface $ttlDate = null;

    public function __construct(StorageInterface $storage, DateTimeProvider $dateTimeProvider, ?int $ttl)
    {
        $this->storage = $storage;
        $this->dateTimeProvider = $dateTimeProvider;
        $this->ttl = $ttl;
    }

    public function saveMemory(array $valuesToRemember): void
    {
        $this->storage->storeValues($valuesToRemember, $this->getTtlDateTime());
    }

    private function getTtlDateTime(): ?\DateTimeInterface
    {
        if ($this->ttl === null) {
            return null;// end of session
        }
        if ($this->ttlDate === null) {
            $this->ttlDate = $this->dateTimeProvider->getNow()->modify('+ ' . $this->ttl . ' seconds');
        }
        return $this->ttlDate;
    }

    public function deleteMemory(): void
    {
        $this->storage->deleteAll();
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        return $this->storage->getValues()[$name] ?? null;
    }
}
