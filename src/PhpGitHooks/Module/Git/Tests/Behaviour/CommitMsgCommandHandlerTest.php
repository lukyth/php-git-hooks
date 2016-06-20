<?php

namespace PhpGitHooks\Module\Git\Tests\Behaviour;

use PhpGitHooks\Module\Configuration\Contract\Query\ConfigurationDataFinderQuery;
use PhpGitHooks\Module\Configuration\Tests\Stub\ConfigurationDataResponseStub;
use PhpGitHooks\Module\Git\Contract\Command\CommitMsgCommand;
use PhpGitHooks\Module\Git\Contract\CommandHandler\CommitMsgCommandHandler;
use PhpGitHooks\Module\Git\Contract\Exception\InvalidCommitMessageException;
use PhpGitHooks\Module\Git\Service\CommitMsgTool;
use PhpGitHooks\Module\Git\Tests\Infrastructure\GitUnitTestCase;

class CommitMsgCommandHandlerTest extends GitUnitTestCase
{
    /**
     * @var CommitMsgCommandHandler
     */
    private $commitMsgCommandHandler;

    protected function setUp()
    {
        $this->commitMsgCommandHandler = new CommitMsgCommandHandler(
            new CommitMsgTool(
                $this->getMergeValidator(),
                $this->getQueryBus(),
                $this->getCommitMessageFinder()
            )
        );
    }

    /**
     * @test
     */
    public function itShouldNotExecuteTool()
    {
        $configurationDataResponse = ConfigurationDataResponseStub::createCustom(true, false);

        $this->shouldHandleQuery(new ConfigurationDataFinderQuery(), $configurationDataResponse);

        $this->commitMsgCommandHandler->handle(new CommitMsgCommand($this->getInput()));
    }

    /**
     * @test
     */
    public function itShouldThrowsException()
    {
        $this->expectException(InvalidCommitMessageException::class);

        $configurationDataResponse = ConfigurationDataResponseStub::createCustom(true, true);

        $this->shouldHandleQuery(new ConfigurationDataFinderQuery(), $configurationDataResponse);
        $this->shouldGetInputFirstArgument('file');
        $this->shouldFindCommitMessage('file', 'invalid commit message');
        $this->shouldCallIsMerge(false);

        $this->commitMsgCommandHandler->handle(new CommitMsgCommand($this->getInput()));
    }
}