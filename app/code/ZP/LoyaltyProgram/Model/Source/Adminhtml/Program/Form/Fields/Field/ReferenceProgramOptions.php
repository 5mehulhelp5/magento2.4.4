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
        $ninProgramIds = [
            'nin' => [BasicProgramsConfig::PROGRAM_MIN, BasicProgramsConfig::PROGRAM_MAX]
        ];
        if ($programIds) {
            $ninProgramIds['nin'] = array_merge($ninProgramIds['nin'], $programIds);
        }

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            LoyaltyProgram::PROGRAM_ID,
            $ninProgramIds
        );

        return $collection->getItems();
    }

    protected function getData(): array
    {
        $data = [];
        $programs = $this->getProgramCollection();
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

    protected function getDefaultOption(string $type): array
    {
        $label = '';
        switch ($type) {
            case self::REMOVE_OPTION :
                $label = '-- Remove Program --';
                break;
            case self::PLEASE_OPTION :
                $label = '-- Please Select --';

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
