<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Package
 */

namespace Zend\Crypt;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Math extends Math\BigInteger
{
    /**
     * Generate a pseudorandom number within the given range.
     * Will attempt to read from a systems RNG if it exists or else utilises
     * a simple random character to maximum length process. Simplicity
     * is a factor better left for development...
     *
     * @param string|int $minimum
     * @param string|int $maximum
     * @return string
     */
    public function rand($minimum, $maximum)
    {
        if (file_exists('/dev/urandom')) {
            $frandom = fopen('/dev/urandom', 'r');
            if ($frandom !== false) {
                return fread($frandom, strlen($maximum) - 1);
            }
        }
        if (strlen($maximum) < 4) {
            return mt_rand($minimum, $maximum - 1);
        }
        $rand = '';
        $i2 = strlen($maximum) - 1;
        for ($i = 1;$i < $i2;$i++) {
            $rand .= mt_rand(0,9);
        }
        $rand .= mt_rand(0,9);
        return $rand;
    }

    /**
     * Get the big endian two's complement of a given big integer in
     * binary notation
     *
     * @param string $long
     * @return string
     */
    public function btwoc($long)
    {
        if (ord($long[0]) > 127) {
            return "\x00" . $long;
        }
        return $long;
    }

    /**
     * Translate a binary form into a big integer string
     *
     * @param string $binary
     * @return string
     */
    public function fromBinary($binary)
    {
        return $this->_math->binaryToInteger($binary);
    }

    /**
     * Translate a big integer string into a binary form
     *
     * @param string $integer
     * @return string
     */
    public function toBinary($integer)
    {
        return $this->_math->integerToBinary($integer);
    }
}
