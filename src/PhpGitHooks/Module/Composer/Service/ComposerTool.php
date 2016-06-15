<?php

namespace PhpGitHooks\Module\Composer\Service;

use PhpGitHooks\Module\Composer\Contract\Exception\ComposerFilesNotFoundException;
use PhpGitHooks\Module\Files\Contract\Query\ComposerFilesExtractorQuery;
use PhpGitHooks\Module\Files\Contract\QueryHandler\ComposerFilesExtractorQueryHandler;
use PhpGitHooks\Module\Git\Contract\Response\BadJobLogoResponse;
use PhpGitHooks\Module\Git\Service\PreCommitOutputWriter;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerTool
{
    const CHECKING_MESSAGE = 'Checking composer files';
    /**
     * @var PreCommitOutputWriter
     */
    private $outputMessage;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var ComposerFilesExtractorQueryHandler
     */
    private $composerFilesExtractorQueryHandler;

    /**
     * ComposerTool constructor.
     *
     * @param ComposerFilesExtractorQueryHandler $composerFilesExtractorQueryHandler
     * @param OutputInterface                    $output
     */
    public function __construct(
        ComposerFilesExtractorQueryHandler $composerFilesExtractorQueryHandler,
        OutputInterface $output
    ) {
        $this->output = $output;
        $this->composerFilesExtractorQueryHandler = $composerFilesExtractorQueryHandler;
    }

    /**
     * @param array  $files
     * @param string $errorMessage
     *
     * @throws ComposerFilesNotFoundException
     */
    public function execute(array $files, $errorMessage)
    {
        $composerFilesResponse = $this->getComposerFilesResponse($files);

        $this->outputMessage = new PreCommitOutputWriter(self::CHECKING_MESSAGE);
        if (true === $composerFilesResponse->isExists()) {
            $this->output->write($this->outputMessage->getMessage());

            $this->executeTool(
                $composerFilesResponse->isJsonFile(),
                $composerFilesResponse->isLockFile(),
                $errorMessage
            );
            $this->output->writeln($this->outputMessage->getSuccessfulMessage());
        }
    }

    /**
     * @param bool   $jsonFile
     * @param bool   $lockFile
     * @param string $errorMessage
     *
     * @throws ComposerFilesNotFoundException *
     */
    private function executeTool($jsonFile, $lockFile, $errorMessage)
    {
        if (true === $jsonFile && true === $lockFile) {
            return;
        }
        
        $this->output->writeln($this->outputMessage->getFailMessage());
        $this->output->writeln(BadJobLogoResponse::paint($errorMessage));
        throw new ComposerFilesNotFoundException();
    }

    /**
     * @param array $files
     *
     * @return \Module\Files\Contract\Response\ComposerFilesResponse
     */
    private function getComposerFilesResponse(array $files)
    {
        return $this->composerFilesExtractorQueryHandler
            ->handle(new ComposerFilesExtractorQuery($files));
    }
}