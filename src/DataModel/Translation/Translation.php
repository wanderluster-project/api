<?php

declare(strict_types=1);

namespace App\DataModel\Translation;

use App\DataModel\Contracts\SerializableInterface;
use App\DataModel\Contracts\TranslatableInterface;
use App\DataModel\Contracts\VersionableTrait;
use App\Exception\ErrorMessages;
use App\Exception\WanderlusterException;

class Translation implements TranslatableInterface
{
    use VersionableTrait;

    protected ?string $lang = null;
    protected ?string $val = null;

    /**
     * {@inheritdoc}
     */
    public function setLanguage(string $lang): TranslatableInterface
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(string $val): TranslatableInterface
    {
        $this->val = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return $this->val;
    }

    public function merge(Translation $type): void
    {
        // TODO: Implement merge() method.
    }

    public function getSerializationId(): string
    {
        return 'TRANS';
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getSerializationId(),
            'ver' => $this->getVersion(),
            'val' => $this->getValue(),
        ];
    }

    public function fromArray(array $data): SerializableInterface
    {
        $fields = ['type', 'val', 'ver'];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Missing Field: '.$field));
            }
        }

        $type = $data['type'];
        $val = $data['val'];
        $ver = (int) $data['ver'];

        if ($type !== $this->getSerializationId()) {
            throw new WanderlusterException(sprintf(ErrorMessages::ERROR_HYDRATING_DATATYPE, $this->getSerializationId(), 'Invalid Type: '.$type));
        }
        $this->setValue($val);
        $this->setVersion($ver);

        return $this;
    }
}
