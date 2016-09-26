var app = angular.module('app', ['ui.grid'], function($interpolateProvider) {
	$interpolateProvider.startSymbol('<%');
	$interpolateProvider.endSymbol('%>');
});

function randomFromArray(array){
	return array[Math.floor((Math.random() * array.length))];
}

function getFractionString(float){
	var f = new Fraction(Number(float));
	return (f.denominator > 1) ? f.numerator + '/' + f.denominator : f.numerator;
}

function cutString(string, n){
	return string.substring(0, (string.length-n));
}

function getTrapString(traps){
	var trapStrings = [];
	for(var i=0; i< traps.length; i++){
		var trapString = []; var trap = traps[i];
		trapString.push(trap.id);
		trapString.push(trap.column);
		trapString.push(trap.row);
		trapStrings.push(trapString);
	}
	return JSON.stringify(trapStrings);
}

function convertValuesToNumbers(hash, list){
	for(var i=0; i<list.length; i++){
		var value = list[i];
		hash[value] = Number(hash[value]);
	}
	return hash;
}

function convertListHashValuesToNumbers(array, list){
	for(var i=0; i<array.length; i++){
		array[i] = convertValuesToNumbers(array[i], list);
	}
	return array;
}

function getTrapSting(traps){
	var trapStrings = [];
	for(var i=0; i< traps.length; i++){
		var trapString = []; var trap = traps[i];
		trapString.push(trap.id+"");
		trapString.push(trap.column);
		trapString.push(trap.row);
		trapStrings.push(trapString);
	}
	return JSON.stringify(trapStrings);
}

app.controller("UtilsController", ['$scope', "$http","$controller", function($scope, $http, $controller){
	angular.extend(this, $controller("StandardUtilitiesController", {$scope: $scope}));

	var Utils = function(CONFIG){
		var that = {};

		const HTTP_CALL_PROJECT_BASE = CONFIG.projectBase || PROJECT_BASE;

		function getApiUrl(){
			return CONFIG.apiUrl || API_URL_LOCATION;
		}
		that.getApiUrl = getApiUrl;

		function getLocalUrl(){
			return HTTP_CALL_PROJECT_BASE+"/"+(CONFIG.localResource || '');
		}
		that. getLocalUrl = getLocalUrl;

		function  getLocalApiUrl() {
			return getApiUrl()+"/"+(CONFIG.localResource || '');
		}
		that.getLocalApiUrl = getLocalApiUrl;

		function runOnFailed(requestName, reason) {
			var errorMessage = "Http request " + requestName + " failed";
			if(reason){errorMessage = errorMessage+": "+reason;}
			if (CONFIG.alertOnError) {
				alert(errorMessage);
			} else if (!CONFIG.disableLog) {
				console.log(errorMessage);
			}
		}
		that.runOnFailed = runOnFailed;

		function getFirstNotNull(list){
			for(var i=0; i<list.length; i++){
				if(list[i]){return list[i];}
			}
			runOnFailed("getFirstNotNull", 'all list entries null');
			return null;
		}
		that.getFirstNotNull = getFirstNotNull;

		return that;
	};

	$scope.CreateShowUtil = function(CONFIG){
		var utils = new Utils(CONFIG);
		var that = {};

		function setDisplay(runOnSuccess, runOnFailed){
			var id = CONFIG.id;
			if(id){
				var url = utils.getLocalApiUrl()+"/"+id;
				$http.get(url).then(function(response){
					runOnSuccess(response.data);
				}, function errorCallback(response){
					if(!runOnFailed){
						runOnFailed("ShowUtil.setDisplay", response.statusText);
					}else {
						runOnFailed(response);
					}
				});
			}
		}
		that.setDisplay =  setDisplay;

		function getDisplayFromHash(valueToReplace, displayHash, defaultIfNoneFound){
			return (displayHash[valueToReplace]) ? displayHash[valueToReplace] : defaultIfNoneFound;
		}
		that.getDisplayFromHash = getDisplayFromHash;

		return that;
	};

	$scope.CreateEditUtil = function(CONFIG){
		var utils = new Utils(CONFIG);
		var that = {};

		function getDataOnEdit(setFunct, requiredId, url){
			requiredId = requiredId || CONFIG.defaultCheckObjectPresent;
			if(requiredIdPresent(requiredId)){
				url = url || utils.getLocalApiUrl()+"/"+requiredId;
				$http.get(url).then(function(response){
					setFunct(response.data);
				}, function errorCallback(response){
					runOnFailed("getDataOnEdit", response.statusText);
				});
			}
		}
		that.getDataOnEdit = getDataOnEdit;

		function requiredIdPresent(requiredId){
			return requiredId && requiredId.length >0;
		}

		function getDefaultAccess(runOnSuccess, url){
			url = url || utils.getApiUrl()+"/profile/defaultAccess";
			$http.get(url).then(function(response){
				runOnSuccess(response.data);
			}, function errorCallback(response){
				utils.runOnFailed("getDefaultAccess", response.statusText);
			});
		}
		that.getDefaultAccess = getDefaultAccess;

		function runOnCreate(functionToRun, requiredId){
			requiredId = requiredId || CONFIG.defaultCheckObjectPresent;
			if(!requiredIdPresent(requiredId)){
				functionToRun();
			}
		}
		that.runOnCreate = runOnCreate;

		return that;

	};

	$scope.ExtendedGrid = function(CONFIG ,options){
		const DEFAULT_GRID_OPTIONS = {enableFiltering: true, enableColumnResizing: true, showColumnFooter: true , enableSorting: false, showGridFooter: true, enableRowHeaderSelection: false, rowHeight: 42, enableColumnMenus: false};

		var that = options || DEFAULT_GRID_OPTIONS;
		var utils = new Utils(CONFIG);

		function formatColumnAsDate(columnName){
			var data = that.data;
			for(var i=0; i<data.length; i++){
				data[i][columnName] = new Date( data[i][columnName]);
			}
			that.data = data;
		}
		that.formatColumnAsDate = formatColumnAsDate;

		function refresh(callback, url){
			url = url || utils.getLocalApiUrl();
			callback = callback || CONFIG.runOnGridRefresh;
			$http.get(url).then(function(response){
				that.data = response.data;
				if(callback){callback(response.data)}
			}, function errorCallback(response){
				utils.runOnFailed("refresh", response.statusText);
			});

		}
		that.refresh = refresh;

		function formatColumnWithHash(columnName, hash, defaultValue){
			var data = that.data;
			angular.forEach(data, function(row){
				if(hash[row[columnName]]){
					row[columnName] = hash[row[columnName]];
				}else if(defaultValue){
					row[columnName] = defaultValue;
				}
			});
			that.data = data;
		}
		that.formatColumnWithHash = formatColumnWithHash;

		function formatCreatedAt(){
			that.formatColumnAsDate("created_at");
		}
		that.formatCreatedAt = formatCreatedAt;

		function formatPublicPrivate(){
			that.formatColumnWithHash('public', {0:"Private", 1: "Public"});
		}
		that.formatPublicPrivate = formatPublicPrivate;

		function getLocalRowUrlWithId(){
			return utils.getLocalUrl() + "/<%row.entity.id%>";
		}

		function setColumnDefs(columns) {
			var href;
			if(!CONFIG.noAdditinalColumns) {
				if (!CONFIG.noEditColumn) {
					href = getLocalRowUrlWithId()+"/edit";
					columns.unshift(getEditCell(href));
				}
				if (!CONFIG.noShowColumn) {
					href = getLocalRowUrlWithId();
					columns.unshift(getShowCell(href));
				}
			}

			that.columnDefs = columns;
		}
		that.setColumnDefs = setColumnDefs;

		function getGridLink(href, buttonName, classes){
			return '<a class="btn '+classes+'" role="button" ng-href="'+href+'">'+buttonName+'</a>;'
		}
		that.getGridLink = getGridLink;

		function getGridButton(clickAction, buttonName, classes){
			return '<button class="btn '+classes+'" role="button" ng-click="'+clickAction+'">'+buttonName+'</button>'
		}
		that.getGridButton = getGridButton;

		function getEditCell(href){
			const  EDIT_BUTTON_HTML = getGridLink(href, "Edit", "btn-default");
			return {field: 'edit', enableFiltering: false, width: 52, cellTemplate: EDIT_BUTTON_HTML};
		}

		function getShowCell(href){
			const SHOW_BUTTON_HTML = getGridLink(href, "Show", "btn-default");
			return {field: 'show', enableFiltering: false, width: 63, cellTemplate: SHOW_BUTTON_HTML};
		}

		return that;
	};

	$scope.getModifer = function(stat){
		if(!stat){return "";}
		var modifer = (stat) ? Math.floor((stat-10)/2): 0;
		return (modifer >0) ? "+"+modifer : modifer;
	};

	const IGNORE_CAPITALIZE = ['the', 'of', 'and', 'at', 'to', 'or'];
	$scope.capitalizeEachWord = function(string, capitalizeAll){
		var stringArray = string.split(" ");
		var newString = '';
		angular.forEach(stringArray, function (string, index) {
			if(!(index != 0 && !capitalizeAll && IGNORE_CAPITALIZE.indexOf(string) != -1)){
				newString += string[0].toUpperCase() + string.slice(1)+" ";
			}else{
				newString += string+" ";
			}
		});
		return newString.trim();
	};

	$scope.getStringDisplay = function(string){
		string = string.replace("_", " ");
		return $scope.capitalizeEachWord(string);
	}


}]);
