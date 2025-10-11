<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\BusinessHours;
use App\Domain\WhatsApp\Repositories\BusinessHoursRepositoryInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Infrastructure\WhatsApp\Models\BusinessHoursModel;
use DateTimeImmutable;

final readonly class EloquentBusinessHoursRepository implements BusinessHoursRepositoryInterface
{
    public function save(BusinessHours $businessHours): void
    {
        BusinessHoursModel::updateOrCreate(
            ['id' => $businessHours->getId()->getValue()],
            [
                'day_of_week' => $businessHours->getDayOfWeek(),
                'open_time' => $businessHours->getOpenTime()?->format('H:i:s'),
                'close_time' => $businessHours->getCloseTime()?->format('H:i:s'),
                'is_closed' => $businessHours->isClosed(),
                'timezone' => $businessHours->getTimezone(),
            ]
        );
    }

    public function findById(MessageId $id): ?BusinessHours
    {
        $model = BusinessHoursModel::find($id->getValue());

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByDayOfWeek(string $dayOfWeek): ?BusinessHours
    {
        $model = BusinessHoursModel::where('day_of_week', strtolower($dayOfWeek))->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findAll(): array
    {
        $models = BusinessHoursModel::orderByRaw("
            FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')
        ")->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function delete(MessageId $id): void
    {
        BusinessHoursModel::where('id', $id->getValue())->delete();
    }

    private function toDomainEntity(BusinessHoursModel $model): BusinessHours
    {
        $openTime = $model->open_time ?
            DateTimeImmutable::createFromFormat('H:i:s', $model->open_time) : null;

        $closeTime = $model->close_time ?
            DateTimeImmutable::createFromFormat('H:i:s', $model->close_time) : null;

        return BusinessHours::create(
            MessageId::fromString($model->id),
            $model->day_of_week,
            $openTime,
            $closeTime,
            $model->is_closed,
            $model->timezone
        );
    }
}