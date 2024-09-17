<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Source\Adminhtml\Program\Form\Fields\Field;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;
use ZP\LoyaltyProgram\Model\LoyaltyProgram;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\Collection;
use ZP\LoyaltyProgram\Model\ResourceModel\LoyaltyProgram\CollectionFactory;
use ZP\LoyaltyProgram\Setup\Patch\Data\AddBasicPrograms as BasicProgramsConfig;

abstract class ReferenceProgramOptions implements OptionSourceInterface
{
    public const PLEASE_OPTION = 'please_option';
    public const REMOVE_OPTION = 'remove_option';
    public const EMPTY_OPTION = 'empty_option';

    protected ?int $currentProgramId = null;

    public function __construct(
        private CollectionFactory $collectionFactory,
        private RequestInterface $request
    ) {}

    public function toOptionArray()
    {
        return $this->getData();
    }

    private function getProgramCollection(array $programIds = []): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $ninProgramIds = $collection->getNinBasicProgramsFilter();
        if ($programIds) {
            $ninKey = array_key_first($ninProgramIds);
            $ninProgramIds[$ninKey] = array_merge($ninProgramIds[$ninKey], $programIds);
        }
        $collection->addFieldToFilter(
            LoyaltyProgram::PROGRAM_ID,
            $ninProgramIds
        );

        return $collection->getItems();
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getData(): array
    {
        $data = [];
        $programs = $this->getProgramCollection();
        if (!$programs) {
            $data[] = $this->getDefaultOption(self::EMPTY_OPTION);

            return $data;
        }

        if ($this->getProgramId() === null || $this->isBasicProgram($this->currentProgramId)) {
            $data[] = $this->getDefaultOption(self::PLEASE_OPTION);

            return $this->getOptionsData($data, $programs);
        }

        /** @var LoyaltyProgram $currentProgram */
        $currentProgram = $programs[$this->currentProgramId];
        $referenceProgramId = $this->getReferenceProgramId($currentProgram);
        if ($referenceProgramId === null || $this->isBasicProgram($referenceProgramId)) {
            $data[] = $this->getDefaultOption(self::PLEASE_OPTION);

            return $this->getOptionsData($data, $this->getProgramCollection([$this->currentProgramId]));
        }

        /** @var LoyaltyProgram $referenceProgram */
        $referenceProgram = $programs[$referenceProgramId];
        $data[] = ['label' => __($referenceProgram->getProgramName()), 'value' => $referenceProgram->getProgramId()];
        $data[] = $this->getDefaultOption(self::REMOVE_OPTION);

        return $this->getOptionsData($data, $this->getProgramCollection([$this->currentProgramId, $referenceProgramId]));
    }

    protected function getProgramId(): ?int
    {
        $programId = $this->request->getParam(LoyaltyProgramInterface::PROGRAM_ID);
        if ($programId !== null) {
            $this->currentProgramId = (int)$programId;
        }

        return $this->currentProgramId;
    }

    /**
     * @param string $type
     * @return array
     * @throws \Exception
     */
    protected function getDefaultOption(string $type): array
    {
        $label = '';
        switch ($type) {
            case self::REMOVE_OPTION :
                $label = '-- Remove Program --';
                break;
            case self::PLEASE_OPTION :
                $label = '-- Please Select --';
                break;
            case self::EMPTY_OPTION :
                $label = '--Nothing To Select';
                break;
            default:
                throw new \Exception('Unknown option type : ' . '\'' . $type . '\'!');
        }

        return ['label' => __($label), 'value' => '0'];
    }

    /**
     * @param array $dataToReturn
     * @param LoyaltyProgram[] $programsData
     * @return array
     */
    protected function getOptionsData(array $dataToReturn, array $programsData): array
    {
        /** @var LoyaltyProgram $program */
        foreach ($programsData as $program) {
            $dataToReturn[] = ['label' => __($program->getProgramName()), 'value' => $program->getProgramId()];
        }

        return $dataToReturn;
    }

    protected function isBasicProgram(int $programId): bool
    {
        return $programId === BasicProgramsConfig::PROGRAM_MIN || $programId === BasicProgramsConfig::PROGRAM_MAX;
    }

    /**
     * @param LoyaltyProgram $program
     * @return int|null
     */
    abstract protected function getReferenceProgramId(LoyaltyProgram $program): ?int;
}
