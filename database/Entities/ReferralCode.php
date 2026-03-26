<?php

namespace Flute\Modules\Referral\database\Entities;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;
use Flute\Core\Database\Entities\User;

#[Entity(table: 'referral_codes')]
#[Index(columns: ['code'], unique: true)]
#[Index(columns: ['user_id'], unique: true)]
class ReferralCode extends ActiveRecord
{
    #[Column(type: 'primary')]
    public int $id;

    #[BelongsTo(target: User::class, nullable: false, innerKey: 'user_id')]
    public User $user;

    #[Column(type: 'string', length: 32)]
    public string $code;

    #[Column(type: 'integer', default: 0)]
    public int $uses = 0;

    #[Column(type: 'boolean', default: true)]
    public bool $active = true;

    #[Column(type: 'datetime', name: 'created_at')]
    public DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function incrementUses(): void
    {
        $this->uses++;
        $this->saveOrFail();
    }

    public function getLink(): string
    {
        return url('/') . '?ref=' . $this->code;
    }
}
