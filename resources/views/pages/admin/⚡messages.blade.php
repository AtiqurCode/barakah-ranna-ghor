<?php

use App\Models\ContactMessage;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Messages')] class extends Component
{
    use WithPagination;

    public function delete(ContactMessage $message): void
    {
        $message->delete();

        Flux::toast(variant: 'success', text: __('Message deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'messages' => ContactMessage::latest()->paginate(15),
        ];
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Contact messages') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Enquiries submitted through the contact form.') }}</flux:text>
    </div>

    @if ($messages->isEmpty())
        <flux:callout icon="inbox">
            <flux:callout.heading>{{ __('No messages yet') }}</flux:callout.heading>
            <flux:callout.text>{{ __('New contact form submissions will appear here.') }}</flux:callout.text>
        </flux:callout>
    @else
        <flux:table :paginate="$messages">
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Phone') }}</flux:table.column>
                <flux:table.column>{{ __('Message') }}</flux:table.column>
                <flux:table.column>{{ __('Received') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($messages as $message)
                    <flux:table.row :key="$message->id">
                        <flux:table.cell variant="strong">{{ $message->name }}</flux:table.cell>
                        <flux:table.cell>{{ $message->phone }}</flux:table.cell>
                        <flux:table.cell class="max-w-md whitespace-normal">{{ $message->message }}</flux:table.cell>
                        <flux:table.cell>{{ $message->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end">
                                <flux:button
                                    size="xs"
                                    icon="trash"
                                    variant="ghost"
                                    wire:click="delete({{ $message->id }})"
                                    wire:confirm="{{ __('Delete this message?') }}"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
