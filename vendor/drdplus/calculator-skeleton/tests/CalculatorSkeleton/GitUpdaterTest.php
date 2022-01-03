<?php declare(strict_types=1);

namespace Tests\DrdPlus\CalculatorSkeleton;

use DrdPlus\CalculatorSkeleton\Exceptions\ForbiddenForGitReader;
use DrdPlus\CalculatorSkeleton\GitUpdater;
use Granam\TestWithMockery\TestWithMockery;

class GitUpdaterTest extends TestWithMockery
{

    /**
     * @test
     */
    public function I_can_not_clone_repository_by_reader(): void
    {
        $this->expectException(ForbiddenForGitReader::class);
        $gitUpdater = new GitUpdater();
        $gitUpdater->cloneBranch('foo', 'bar', 'baz');
    }

    /**
     * @test
     */
    public function I_can_not_update_repository_by_reader(): void
    {
        $this->expectException(ForbiddenForGitReader::class);
        $gitReader = new GitUpdater();
        $gitReader->updateBranch('foo', 'bar');
    }
}
