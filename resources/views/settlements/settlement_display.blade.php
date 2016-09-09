<div class="panel  panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">{{$settlement->name}}</h1>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                Size: {{$settlement->getSizeDisplay()}}
            </div>

            <div class="col-md-6">
                Population: {{$settlement->population}}
            </div>
        </div>

        @if($settlement->known_for)
            <div class="row col-md-12">
                Known For: {{$settlement->known_for}}
            </div>
        @endif

        @if($settlement->notable_traits)
            <div class="row col-md-12">
                Notable Traits: {{$settlement->notable_traits}}
            </div>
        @endif

        @if($settlement->ruler_status)
            <div class="row col-md-12">
                Ruler Status: {{$settlement->ruler_status}}
            </div>
        @endif

        @if($settlement->current_calamity)
            <div class="row col-md-12">
                Current Calamity: {{$settlement->current_calamity}}
            </div>
        @endif

        @if($settlement->other_information)
            <div class="row col-md-12">
                {{$settlement->other_information}}
            </div>
        @endif
    </div>

</div>