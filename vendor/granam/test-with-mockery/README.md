# Mockery test helper

No more mocked non-existing methods after a method name change.

```php
class MyTest extends \Granam\Tests\TestWithMockery {
    
    public function testMyClass() {
        $myClassMock = $this->mockery(Sos::class);
        $myClassMock->expects('saveMe') // saveMe() no more exists and \Granam\Tests\Exceptions\MockingOfNonExistingMethod is thrown
            ->andReturn(true);
    }
}

class Sos {
    
    // renamed previous saveMe()
    public function saveUs(): bool {
        return false;
    }
}
```

## Installation
```bash
composer require --dev granam/test-with-mockery
```