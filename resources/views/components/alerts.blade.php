@if (session('success'))
    <div class="alert alert-success" role="status" data-dismissable>
        <x-icon name="check" />
        <div>{{ session('success') }}</div>
        <button type="button" class="alert__close" data-dismiss aria-label="Close">&times;</button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-error" role="alert">
        <x-icon name="alert" />
        <div>{{ session('error') }}</div>
        <button type="button" class="alert__close" data-dismiss aria-label="Close">&times;</button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <x-icon name="alert" />
        <div>
            Please fix the following:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
