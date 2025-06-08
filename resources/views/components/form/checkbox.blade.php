<!-- resources/views/components/form/checkbox.blade.php -->
@props(['name', 'label', 'checked' => false, 'value' => 1])

<div class="flex items-center mb-4">
    <input 
        type="checkbox" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500']) }}
    >
    @if($label)
        <label for="{{ $name }}" class="ms-2 text-sm font-medium text-gray-900">{{ $label }}</label>
    @endif
</div>