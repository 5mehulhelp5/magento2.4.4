<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model;

use Magento\Framework\Api\SearchResults;
use ZP\LoyaltyProgram\Api\Data\LoyaltyProgramSearchResultsInterface;

/**
 * Service Data Object with LoyaltyProgram search results.
 */
class LoyaltyProgramSearchResults extends SearchResults implements LoyaltyProgramSearchResultsInterface
{

}
