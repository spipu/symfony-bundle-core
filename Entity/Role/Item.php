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

    private string $code;
    private string $label = '';
    private string $type = self::TYPE_ROLE;
    private int $weight = 10;
    private ?string $purpose = 'admin';

    /**
     * @var Item[]
     */
    private array $children = [];

    /**
     * @var Item[]
     */
    private static array $repository = [];

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

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

    public function addChild(string $code): self
    {
        $child = self::load($code);

        $this->children[$child->getCode()] = $child;

        return $this;
    }

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

    public static function resetAll(): void
    {
        self::$repository = [];
    }
}
