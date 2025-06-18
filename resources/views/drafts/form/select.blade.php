@props([
    'label',
    'name',
    'options' => [],
    'selected' => null,
    'required' => false
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <select name="{{ $name }}" id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500']) }}>
        <option value="">-- Select {{ $label }} --</option>
        @foreach ($options as $value => $display)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $display }}
            </option>
        @endforeach
    </select>
</div>
