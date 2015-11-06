<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

class ResponseHelper
{
    /**
     * Is this response successful?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isOk($response)
    {
        return $response->getStatusCode() === 200;
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isRedirect($response)
    {
        return in_array($response->getStatusCode(), [301, 302, 303, 307]);
    }

    /**
     * @param ResponseInterface $response
     * @param int               $from
     * @param int               $to
     * @return bool
     */
    private static function isStatusCodeInRange($response, $from, $to)
    {
        return $response->getStatusCode() >= $from && $response->getStatusCode() < $to;
    }

    /**
     * Is this response a client error?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isInformational($response)
    {
        return self::isStatusCodeInRange($response, 100, 200);
    }

    /**
     * Is this response a success?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isSuccess($response)
    {
        return self::isStatusCodeInRange($response, 200, 300);
    }

    /**
     * Is this response a redirection?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isRedirection($response)
    {
        return self::isStatusCodeInRange($response, 300, 400);
    }

    /**
     * Is this response a client error?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isClientError($response)
    {
        return self::isStatusCodeInRange($response, 400, 500);
    }

    /**
     * Is this response a server error?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isServerError($response)
    {
        return self::isStatusCodeInRange($response, 500, 600);
    }

    /**
     * Is this response an error, (either of client or server error)?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isError($response)
    {
        return self::isStatusCodeInRange($response, 400, 600);
    }

    /**
     * gets a header's Location value if set.
     *
     * @param ResponseInterface $response
     * @return string
     */
    public static function getLocation($response)
    {
        $locations = $response->getHeader('Location');
        if (!empty($locations)) {
            return $locations[0];
        }

        return null;
    }
}