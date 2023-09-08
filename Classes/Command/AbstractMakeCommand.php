<?php

declare(strict_types=1);

namespace Mirko\T3maker\Command;

use LogicException;
use Mirko\T3maker\Generator\Generator;
use Mirko\T3maker\Maker\MakerInterface;
use Mirko\T3maker\Utility\Typo3Utility;
use Mirko\T3maker\Validator\ClassValidator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

abstract class AbstractMakeCommand extends Command
{
    protected SymfonyStyle $io;

    protected string $extensionName = '';

    protected string $extensionPath = '';

    public function __construct(
        protected MakerInterface $maker,
        protected Generator $generator,
        string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     *
     * @throws LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ext_list = array_filter(
            ExtensionManagementUtility::getLoadedExtensionListArray(),
            fn ($ext) => !str_ends_with(ExtensionManagementUtility::extPath($ext), '/sysext/' . $ext . '/')
        );

        $question = new Question('Name for which extension command will be executed');
        // todo: Remove modules from autocomplete installed from composer in v12.
        $question->setAutocompleterValues($ext_list);
        $question->setValidator(function (string $answer) use ($ext_list): string {
            if (!Typo3Utility::isExtensionLoaded($answer)) {
                throw new RuntimeException('Given extensions [ ' . $answer . ' ] not found in the system.');
            }
            if (!in_array($answer, $ext_list)) {
                throw new RuntimeException('Given extensions [ ' . $answer . ' ] is part of the typo3 core.');
            }

            return $answer;
        });

        $this->extensionName = $this->io->askQuestion($question);
        $this->addArgument(
            'extensionName',
            InputArgument::REQUIRED,
            'Name for which extension command will be executed',
        );
        $input->setArgument('extensionName', $this->extensionName);
        /* if ($this->generator->hasPendingOperations()) {
             throw new LogicException('Make sure to call the writeChanges() method on the generator.');
         }*/

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        /* $extensionName = $input->getArgument('extensionName');
           if ($extensionName) {
               return;
           }

           $argument = $this->getDefinition()->getArgument('extensionName');
           $question = $this->createClassQuestion($argument->getDescription());
           $extensionName = $this->io->askQuestion($question);

           $input->setArgument('extensionName', $extensionName);*/
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function createClassQuestion(string $questionText): Question
    {
        $question = new Question($questionText);
        $question->setValidator([ClassValidator::class, 'notEmpty']);

        return $question;
    }
}
