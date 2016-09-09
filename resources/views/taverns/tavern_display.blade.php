<div class="panel  panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">{{$tavern->name}}</h1>
    </div>
    <div class="panel-body">
        <div>
            Type: {{$tavern->type}}
        </div>

        @if($tavern->other_information)
            <div>
                {{$tavern->other_information}}
            </div>
        @endif

    </div>
</div>
