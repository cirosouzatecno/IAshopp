<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatbotRule extends Model
{
    protected $fillable = [
        'name',
        'status',
        'match_type',
        'triggers_text',
        'response_type',
        'response_text',
        'template_id',
        'buttons_text',
        'applies_to_state',
        'priority',
    ];

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function appliesToState(?string $state): bool
    {
        if (!$this->applies_to_state || $this->applies_to_state === 'any') {
            return true;
        }

        return $state === $this->applies_to_state;
    }

    public function triggers(): array
    {
        return $this->splitLines($this->triggers_text);
    }

    public function buttons(): array
    {
        $lines = $this->splitLines($this->buttons_text);
        $buttons = [];
        $index = 1;

        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', $line, 2));
            $title = $parts[0] ?? '';
            if ($title === '') {
                continue;
            }

            $id = $parts[1] ?? '';
            if ($id === '') {
                $slug = Str::slug($title, '-');
                $id = $slug !== '' ? $slug : 'btn_' . $index;
            }

            $buttons[] = [
                'id' => $id,
                'title' => $title,
            ];
            $index++;
        }

        return $buttons;
    }

    public function matches(string $normalizedText): bool
    {
        $triggers = $this->triggers();
        if (empty($triggers)) {
            return false;
        }

        foreach ($triggers as $trigger) {
            if ($this->matchTrigger($normalizedText, $trigger)) {
                return true;
            }
        }

        return false;
    }

    protected function matchTrigger(string $text, string $trigger): bool
    {
        $trigger = mb_strtolower(trim($trigger));
        if ($trigger === '') {
            return false;
        }

        $matchType = $this->match_type;

        if ($matchType === 'exact') {
            return $text === $trigger;
        }

        if ($matchType === 'starts_with') {
            return str_starts_with($text, $trigger);
        }

        if ($matchType === 'regex') {
            $pattern = $this->normalizeRegex($trigger);
            if (!$pattern) {
                return false;
            }
            return (bool) @preg_match($pattern, $text);
        }

        return str_contains($text, $trigger);
    }

    protected function normalizeRegex(string $trigger): ?string
    {
        $trigger = trim($trigger);
        if ($trigger === '') {
            return null;
        }

        $first = $trigger[0];
        $last = substr($trigger, -1);
        if (($first === '/' && $last === '/') || ($first === '#' && $last === '#')) {
            return $trigger;
        }

        return '/' . str_replace('/', '\/', $trigger) . '/';
    }

    protected function splitLines(?string $text): array
    {
        if (!$text) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $clean = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $clean[] = $line;
            }
        }

        return $clean;
    }
}
