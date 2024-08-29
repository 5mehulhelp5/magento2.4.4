<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Ui\Component\LoyaltyProgram\Form;

use Magento\Ui\Component\Form\Field as ComponentField;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

class Field extends ComponentField
{
    public function prepare()
    {
        parent::prepare();

        $programId = (int)$this->context->getRequestParam(LoyaltyProgramInterface::PROGRAM_ID);
        if (in_array($programId,[BasicProgramsConfig::PROGRAM_MIN, BasicProgramsConfig::PROGRAM_MAX])) {
            $config = $this->getData('config');
            $config['disabled'] = true;
            $this->setData('config', $config);
        }
    }
}
