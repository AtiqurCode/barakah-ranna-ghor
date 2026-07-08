<?php

use App\Models\Subscriber;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Subscribers')] class extends Component
{
    use WithPagination;

    public function delete(Subscriber $subscriber): void
    {
        $subscriber->delete();

        Flux::toast(variant: 'success', text: __('Subscriber removed.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'subscribers' => Subscriber::latest()->paginate(20),
        ];
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Newsletter subscribers') }}</flux:heading>
        <flux:text class="mt-1">{{ __('People who joined from the footer signup.') }}</flux:text>
    </div>

    @if ($subscribers->isEmpty())
        <flux:callout icon="envelope">
            <flux:callout.heading>{{ __('No subscribers yet') }}</flux:callout.heading>
            <flux:callout.text>{{ __('Newsletter signups will appear here.') }}</flux:callout.text>
        </flux:callout>
    @else
        <flux:table :paginate="$subscribers">
            <flux:table.columns>
                <flux:table.column>{{ __('Email') }}</flux:table.column>
                <flux:table.column>{{ __('Subscribed') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($subscribers as $subscriber)
                    <flux:table.row :key="$subscriber->id">
                        <flux:table.cell variant="strong">{{ $subscriber->email }}</flux:table.cell>
                        <flux:table.cell>{{ $subscriber->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end">
                                <flux:button
                                    size="xs"
                                    icon="trash"
                                    variant="ghost"
                                    wire:click="delete({{ $subscriber->id }})"
                                    wire:confirm="{{ __('Remove this subscriber?') }}"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
