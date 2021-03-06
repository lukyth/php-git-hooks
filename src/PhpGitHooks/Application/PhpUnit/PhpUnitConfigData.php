<?php

namespace PhpGitHooks\Application\PhpUnit;

use PhpGitHooks\Application\Config\AbstractToolConfigData;

final class PhpUnitConfigData extends AbstractToolConfigData
{
    const TOOL = 'phpunit';
    const ENABLED_KEY = 'enabled';
    const RANDOM_MODE_KEY = 'random-mode';
    const DEFAULT_ANSWER = 'Y';

    /**
     * @param array $data
     *
     * @return array
     *
     * @throws PhpUnitConfigDataException
     */
    public function createConfigData(array $data)
    {
        $this->configData = $data;
        $this->setEnabled();
        $this->setRandomizerOption();

        return $this->configData[self::TOOL];
    }

    /**
     * @throws PhpUnitConfigDataException
     */
    private function checkConfigData()
    {
        $configData = $this->configData[self::TOOL];
        if (!is_array($configData)) {
            throw new PhpUnitConfigDataException();
        }

        if (!isset($configData[self::ENABLED_KEY])) {
            throw new PhpUnitConfigDataException();
        }
    }

    /**
     * @return string
     */
    protected function getToolName()
    {
        return self::TOOL;
    }

    private function setRandomizerOption()
    {
        if (!isset($this->configData[self::TOOL][self::RANDOM_MODE_KEY])) {
            $answer = $this
                ->setQuestion(
                    sprintf('Do you want run %s tool in randomize mode?', strtoupper(self::TOOL)),
                    self::DEFAULT_ANSWER,
                    'Y/n'
                );
            $answer = self::DEFAULT_ANSWER === strtoupper($answer) ? true : false;
        } else {
            $answer = $this->configData[self::TOOL][self::RANDOM_MODE_KEY];
        }

        $this->configData[self::TOOL][self::RANDOM_MODE_KEY] = $answer;
    }

    private function setEnabled()
    {
        if (!isset($this->configData[self::TOOL])) {
            $answer = $this->setQuestion(
                sprintf('Do you want enable %s tool: ', strtoupper(self::TOOL)),
                self::DEFAULT_ANSWER,
                'Y/n'
            );
            $answer = self::DEFAULT_ANSWER === strtoupper($answer) ? true : false;
        } else {
            $this->checkConfigData();
            $answer = $this->configData[self::TOOL][self::ENABLED_KEY];
        }

        $this->enableTool($answer);
    }
}
