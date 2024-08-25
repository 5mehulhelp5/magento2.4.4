<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Ui\Component\LoyaltyProgram\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramInterface;

class ProgramActions extends Column
{
    /**
     * Url path
     */
    public const URL_PATH_EDIT = 'loyalty_program/program/edit';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[LoyaltyProgramInterface::PROGRAM_ID])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    LoyaltyProgramInterface::PROGRAM_ID => $item[LoyaltyProgramInterface::PROGRAM_ID]
                                ]
                            ),
                            'label' => __('Edit')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
