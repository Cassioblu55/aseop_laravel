@extends('layout.show')

@section('required_scripts')
    @include('tiles.add_required_script', ['src' => 'js/roll.js'])
@stop

@section('show_title', $monster->name)

@section('show_body')
    <!-- Hit points, armor, speed -->
    <div class="panel panel-default">
        <div class="panel-body">
            <!-- Hit points -->
            <div class="col-md-4">
                <div>
                    <b>Hit Points:</b> <%hit_points%>
                    (<%monster.hit_points.stringValue%>)
                </div>
                <div>
                    <b>Average:</b> <%(monster.hit_points != null) ?
							getDiceAverage(monster.hit_points) : ''%>
                </div>
            </div>
            <!-- armor -->
            <div class="col-md-4">
                <div>
                    <b>Armor:</b> <%monster.armor%>
                </div>
            </div>
            <!-- speed -->
            <div class="col-md-4">
                <div>
                    <b>Speed:</b> <%monster.speed%>ft
                </div>
            </div>
        </div>
    </div>
    <!-- Challenge, found -->
    <div class="panel panel-default">
        <div class="panel-body">
            <!-- found -->
            <div ng-show="hashArrayValueToString(found_places, 'found').length > 0">
                <div class="col-md-6">
                    <div>
                        <b>Found:</b> <%hashArrayValueToString(found_places, 'found')%>
                    </div>
                </div>
            </div>

            <!-- found ends -->
            <!-- Challenge -->
            <div class="col-md-6">
                <div>
                    <b>Challenge:</b> <%challenge%> (<%monster.xp%> XP)
                </div>

            </div>
            <!-- Challenge ends -->
        </div>
    </div>
    <!-- Challenge, found end-->

    <!-- Hit points, armor, speed ends -->
    <!-- Stats -->
    <div class="panel panel-default">
        <div class="panel-body">
            <div ng-repeat="(key, value) in monster.stats">
                <div ng-class="columnSizeByHash(monster.stats, 'md', 6)">
                    <label><b><%capitalizeFirstLetter(key)%></b></label>
                    <p><%value%></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Stats ends -->
    <!-- Skills, Senses, Languages -->
    <div class="panel panel-default" ng-show="skills.length > 0 || senses.length > 0 || languages.length > 0">
        <div class="panel-body">
            <!-- skills -->
            <div class="col-md-4" ng-show="skills.length > 0">
                <b>Skills:</b>
                <div ng-repeat="skill in skills track by $index">
                    <div ng-class="columnSizeByArray(skills, 'md', 2)">
                        <%skill.skill%>: <b> <%(skill.modifier >=0) ? '+' :
									''%><%skill.modifier%></b>
                    </div>
                </div>
            </div>
            <!-- skills end -->
            <!-- Senses -->
            <div class="col-md-4" ng-show="senses.length > 0">
                <b>Senses:</b>
                <div ng-repeat="sense in senses track by $index">
                    <div ng-class="columnSizeByArray(senses, 'md', 2)">
                        <%sense.sense%></div>
                </div>
            </div>
            <!-- Senses end -->
            <!-- Languages -->
            <div class="col-md-4" ng-show="languages.length > 0">
                <b>Languages:</b>
                <div ng-repeat="language in languages track by $index">
                    <div ng-class="columnSizeByArray(languages, 'md', 3)">
                        <%language.language%></div>
                </div>
            </div>
            <!-- Senses end -->
        </div>
    </div>
    <!-- Skills, Senses, Languages end-->

    <!-- Abilites, actions -->
    <div class="row">
        <!-- Abilties -->
        <div class="col-md-6" ng-show="abilities.length > 0">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Abilites</h3>
                </div>
                <div class="panel-body">
                    <div ng-repeat="ability in abilities">
                        <div>
                            <b><%ability.name%></b>
                        </div>
                        <div class="showDisplay"><%ability.description%></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- actions -->
        <div class="col-md-6" ng-show="actions.length > 0">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Actions</h3>
                </div>
                <div class="panel-body">
                    <div ng-repeat="action in actions">
                        <div>
                            <b><%action.name%></b>
                        </div>
                        <div class="showDisplay"><%action.description%></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- actions ends -->

    </div>
    <!-- Abilites, actions end-->
    <!-- description -->
    @if($monster->description)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Description</h3>
            </div>
            <div class="panel-body">
                <div class="showDisplay">{{$monster->description}}</div>
            </div>

        </div>
    @endif

@stop

@section('scripts')
    <script>
        app.controller("MonsterShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));
            angular.extend(this, $controller('rollDisplayController', {$scope: $scope}));

            const CONFIG = {localResource: 'monsters', id: "{{$monster->id}}"};

            $scope.utils = $scope.CreateShowUtil(CONFIG);


            $scope.utils.setDisplay(function (monster) {
                $scope.monster = monster;
                $scope.monster.stats = (monster.stats) ? JSON.parse(convertValuesToNumbers($scope.monster.stats, Object.keys($scope.monster.stats))) : {};
                $scope.skills = (monster.skills) ? convertListHashValuesToNumbers($scope.monster.skills.parseEscape(), ['modifer']) : [];
                var statsKeys = Object.keys($scope.monster.stats);
                for (var i = 0; i < statsKeys.length; i++) {
                    var modifer = $scope.getModifer($scope.monster.stats[statsKeys[i]]);
                    $scope.monster.stats[statsKeys[i]] = $scope.monster.stats[statsKeys[i]] + " (" + modifer + ")";
                }
                $scope.languages = (monster.languages) ? $scope.monster.languages.parseEscape() : [];
                $scope.senses = (monster.senses) ? $scope.monster.senses.parseEscape() : [];
                $scope.abilities = (monster.abilities) ? $scope.monster.abilities.parseEscape() : [];
                $scope.actions = (monster.actions) ? $scope.monster.actions.parseEscape() : [];
                $scope.challenge = getFractionString(monster.challenge);
                $scope.found_places = (monster.found) ? $scope.monster.found.parseEscape() : [];
                $scope.monster.hit_points = (monster.hit_points) ? getDiceValue(monster.hit_points) : {};
                $scope.hit_points = rollDice($scope.monster.hit_points);

            });

        }]);

    </script>
@stop
