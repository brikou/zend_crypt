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

use Zend\Crypt\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Crypt
{
    const TYPE_OPENSSL = 'openssl';
    const TYPE_HASH    = 'hash';
    const TYPE_MHASH   = 'mhash';

    protected static $_type = null;

    /**
     * @var array
     */
    protected static $_supportedAlgosOpenssl = array(
        'md2',
        'md4',
        'mdc2',
        'rmd160',
        'sha',
        'sha1',
        'sha224',
        'sha256',
        'sha384',
        'sha512',
    );

    /**
     * @var array
     */
    protected static $_supportedAlgosMhash = array(
        'adler32',
        'crc32',
        'crc32b',
        'gost',
        'haval128',
        'haval160',
        'haval192',
        'haval256',
        'md4',
        'md5',
        'ripemd160',
        'sha1',
        'sha256',
        'tiger',
        'tiger128',
        'tiger160',
    );

    /**
     * @param  string $algorithm
     * @param  string $data
     * @param  bool $binaryOutput
     * @return void
     */
    public static function hash($algorithm, $data, $binaryOutput = false)
    {
        $algorithm = strtolower($algorithm);
        if (function_exists($algorithm)) {
            return $algorithm($data, $binaryOutput);
        }
        self::_detectHashSupport($algorithm);
        $supportedMethod = '_digest' . ucfirst(self::$_type);
        return self::$supportedMethod($algorithm, $data, $binaryOutput);
    }

    /**
     * @param  string $algorithm
     * @throws RuntimeException
     */
    protected static function _detectHashSupport($algorithm)
    {
        if (function_exists('hash')) {
            self::$_type = self::TYPE_HASH;
            if (in_array($algorithm, hash_algos())) {
               return;
            }
        }
        if (function_exists('mhash')) {
            self::$_type = self::TYPE_MHASH;
            if (in_array($algorithm, self::$_supportedAlgosMhash)) {
               return;
            }
        }
        if (function_exists('openssl_digest')) {
            if ($algorithm == 'ripemd160') {
                $algorithm = 'rmd160';
            }
            self::$_type = self::TYPE_OPENSSL;
            if (in_array($algorithm, self::$_supportedAlgosOpenssl)) {
               return;
            }
        }
        throw new RuntimeException('\'' . $algorithm . '\' is not supported by any available extension or native function');
    }

    /**
     * @param  string $algorithm
     * @param  string $data
     * @param  bool $binaryOutput
     * @return string
     */
    protected static function _digestHash($algorithm, $data, $binaryOutput)
    {
        return hash($algorithm, $data, $binaryOutput);
    }

    /**
     * @param  string $algorithm
     * @param  string $data
     * @param  bool $binaryOutput
     * @return string
     */
    protected static function _digestMhash($algorithm, $data, $binaryOutput)
    {
        $constant = constant('MHASH_' . strtoupper($algorithm));
        $binary = mhash($constant, $data);
        if ($binaryOutput) {
            return $binary;
        }
        return bin2hex($binary);
    }

    /**
     * @param  string $algorithm
     * @param  string $data
     * @param  bool $binaryOutput
     * @return string
     */
    protected static function _digestOpenssl($algorithm, $data, $binaryOutput)
    {
        if ($algorithm == 'ripemd160') {
            $algorithm = 'rmd160';
        }
        return openssl_digest($data, $algorithm, $binaryOutput);
    }
}
