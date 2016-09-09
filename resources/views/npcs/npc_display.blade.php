<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <h1 class="panel-title pull-left" style="padding-top: 7.5px;">{{(isset($title) && $title==true) ? $title : $npc->displayName()}}</h1>
        @if((isset($hide) && $hide==true))
            <button class="btn btn-primary pull-right" ng-click="showNpc = !showNpc"><% (showNpc) ? 'Hide' : 'Show' %></button>
        @endif
    </div>
    <div class="panel-body" @if((isset($hide) && $hide==true)) ng-show="showNpc" @endif>
        <div class="row">
            <div class="col-md-3">
                Age: <b>{{$npc->age}}</b>
            </div>
            <div class="col-md-3">
                Sex: <b>{{$npc->displaySex()}}</b>
            </div>
            <div class="col-md-3">
                Weight: <b>{{$npc->weight}}</b> lbs.
            </div>
            <div class="col-md-3">
                Height: <b>{{$npc->displayHeight()}}</b>
            </div>
        </div>

        @if($npc->flaw)
            <div class="row">
                <div class="col-md-12">
                    <h4>Flaw</h4>
                    <div>{{$npc->flaw}}</div>
                </div>
            </div>
        @endif

        @if($npc->interaction)
        <div class="row">
            <div class="col-md-12">
                <h4>Interaction Trait</h4>
                <div>{{$npc->interaction}}</div>
            </div>
        </div>
        @endif

        @if($npc->mannerism)
        <div class="row">
            <div class="col-md-12">
                <h4>Mannerism</h4>
                <div>{{$npc->mannerism}}</div>
            </div>
        </div>
        @endif

        @if($npc->bond)
        <div class="row">
            <div class="col-md-12">
                <h4>Bond</h4>
                <div>{{$npc->bond}}</div>
            </div>
        </div>
        @endif

        @if($npc->appearance)
        <div class="row">
            <div class="col-md-12">
                <h4>Appearance</h4>
                <div>{{$npc->appearance}}</div>
            </div>
        </div>
        @endif

        @if($npc->talent)
        <div class="row">
            <div class="col-md-12">
                <h4>Talent</h4>
                <div>{{$npc->talent}}</div>
            </div>
        </div>
        @endif

        @if($npc->ideal)
        <div class="row">
            <div class="col-md-12">
                <h4>Ideal</h4>
                <div>{{$npc->ideal}}</div>
            </div>
        </div>
        @endif

        @if($npc->ability)
        <div class="row">
            <div class="col-md-12">
                <h4>Ability</h4>
                <div>{{$npc->ability}}</div>
            </div>
        </div>
        @endif

        @if($npc->other_information)
        <div class="row">
            <div class="col-md-12">
                <h4>Other Information</h4>
                <div class="showDisplay">{{$npc->other_information}}</div>
            </div>
        </div>
        @endif

    </div>
</div>