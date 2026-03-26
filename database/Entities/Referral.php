<?php

namespace Flute\Modules\Referral\database\Entities;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;
use Flute\Core\Database\Entities\User;

#[Entity(table: 'referrals')]
#[Index(columns: ['referrer_id'])]
#[Index(columns: ['referred_id'], unique: true)]
#[Index(columns: ['created_at'])]
class Referral extends ActiveRecord
{
    #[Column(type: 'primary')]
    public int $id;

    #[BelongsTo(target: User::class, nullable: false, innerKey: 'referrer_id')]
    public User $referrer;

    #[BelongsTo(target: User::class, nullable: false, innerKey: 'referred_id')]
    public User $referred;

    #[Column(type: 'boolean', default: false)]
    public bool $reward_claimed = false;

    #[Column(type: 'decimal(10,2)', default: 0)]
    public float $reward_amount = 0;

    #[Column(type: 'datetime', name: 'created_at')]
    public DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function claimReward(float $amount): void
    {
        $this->reward_claimed = true;
        $this->reward_amount = $amount;
        $this->saveOrFail();
    }
}
