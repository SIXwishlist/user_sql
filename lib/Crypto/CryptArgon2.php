<?php
/**
 * Nextcloud - user_sql
 *
 * @copyright 2018 Marcin Łojewski <dev@mlojewski.me>
 * @author    Marcin Łojewski <dev@mlojewski.me>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace OCA\UserSQL\Crypto;

use OCP\IL10N;

/**
 * Argon2 Crypt hash implementation.
 *
 * @see    crypt()
 * @author Marcin Łojewski <dev@mlojewski.me>
 */
class CryptArgon2 extends AbstractAlgorithm
{
    /**
     * @var int Maximum memory (in bytes) that may be used to compute.
     */
    private $memoryCost;
    /**
     * @var int Maximum amount of time it may take to compute.
     */
    private $timeCost;
    /**
     * @var int Number of threads to use for computing.
     */
    private $threads;

    /**
     * The class constructor.
     *
     * @param IL10N $localization The localization service.
     * @param int   $memoryCost   Maximum memory (in bytes) that may be used
     *                            to compute.
     * @param int   $timeCost     Maximum amount of time it may take to compute.
     * @param int   $threads      Number of threads to use for computing.
     */
    public function __construct(
        IL10N $localization, $memoryCost = -1, $timeCost = -1, $threads = -1
    ) {
        if (version_compare(PHP_VERSION, "7.2.0") === -1) {
            throw new \RuntimeException(
                "PASSWORD_ARGON2I requires PHP 7.2.0 or above."
            );
        } else {
            if ($memoryCost === -1) {
                $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST;
            }
            if ($timeCost === -1) {
                $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST;
            }
            if ($threads === -1) {
                $threads = PASSWORD_ARGON2_DEFAULT_THREADS;
            }
        }

        parent::__construct($localization);
        $this->memoryCost = $memoryCost;
        $this->timeCost = $timeCost;
        $this->threads = $threads;
    }

    /**
     * @inheritdoc
     */
    public function checkPassword($password, $dbHash)
    {
        return password_verify($password, $dbHash);
    }

    /**
     * @inheritdoc
     */
    public function getPasswordHash($password)
    {
        return password_hash(
            $password, PASSWORD_ARGON2I, [
                "memory_cost" => $this->memoryCost,
                "time_cost" => $this->timeCost,
                "threads" => $this->threads
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function getAlgorithmName()
    {
        return "Argon2 (Crypt)";
    }
}
