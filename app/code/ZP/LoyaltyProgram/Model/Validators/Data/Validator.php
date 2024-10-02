<?php
declare(strict_types=1);

namespace ZP\LoyaltyProgram\Model\Validators\Data;

use ZP\LoyaltyProgram\Api\Data\ValidatorInterface;

class Validator implements ValidatorInterface
{
    /**
     * @param mixed $data
     * @return bool
     */
    public function isDataInteger(mixed $data): bool
    {
        if (is_int($data)) {
            return true;
        }

        if (is_string($data) && $data !== '') {
            return $this->isInteger($data);
        }

        return false;
    }

    protected function isInteger(string $data): bool
    {
        return is_numeric($data) && $this->isIntegerInString($data);
    }

    protected function isIntegerInString(string $data): bool
    {
        preg_match('/\./', $data, $matches);

        return !$matches;
    }

    public function validateMultiselectFieldIntData(
        int|string|array|null $data,
        string $fieldName,
        string $entityName
    ): array {
        $result = [];
        if ($data === null || $data === '' || (is_array($data) && !$data)) {
            return $result;
        }

        if ($this->isDataInteger($data)) {
            $result[] = (int)$data;

            return $result;
        }

        if (is_string($data) || is_array($data)) {
            if (is_string($data)) {
                $data = explode(',', $data);
            } elseif (is_array($data)) {
                if (count($data) === 1) {
                    $data = explode(',', array_shift($data));
                }
            }

            foreach ($data as $key => $value) {
                if (!$this->isDataInteger($value)) {
                    $value = $this->getExceptionValues($value);
                    throw new \Exception(
                        'Wrong field \'' . $fieldName . '\' data of ' . $entityName . ' entity!' .
                        'You received $value : \'' . $value . '\' !'
                    );
                }

                $data[$key] = (int)$value;
            }
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @return string
     * @throws \Exception
     */
    public function getExceptionValues(mixed $data): string
    {
        if (is_null($data)) {
            $data = 'NULL';
        } elseif (is_object($data)) {
            $data = 'OBJECT';
        } elseif (is_array($data)) {
            $data = 'ARRAY';
        } elseif (is_bool($data)) {
            $data = 'BOOL';
        } elseif($data === '') {
            $data = 'EMPTY STRING';
        } elseif (is_resource($data)) {
            $data = 'RESOURCE';
        } else {
            $data = (string)$data;
        }

        return $data;
    }
}
