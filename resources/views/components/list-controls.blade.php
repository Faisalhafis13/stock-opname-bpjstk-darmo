<form
    method="GET"
    action="{{ $action }}"
    class="d-flex align-items-center gap-2 flex-wrap"
>
    @foreach(request()->except(['page', 'per_page', 'search']) as $name => $value)
        @if(is_scalar($value))
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
    @endforeach

    <label for="list-search" class="visually-hidden">Cari data</label>
    <input
        type="search"
        id="list-search"
        name="search"
        value="{{ request('search') }}"
        class="form-control form-control-sm"
        placeholder="Cari data..."
        aria-label="Cari data"
        style="width: 220px;"
    >
    <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-search me-1"></i>
        Cari
    </button>

    <label for="per_page" class="mb-0 text-muted small">Tampilkan</label>
    <select
        name="per_page"
        id="per_page"
        class="form-select form-select-sm"
        style="width: 85px;"
        onchange="this.form.submit()"
    >
        @foreach([10, 25, 50, 100] as $option)
            <option value="{{ $option }}" @selected(($perPage ?? request('per_page', 10)) == $option)>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <span class="text-muted small">data</span>
</form>
