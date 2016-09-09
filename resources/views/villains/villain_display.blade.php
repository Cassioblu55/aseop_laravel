<div class="panel  panel-default">
    <div class="panel-heading clearfix">
        <h1 class="panel-title pull-left" style="padding-top: 7.5px;">Villian Traits</h1>
        @if((isset($hide) && $hide==true))
            <button class="btn btn-primary pull-right" ng-click="showVillain = !showVillain"><% (showVillain) ? 'Hide' : 'Show' %></button>
        @endif
    </div>
    <div class="panel-body" @if((isset($hide) && $hide==true)) ng-show="showVillain" @endif>
        @if($villain->method_type)
            <div>Method: {{$villain->method_type}}</div>
            @if($villain->method_description)
                <div>{{$villain->method_description}}</div>
            @endif
        @endif

        @if($villain->scheme_type)
            <div>Scheme: {{$villain->scheme_type}}</div>
            @if($villain->scheme_description)
                <div>{{$villain->scheme_description}}</div>
            @endif
        @endif

        @if($villain->weakness_type)
            <div>Weakness: {{$villain->weakness_type}}</div>
            @if($villain->weakness_description)
                <div>{{$villain->weakness_description}}</div>
            @endif
        @endif

    </div>
</div>