<div wire:poll.{{ $pollingInterval }}ms>
    <div class="position-relative">
        <div class="d-flex align-items-center gap-2">
            <div class="flex-grow-1">
                <select wire:model="selectedOffice" name="office" id="office" class="form-select" wire:loading.class="opacity-50">
                    <option value="">Select Office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->code }}">{{ $office->name }} ({{ $office->abbreviation }})</option>
                    @endforeach
                </select>
            </div>
            <button type="button" 
                    class="btn btn-outline-primary btn-sm" 
                    wire:click="loadOffices" 
                    wire:loading.attr="disabled"
                    title="Refresh Offices">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin"></i>
            </button>
        </div>
        <div wire:loading class="position-absolute top-0 end-0 mt-2 me-2">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div> 