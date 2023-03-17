<?php

declare(strict_types=1);


namespace Mirko\T3maker\Typo3\TCA\Config\RenderType;

use Mirko\T3maker\Typo3\TCA\Config\ReusablePropertiesQuestionFactory;

class InputDefault extends AbstractConfigRenderType implements DefaultRenderTypeInterface
{
    protected array $availableConfigProperties = [
        ReusablePropertiesQuestionFactory::CONFIG_PROPERTY_SIZE,
        ReusablePropertiesQuestionFactory::CONFIG_PROPERTY_READ_ONLY,
        ReusablePropertiesQuestionFactory::CONFIG_PROPERTY_PLACEHOLDER,
        ReusablePropertiesQuestionFactory::CONFIG_PROPERTY_DEFAULT,
        ReusablePropertiesQuestionFactory::CONFIG_PROPERTY_EVAL,
    ];
}