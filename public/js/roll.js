//This will take two strings a description string and roll string and it will split the roll text into 
//a list of rolls and then will convert the roll into numbers and roll dice, it will then replace the text in the description
//And return it
$.guid= 0;

function getRolls(d, r){
	var description = d;
	if(r){
		var die = getDiceValues(r);
		for(var i=0; i<die.length; i++){
			description = description.replace(die[i].stringValue, rollDice(die[i])+" ("+die[i].stringValue+")");
		}
	}
	return description;
}

//Will convert a single roll instance to a dice object
function getDiceValue(rollString){
	that = {};
	that.amount = Number(rollString.split("d")[0]);
	that.kind = Number(rollString.split("d")[1].split("+")[0]);
	that.modifer = Number(rollString.split("+")[1]);
	that.stringValue = rollString;
	that.id = $.guid++;
	return that;
}

function getStringValues(rolls){
	var string = "";
	for(var i=0; i<rolls.length; i++){
		string += getDiceDisplay(rolls[i])+", ";
	}
	return cutString(string, 2);
}

//Converts string to array of dice roll values
function getDiceValues(r){
	var rolls = r.split(",");
	var diceValues = [];
	for(var i=0; i< rolls.length; i++){
		diceValues.push(getDiceValue(rolls[i]));
	}
	return diceValues;
}

function getDiceDisplay(dice){
		return (dice) ? (dice.amount || 0)+"d"+(dice.kind || 0)+"+"+(dice.modifer || 0) : '';
}

function getDiceMin(dice){
	return (dice) ? (dice.amount || 0) + (dice.modifer || 0)  : '';
}

function getDiceMax(dice){
	return (dice) ? ((dice.amount || 0)*(dice.kind || 0)) + (dice.modifer || 0)  : '';
}

function getDiceAverage(dice){
	return (dice) ? Math.floor((dice.amount || 0)*(((dice.kind || 0)+1)/2))+(dice.modifer || 0)  : '';
}

//takes a dice object and will return the number value of the roll
function rollDice(dice){
	var total = 0;
	for(var i=0; i<dice.amount; i++){
		total += Math.floor((Math.random() * dice.kind)) +1; 
	}
	return total+ (dice.modifer);
}

app.controller("rollDisplayController", ['$scope', "$controller", function($scope, $controller){
	
	$scope.newDice = {};
	$scope.rollValues = [];
	
	$scope.$watch('[newDice,newDice.amount,newDice.kind,newDice.modifer]', function(){
		$scope.newDice.displayText = getDiceDisplay($scope.newDice);
		$scope.newDice.minRoll = getDiceMin($scope.newDice);
		$scope.newDice.maxRoll = getDiceMax($scope.newDice);
	}, true);
	
	$scope.addRoll = function(newDice){
		newDice.id = $.guid++;
		$scope.rollValues.push(newDice);
		$('#addRollModal').modal('hide')
		$scope.newDice = {};
	}

	$scope.deleteRoll = function(id){
		for(var i=0; i<$scope.rollValues.length; i++){
			if($scope.rollValues[i].id == id){
				 $scope.rollValues.splice(i, 1);
				}
		}
	}
	
	$scope.getRoll = function(dice){
		return (dice) ? rollDice(dice) : null;
	}
	
	$scope.getDiceMin = function(dice){
		return getDiceMin(dice);
	}
	
	$scope.getDiceMax = function(dice){
		return getDiceMax(dice);
	}
	
	$scope.getDiceDisplay = function(dice){
		return getDiceDisplay(dice);
	}
	
	$scope.getDiceAverage = function(dice){
		return getDiceAverage(dice);
	}
	
}]);

