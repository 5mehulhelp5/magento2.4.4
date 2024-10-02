<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Validators\Controller\Adminhtml\Program\MassAction;

use Psr\Log\LoggerInterface;
use ZP\LoyaltyProgram\Model\Validators\Controller\Adminhtml\Program\Validator as BaseValidator;
use ZP\LoyaltyProgram\Api\Model\Validators\Controller\Adminhtml\Program\MassAction\ValidatorInterface;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

class Validator extends BaseValidator implements ValidatorInterface
{
    public function __construct(private LoggerInterface $logger)
    {}

    /**
     * @param array $programIds
     * @return array
     * @throws \Exception
     */
    public function validateProgramIds(array $programIds): array
    {
        foreach ($programIds as $key => $programId) {
            try {
                $programIds[$key] = $this->validateProgramId($programId);
            } catch (\Exception $exception) {
                $this->logger->notice($exception->getMessage());
                unset($programIds[$key]);
            }
        }

        if (!$programIds) {
            throw new \Exception(
                'Program Ids must be an integer or integer in quotes!'
            );
        }

        return $programIds;
    }

    /**
     * @param array $programIds
     * @param string $actionType
     * @throws \Exception
     */
    public function checkProgramIds(array $programIds, string $actionType): void
    {
        if (array_key_exists(BasicProgramsConfig::PROGRAM_MIN, $programIds)) {
            unset($programIds[BasicProgramsConfig::PROGRAM_MIN]);
        }

        if (array_key_exists(BasicProgramsConfig::PROGRAM_MAX, $programIds)) {
            unset($programIds[BasicProgramsConfig::PROGRAM_MAX]);
        }

        if (!$programIds) {
            switch ($actionType) {
                case 'delete' :
                case 'edit' :
                    break;
                default :
                    throw new \Exception('Unknown $actionType: \'' . $actionType . '\'!');
            }

            throw new \Exception(
                'Someone tried to ' . $actionType . ' only Basic Programs, which are forbidden to ' .
                strtoupper($actionType) . '!'
            );
        }
    }
}
