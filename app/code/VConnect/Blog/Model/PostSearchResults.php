<?php
declare(strict_types=1);

namespace VConnect\Blog\Model;

use    Magento\Framework\Api\SearchResults;
use VConnect\Blog\Api\Data\PostSearchResultsInterface;

/**
 * Service Data Object with Post search results.
 */
class PostSearchResults extends SearchResults implements PostSearchResultsInterface
{

}
