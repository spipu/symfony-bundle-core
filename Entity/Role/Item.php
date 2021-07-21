<?php
/**
 * This file is part of a Spipu Bundle
 *
 * (c) Laurent Minguet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spipu\CoreBundle\Entity\Role;

class Item
{
    public const TYPE_PROFILE  = 'profile';
    public const TYPE_ROLE     = 'role';

    /**
     * @var Item[]
     */
    private static $repository = [];

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $type = self::TYPE_ROLE;

    /**
     * @var int
     */
    private $weight = 10;

    /**
     * @var Item[]
     */
    private $children = [];

    /**
     * @var string|null
     */
    private $purpose = 'admin';

    /**
     * Item constructor.
     * @param string $code
     */
    public function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return self
     */
    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    /**
     * @param string|null $purpose
     * @return $this
     */
    public function setPurpose(?string $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param string $code
     * @return self
     */
    public function addChild(string $code): self
    {
        $child = self::load($code);

        $this->children[$child->getCode()] = $child;

        return $this;
    }

    /**
     * @param string $code
     * @return Item
     */
    public static function load(string $code): Item
    {
        if (!array_key_exists($code, self::$repository)) {
            self::$repository[$code] = new Item($code);
        }
        return self::$repository[$code];
    }

    /**
     * @return Item[]
     */
    public static function getAll(): array
    {
        return self::$repository;
    }

    /**
     * @return void
     */
    public static function resetAll(): void
    {
        self::$repository = [];
    }
}
