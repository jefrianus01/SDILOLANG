<?php

namespace App\Support;

final class ImportResult
{
    /**
     * @param  array<int, array{row: int, messages: array<int, string>, data: array<string, mixed>}>  $failures
     * @param  array<int, array{name: string, username: string, password: string}>  $accounts
     */
    public function __construct(
        public int $imported = 0,
        public array $failures = [],
        public array $accounts = [],
    ) {
    }

    public function hasFailures(): bool
    {
        return $this->failures !== [];
    }

    public function failedCount(): int
    {
        return count($this->failures);
    }
}
