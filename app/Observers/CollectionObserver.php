<?php

namespace App\Observers;

use App\Models\Collection;

class CollectionObserver
{
    /**
     * Handle the collection "created" event.
     *
     * @throws \Exception
     */
    public function created(Collection $collection): void
    {
        // Updates the order for all other collections of the same type.
        Collection::query()
            ->where('type', $collection->type)
            ->where('id', '!=', $collection->id)
            ->where('order', '>=', $collection->order)
            ->increment('order');
    }

    /**
     * Handle the collection "updating" event.
     *
     * @throws \Exception
     */
    public function updating(Collection $collection)
    {
        // Get the original order.
        $originalOrder = $collection->getOriginal('order');

        // If the order number was not updated.
        if ($originalOrder === $collection->order) {
            return;
        }

        if ($originalOrder < $collection->order) {
            // If the order has increased then decrement the other order behind.
            Collection::query()
                ->where('type', $collection->type)
                ->where('id', '!=', $collection->id)
                ->where('order', '<=', $collection->order)
                ->where('order', '>', $originalOrder)
                ->decrement('order');
        } else {
            // If the order has decreased then increment the other order ahead.
            Collection::query()
                ->where('type', $collection->type)
                ->where('id', '!=', $collection->id)
                ->where('order', '>=', $collection->order)
                ->where('order', '<', $originalOrder)
                ->increment('order');
        }
    }

    /**
     * Handle the collection "updated" event.
     *
     * @throws \Exception
     */
    public function updated(Collection $collection): void
    {
        $collection->touchServices();
    }

    /**
     * Handle the organisation "deleting" event.
     */
    public function deleting(Collection $collection)
    {
        $collection->collectionTaxonomies->each->delete();
    }

    /**
     * Handle the collection "deleted" event.
     *
     * @throws \Exception
     */
    public function deleted(Collection $collection): void
    {
        // Updates the order for all other collections of the same type.
        Collection::query()
            ->where('type', $collection->type)
            ->where('order', '>', $collection->order)
            ->decrement('order');
    }
}
