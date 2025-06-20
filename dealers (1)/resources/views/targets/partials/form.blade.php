@php
    $currentYear = now()->year;
    $currentMonth = now()->month;
    $selectedYear = old('year', $target->year ?? $currentYear);
    $selectedMonth = old('month', $target->month ?? $currentMonth);
@endphp

@csrf

<div class="mb-3">
    <label for="year" class="form-label">📅 Year</label>
    <input type="number" name="year" class="form-control" value="{{ $selectedYear }}" readonly>
</div>

<div class="mb-3">
    <label for="month" class="form-label">🗓️ Month</label>
    <select name="month" class="form-select" id="month-select" required>
        <option value="">Select Month</option>
        @for ($m = 1; $m <= 12; $m++)
            @php
                $isDisabled = ($selectedYear == $currentYear && $m < $currentMonth);
            @endphp
            <option value="{{ $m }}" 
                {{ ($selectedMonth == $m) ? 'selected' : '' }} 
                {{ $isDisabled ? 'disabled' : '' }}>
                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
            </option>
        @endfor
    </select>
</div>

<div class="mb-3">
    <label for="target_amount" class="form-label">🎯 Target Amount (LKR)</label>
    <input type="number" step="0.01" name="target_amount" class="form-control" placeholder="e.g., 100000.00" value="{{ old('target_amount', $target->target_amount ?? '') }}" required min="0">
</div>
