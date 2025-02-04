<?php

namespace Mirko\T3maker\Typo3\TCA\Config\RenderType;

use ReflectionProperty;
use Symfony\Component\Console\Style\SymfonyStyle;

interface ConfigRenderTypeInterface
{
    public static function getTypeName(): string;
    public function askRenderTypeDetails(SymfonyStyle $io): array;
    public function askForConfigPresets(SymfonyStyle $io, ReflectionProperty $property): array;
    public function getExampleConfig(): array;
}
