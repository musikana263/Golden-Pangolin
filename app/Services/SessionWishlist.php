<?php

namespace App\Services;

use App\Accommodation;
use App\Activity;
use App\Package;
use Illuminate\Support\Collection;

class SessionWishlist
{
    private const KEY = 'wishlist_items';

    public const TYPE_PACKAGE = 'package';

    public const TYPE_ACTIVITY = 'activity';

    public const TYPE_ACCOMMODATION = 'accommodation';

    private function all(): array
    {
        return array_values(session(self::KEY, []));
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function ids(string $type): array
    {
        $prefix = $type.':';

        return collect($this->all())
            ->filter(fn ($k) => str_starts_with($k, $prefix))
            ->map(fn ($k) => (int) substr($k, strlen($prefix)))
            ->values()
            ->all();
    }

    public function has(string $type, int $id): bool
    {
        return in_array("{$type}:{$id}", $this->all(), true);
    }

    public function add(string $type, int $id): void
    {
        $items = $this->all();
        $key = "{$type}:{$id}";
        if (! in_array($key, $items, true)) {
            $items[] = $key;
            session([self::KEY => $items]);
        }
    }

    public function remove(string $type, int $id): void
    {
        $items = array_values(array_filter(
            $this->all(),
            fn ($k) => $k !== "{$type}:{$id}"
        ));
        session([self::KEY => $items]);
    }

    public function toggle(string $type, int $id): bool
    {
        if ($this->has($type, $id)) {
            $this->remove($type, $id);

            return false;
        }

        $this->add($type, $id);

        return true;
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function packages(): Collection
    {
        $ids = $this->ids(self::TYPE_PACKAGE);
        if (empty($ids)) {
            return collect();
        }

        return Package::whereIn('id', $ids)
            ->with('destinations', 'media')
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->values();
    }

    public function activities(): Collection
    {
        $ids = $this->ids(self::TYPE_ACTIVITY);
        if (empty($ids)) {
            return collect();
        }

        return Activity::whereIn('id', $ids)
            ->with('destination', 'media')
            ->get()
            ->sortBy(fn ($a) => array_search($a->id, $ids))
            ->values();
    }

    public function accommodations(): Collection
    {
        $ids = $this->ids(self::TYPE_ACCOMMODATION);
        if (empty($ids)) {
            return collect();
        }

        return Accommodation::whereIn('id', $ids)
            ->with('type', 'media')
            ->get()
            ->sortBy(fn ($a) => array_search($a->id, $ids))
            ->values();
    }

    public function whatsappMessage(): string
    {
        $packages = $this->packages();
        $activities = $this->activities();
        $accommodations = $this->accommodations();

        if ($packages->isEmpty() && $activities->isEmpty() && $accommodations->isEmpty()) {
            return "Hello Golden Pangolin! I'm exploring your tours and would love to chat.";
        }

        $lines = ["Hello Golden Pangolin! I'd love to enquire about these items from my wishlist:"];

        if ($packages->isNotEmpty()) {
            $lines[] = "\n*Tour Packages:*";
            foreach ($packages as $i => $p) {
                $line = ($i + 1).'. '.$p->name;
                if ($p->country) {
                    $line .= " ({$p->country})";
                }
                if ($p->price_from) {
                    $line .= " — {$p->price_from}";
                }
                $lines[] = $line;
            }
        }

        if ($activities->isNotEmpty()) {
            $lines[] = "\n*Activities:*";
            foreach ($activities as $i => $a) {
                $line = ($i + 1).'. '.$a->name;
                if ($a->destination) {
                    $line .= ' @ '.$a->destination->title;
                }
                if ($a->price_from) {
                    $line .= " — {$a->price_from}";
                }
                $lines[] = $line;
            }
        }

        if ($accommodations->isNotEmpty()) {
            $lines[] = "\n*Accommodation:*";
            foreach ($accommodations as $i => $a) {
                $line = ($i + 1).'. '.$a->name;
                if ($a->location) {
                    $line .= ' @ '.$a->location;
                }
                if ($a->price_from) {
                    $line .= " — {$a->price_from}";
                }
                $lines[] = $line;
            }
        }

        $lines[] = "\nCould you help me put a trip together?";

        return implode("\n", $lines);
    }

    public function whatsappUrl(): string
    {
        $number = site_whatsapp_number();
        $text = rawurlencode($this->whatsappMessage());

        return "https://wa.me/{$number}?text={$text}";
    }
}
