<?php

/*
    <!--    <event name="sales_order_invoice_save_after">-->
    <!--        <observer-->
    <!--            name="assign_loyalty_program_to_customer_after_quote_submit_success"-->
    <!--            instance="ZP\LoyaltyProgram\Observer\Quote\Model\QuoteManagement\AssignLoyaltyProgramAfterQuoteSubmitSuccess"-->
    <!--        />-->
    <!--    </event>-->
 * */

//SELECT `main_table`.*, COUNT(`zp_loyalty_program_customer`.`customer_id`) AS `customers_in_program`
//FROM `zp_loyalty_program` AS `main_table`
//LEFT JOIN `zp_loyalty_program_customer` ON main_table.entity_id= zp_loyalty_program_customer.program_id
//WHERE (main_table.`entity_id` NOT IN(1, 2)) GROUP BY main_table.`entity_id` HAVING `customers_in_program` > 1

//$connection->insert(
//    LoyaltyProgramInterface::MAIN_TABLE,
//    [
//        LoyaltyProgramInterface::PROGRAM_ID => 3,
//        LoyaltyProgramInterface::PROGRAM_NAME => 'Bronze',
//        LoyaltyProgramInterface::IS_ACTIVE => 1,
//        LoyaltyProgramInterface::WEBSITE_ID => 1,
//        LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '1,2,3',
//        LoyaltyProgramInterface::ORDER_SUBTOTAL => 1000
//    ]
//);
//
//$connection->insert(
//    LoyaltyProgramInterface::MAIN_TABLE,
//    [
//        LoyaltyProgramInterface::PROGRAM_ID => 4,
//        LoyaltyProgramInterface::PROGRAM_NAME => 'Silver',
//        LoyaltyProgramInterface::IS_ACTIVE => 1,
//        LoyaltyProgramInterface::WEBSITE_ID => 1,
//        LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '1,2,3',
//        LoyaltyProgramInterface::ORDER_SUBTOTAL => 2000
//    ]
//);
//
//$connection->insert(
//    LoyaltyProgramInterface::MAIN_TABLE,
//    [
//        LoyaltyProgramInterface::PROGRAM_ID => 5,
//        LoyaltyProgramInterface::PROGRAM_NAME => 'Gold',
//        LoyaltyProgramInterface::IS_ACTIVE => 1,
//        LoyaltyProgramInterface::WEBSITE_ID => 1,
//        LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '1,2,3',
//        LoyaltyProgramInterface::ORDER_SUBTOTAL => 5000
//    ]
//);
//
//$connection->insert(
//    LoyaltyProgramInterface::MAIN_TABLE,
//    [
//        LoyaltyProgramInterface::PROGRAM_ID => 6,
//        LoyaltyProgramInterface::PROGRAM_NAME => 'Diamant',
//        LoyaltyProgramInterface::IS_ACTIVE => 1,
//        LoyaltyProgramInterface::WEBSITE_ID => 1,
//        LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '1,2,3',
//        LoyaltyProgramInterface::ORDER_SUBTOTAL => 10000
//    ]
//);
//
//$connection->insert(
//    LoyaltyProgramInterface::MAIN_TABLE,
//    [
//        LoyaltyProgramInterface::PROGRAM_ID => 7,
//        LoyaltyProgramInterface::PROGRAM_NAME => 'Platin',
//        LoyaltyProgramInterface::IS_ACTIVE => 1,
//        LoyaltyProgramInterface::WEBSITE_ID => 1,
//        LoyaltyProgramInterface::CUSTOMER_GROUP_IDS => '1,2,3',
//        LoyaltyProgramInterface::ORDER_SUBTOTAL => 20000
//    ]
//);

//            $customerCollection = $this->customerCollectionFactory->create();
//            if (!$this->isAllCustomers($customerIds)) {
//                $customerCollection->addFieldToFilter(
//                    'entity_id',
//                    ['in' => $checkedCustomerIds]
//                );
//                $customerIds = $checkedCustomerIds;
//            }
//
//            /** @var Customer[] $customers */
//            $customers = $customerCollection->getItems();
$string = '';
$arr = explode(',', $string);
var_dump($arr);
