<?php

namespace App\formatter;

use Interop\Container\Exception\ContainerException;
use Slim\Http\Response;

/**
 * Formatting data
 *
 * @author Thomas.
 */
class Example extends AbstractFormatter
{
    /**
     * Get Data
     * @return array
     */
    public function getData()
    {
        $params = $this->getArgs();
        return ["Hello World", $params->name ?? ''];
    }

    /**
     * Format datas
     *
     * @param array $data
     * @return Response
     * @throws ContainerException
     */
    public function formatData(array $data)
    {
        if (empty($data) == false) {
            return $this->getFormatResponseOk($data);
        } else {
            return $this->getFormatResponseError('no_data', 'No response');
        }
    }
}
