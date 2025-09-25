<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Authorization\DenyResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use LogicException;

trait InteractsWithSelectedRecords
{
    protected bool | Closure $canAccessSelectedRecords = false;

    protected int $totalSelectedRecordsCount = 0;

    protected int $successfulSelectedRecordsCount = 0;

    protected int $bulkAuthorizationFailureWithoutMessageCount = 0;

    protected int $bulkProcessingFailureWithoutMessageCount = 0;

    /**
     * @var array<string>
     */
    protected array $bulkAuthorizationFailureMessages = [];

    /**
     * @var array<string, array{message: string | Closure, count: int}>
     */
    protected array $bulkProcessingFailureMessages = [];

    public function accessSelectedRecords(bool | Closure $condition = true): static
    {
        $this->canAccessSelectedRecords = $condition;

        return $this;
    }

    public function canAccessSelectedRecords(): bool
    {
        return (bool) $this->evaluate($this->canAccessSelectedRecords);
    }

    public function getSelectedRecords(): EloquentCollection | Collection | LazyCollection
    {
        if (! $this->canAccessSelectedRecords()) {
            throw new LogicException("The action [{$this->getName()}] is attempting to access the selected records from the table, but it is not using [accessSelectedRecords()], so they are not available.");
        }

        $records = $this->getLivewire()->getSelectedTableRecords($this->shouldFetchSelectedRecords(), $this->getSelectedRecordsChunkSize());

        $this->totalSelectedRecordsCount = ($records instanceof LazyCollection)
            ? $this->getLivewire()->getSelectedTableRecordsQuery(shouldFetchSelectedRecords: false)->count()
            : $records->count();
        $this->successfulSelectedRecordsCount = $this->totalSelectedRecordsCount;

        return $records;
    }

    public function getSelectedRecordsQuery(): Builder
    {
        if (! $this->canAccessSelectedRecords()) {
            throw new LogicException("The action [{$this->getName()}] is attempting to access the selected records query from the table, but it is not using [accessSelectedRecords()], so they are not available.");
        }

        return $this->getLivewire()->getSelectedTableRecordsQuery($this->shouldFetchSelectedRecords(), $this->getSelectedRecordsChunkSize());
    }

    public function getIndividuallyAuthorizedSelectedRecords(): EloquentCollection | Collection | LazyCollection
    {
        if (! $this->shouldAuthorizeIndividualRecords()) {
            return $this->getSelectedRecords();
        }

        $this->successfulSelectedRecordsCount = 0;

        $authorizationResponses = [];
        $failureCountsByAuthorizationResponse = [];
        $failureCountWithoutAuthorizationResponse = 0;

        try {
            return $this->getSelectedRecords()->filter(function ($record) use (&$authorizationResponses, &$failureCountsByAuthorizationResponse, &$failureCountWithoutAuthorizationResponse) {
                $response = $this->getIndividualRecordAuthorizationResponse($record);

                if ($response->allowed()) {
                    return true;
                }

                if ($response instanceof DenyResponse) {
                    $responseKey = $response->getKey();

                    $authorizationResponses[$responseKey] ??= $response;
                    $failureCountsByAuthorizationResponse[$responseKey] ??= 0;
                    $failureCountsByAuthorizationResponse[$responseKey]++;
                } elseif (filled($responseMessage = $response->message())) {
                    $responseKey = array_search($responseMessage, $authorizationResponses);

                    if ($responseKey === false) {
                        $authorizationResponses[] = $responseMessage;
                        $responseKey = array_key_last($authorizationResponses);
                        $failureCountsByAuthorizationResponse[$responseKey] = 0;
                    }

                    $failureCountsByAuthorizationResponse[$responseKey]++;
                } else {
                    $failureCountWithoutAuthorizationResponse++;
                }

                $this->successfulSelectedRecordsCount--;

                return false;
            });
        } finally {
            $failureMessages = [];

            if ($this->totalSelectedRecordsCount > $this->successfulSelectedRecordsCount) {
                foreach ($authorizationResponses as $responseKey => $response) {
                    if ($response instanceof DenyResponse) {
                        $failureMessages[] = $response->message($failureCountsByAuthorizationResponse[$responseKey], $this->totalSelectedRecordsCount);
                    } else {
                        $failureMessages[] = $response;
                    }
                }
            }

            $this->bulkAuthorizationFailureWithoutMessageCount = $failureCountWithoutAuthorizationResponse;
            $this->bulkAuthorizationFailureMessages = $failureMessages;
        }
    }

    public function reportBulkProcessingFailure(?string $key = null, string | Closure | null $message = null): void
    {
        if (filled($key)) {
            $this->bulkProcessingFailureMessages[$key] = [
                'message' => $message,
                'count' => ($this->bulkProcessingFailureMessages[$key]['count'] ?? 0) + 1,
            ];
        } else {
            $this->bulkProcessingFailureWithoutMessageCount++;
        }

        $this->successfulSelectedRecordsCount--;
    }

    public function reportBulkProcessingSuccessfulRecordsCount(int $count): void
    {
        $this->bulkProcessingFailureWithoutMessageCount = $this->successfulSelectedRecordsCount - $count;
        $this->successfulSelectedRecordsCount = $count;
    }

    public function reportCompleteBulkProcessingFailure(?string $key = null, string | Closure | null $message = null): void
    {
        if (filled($key)) {
            $this->bulkProcessingFailureMessages[$key] = [
                'message' => $message,
                'count' => $this->getTotalSelectedRecordsCount(),
            ];
        } else {
            $this->bulkProcessingFailureWithoutMessageCount += $this->getTotalSelectedRecordsCount();
        }

        $this->successfulSelectedRecordsCount = 0;
    }

    /**
     * @return array<string>
     */
    public function getBulkProcessingFailureMessages(): array
    {
        return array_reduce(
            $this->bulkProcessingFailureMessages,
            function (array $carry, array $failure): array {
                if (blank($failure['message'])) {
                    return $carry;
                }

                $carry[] = $this->evaluate($failure['message'], [
                    'count' => $failure['count'],
                    'failureCount' => $failure['count'],
                    'isAll' => $failure['count'] === $this->totalSelectedRecordsCount,
                    'total' => $this->totalSelectedRecordsCount,
                    'totalCount' => $this->totalSelectedRecordsCount,
                ]);

                return $carry;
            },
            initial: [],
        );
    }

    public function getTotalSelectedRecordsCount(): int
    {
        return $this->totalSelectedRecordsCount;
    }
}
