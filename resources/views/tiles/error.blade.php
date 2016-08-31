@if ($errors->has($errorName))
    <span class="text-danger">
        @foreach ($errors->get($errorName) as $error)
            <strong>{{ $error }}</strong>
        @endforeach
    </span>
@endif