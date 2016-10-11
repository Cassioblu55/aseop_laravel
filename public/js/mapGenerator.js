var sizeHash = {"S": 6, "M": 8, "L":12};
var letters = ["A","B","C","D","E","F","G","H","I","J","K","L","M",
       		   "N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];

//Map object, has methods for interfacing with map of tiles

app.controller("TrapController", ['$scope', "$controller", function($scope, $controller){
	angular.extend(this, $controller('UtilsController', {$scope: $scope}));
	
	$scope.getParsedMap = function(){return JSON.parse($scope.dungeon.map);}
	$scope.stringifyMap = function(map){$scope.dungeon.map = JSON.stringify(map);}
	
	$scope.trapById = function(id){
		if($scope.trapOptions){			
			for(var i=0; i<$scope.trapOptions.length; i++){
				if($scope.trapOptions[i].id == id){return $scope.trapOptions[i];}
			}
		}
	}
	
	$scope.generateMap = function(){
		$scope.map = generateMap($scope.dungeon.size);
		$scope.stringifyMap($scope.map.getTiles());
		$scope.traps = [];
		$scope.trapNumber = 0;
		
	}
	
	//Will loop through list of traps and and add values to any that are missing
	$scope.setRandomTraps = function(){
		if($scope.trapOptions.length > 0) {
			for (var i = 0; i < $scope.traps.length; i++) {
				//If no trap id is selected
				if (!$scope.traps[i].id) {
					$scope.traps[i].id = randomFromArray($scope.trapOptions).id;
				}
				//If there is no row or column
				if (!$scope.traps[i].column && !$scope.traps[i].row) {
					//Get a random row
					var rows = $scope.map.activeRows();
					var row = randomKeyFromHash(rows);
					$scope.traps[i].row = row;
					//Get ranodm column from vaild rows
					var columns = $scope.map.activeColumns(row);
					var column = randomKeyFromHash(columns);
					$scope.traps[i].column = column;
				}
				//If there is a row without a column
				else if (!$scope.traps[i].column && $scope.traps[i].row) {
					//Find a vaild column with the given row
					var row = $scope.traps[i].row;
					var columns = $scope.map.activeColumns(row);
					var column = randomKeyFromHash(columns);
					$scope.traps[i].column = column;
				}
				//If there a column and no row
				else if ($scope.traps[i].column && !$scope.traps[i].row) {
					//Find a vaild column with the given row
					var column = $scope.traps[i].column
					var rows = $scope.map.activeRows(column);
					var row = randomKeyFromHash(rows);
					$scope.traps[i].row = row;
				}
			}
		}
	}
	
	$scope.getTrapDisplay = function(t){
		var hash = {}; var name="Unknown", location, description ="No Description";
		if(t.id){
			var trap = $scope.trapById(t.id);
			name = (trap && trap.name) ? trap.name : name;
			description = (trap && trap.description) ? trap.description : description;
		}

		var row = (t.row) ? parseInt(t.row)+1 : "?"; 
		var column = (t.column) ? letters[t.column] : "?";
		location = "("+column+","+row+")";
		hash.title = name+location;
		hash.description = description;
		return hash;
	}
	
}]);

var map = function(t){
	var that = {};
	var tiles = t;
	var start = {};
	var activeTiles =[];
	var maxTiles = {"S": 20, "M": 40, "L": 80};

	function setActiveTiles(){
		for(var y=0; y<tiles.length; y++){
			var row = tiles[y];
			for(var x=0; x<row.length; x++){
				if(row[x] != "x"){
					var t = {};
					t.x = x;
					t.y = y;
					activeTiles.push(t);
					if(row[x] =='s'){
						start.x = x;
						start.y = y;
					}
				}
			}
		}
	}
	that.setActiveTiles = setActiveTiles;
	//finds the map size based on the number of rows
	function getSize(){
		return
		(sizeHash, tiles[0].length);
		}
	
	function isStart(x, y){
		return (x == start.x && y == start.y);
	}
	
	function notStart(x,y){
		return !isStart(x,y);
	}
	
	function activeRows(column){
		var array = [];
		for(var i=0; i<activeTiles.length; i++){
			var y = activeTiles[i].y; var x = activeTiles[i].x;
			if(array.indexOf(y)== -1 && (!column || (column && column == x && notStart(x,y)))){
				array.push(activeTiles[i].y);
			}
		}
		array = sortAsc(array);
		var a={};
		for(var i=0; i<array.length; i++){
			var r = array[i];
			a[r] = (r+1)+"";
		}
		return a;
	}
	that.activeRows = activeRows;
	
	function activeColumns(row){
		var array = [];
		for(var i=0; i<activeTiles.length; i++){
			var y = activeTiles[i].y; var x = activeTiles[i].x;
			if(array.indexOf(x)== -1 && (!row || (row && row == y && notStart(x,y)))){
				array.push(activeTiles[i].x);
			}
		}
		array = sortAsc(array);
		a = {};
		//Convert the number values to letter values with they key being the number value
		for(var i=0; i<array.length; i++){
			var l = array[i];
			a[l] = letters[l];
		}
		return a;
	}
	that.activeColumns = activeColumns;
	
	//will return true if the map has the specifed number active tiles based on size
	function mapFull(){
		return activeTiles.length >= maxTiles[getSize()];
	}
	that.mapFull = mapFull;

	function getActiveTiles(){
		return activeTiles;
	}
	that.getActiveTiles = getActiveTiles;
	//Returns a random active tile
	function getRandomActive(){
		return activeTiles[getRandomNormal(activeTiles.length)];
	}
	that.getRandomActive = getRandomActive;

	//Will see if any of the surrounding tiles are active based off direction
	function noneAround(t, d){
		var lookOne; var lookTwo;
		//going up or down will look left and right
		if(d==0 || d==1){
			lookOne = move(t, 2);
			lookTwo = move(t, 3);
		}
		// right or left will look up and down
		else{
			lookOne = move(t,0);
			lookTwo = move(t,1);
		}
		//If both tiles are either null or inactive it will return true
		return (lookOne==null || !active(lookOne)) && (lookTwo == null || !active(lookTwo));
	}
	that.noneAround = noneAround;
	
	//returns all tiles 
	function getTiles(){
		return tiles;
	}
	that.getTiles = getTiles;
	
	//returns tile of specifed location, if tile doesn't exist it will return null
	function get(x,y){
		return vaildTile(x,y) ? {"x" : x, "y" : y} : null;
	}
	that.get = get;

	//Will check to see if requested tile exists in grid, returns false if tile is invaild
	function vaildTile(x,y){
		return (x>=0 && y>=0 && x<tiles[0].length && y<tiles[0].length);
	}

	//Will set the value of a specifyed tile, if tile does not exist will return false
	function set(t, value){
		if(vaildTile(t.x,t.y)){
			tiles[t.y][t.x]=value;
			activeTiles.push(t);
			return true;
		}
		return false;
	}
	that.set = set;
	
	function setStart(t){
		if(vaildTile(t.x,t.y)){
			set(t, "s")
			start = t;
		}
	}
	that.setStart = setStart;

	//Will check to see if specifed tile already has a value
	function active(t){
		for(var i=0; i<activeTiles.length; i++){
			var at = activeTiles[i];
			if(at.x == t.x && at.y == t.y){return true;}
		}
		return false;
	}
	that.active = active;

	function removeTraps(){
		for(var y=0; y< tiles.length; y++){
			for(var x=0; x<tiles.length; x++){
				if(tiles[y][x]=="t"){
					if(start.y == y && start.x==x){tiles[y][x]="s";}
					else{tiles[y][x]="w";}
					
				}
			}
		}
	}
	that.removeTraps = removeTraps;
	
	function setTrap(x,y){
		if(vaildTile(x,y) && (start.y != y || start.x != x)){
			tiles[y][x] = "t";
		}
	}
	that.setTrap = setTrap;
	
	//Will return the tile in the direction specified
	//0 == down, 1 == up, 2 == left, 3 == right
	//Will return null if tile doesn't exist
	function move(s, d){
		if(d==0){return get(s.x, s.y+1);}
		else if(d==1){return get(s.x, s.y-1);}
		else if(d==2){return get(s.x+1, s.y);}
		else{return get(s.x-1, s.y);}
	}
	that.move = move;
	return that;
}

function sortAsc(array){
	array.sort(function(a, b) {
		  return a - b;
		});
	return array;
}

function sortDsc(array){
	array.sort(function(a, b) {
		  return b - a;
		});
	return array;
}

var textOffSet ={"S": {font: "18px Arial", Yoffset: 16, Xoffset: 8, numXOff: 5, numYOff: 2},
		 		 "M": {font: "18px Arial", Yoffset: 15, Xoffset: 4, numXOff: 5, numYOff: 2},
		 		 "L": {font: "13px Arial", Yoffset: 10, Xoffset: 4, numXOff: 5, numYOff: 2}
				};	

function drawMap(tiles){
		var c = document.getElementById("mapDisplay");
		var width = c.width;
		var height = c.hight;
		var mapSize = tiles[0].length
		var tileSize = width/(mapSize+1); 
		var textOptions = textOffSet[keyFromValueInHash(sizeHash, mapSize)];
		var colors = { 
				"x" : "#FFFFFF", "s" : "#006400",
				"t" : "#DC143C", "w" : "#A9A9A9"
					}
		
		var ctx = c.getContext("2d");
		//Clear canvas
		ctx.font = textOptions.font;
		ctx.fillStyle = "black";
		ctx.clearRect(0, 0, c.width, c.height);
		ctx.fillRect(0,0,tileSize,tileSize/2);
		
		//Draw coordiantes
		var coordYStart= (tileSize/2)-textOptions.numYOff;
		for(var y=0; y<mapSize+1; y++){
			var xStart = (tileSize/2)-textOptions.Xoffset;
			//draw x coords
			if(y==0){
				for(var x=0; x<mapSize+1; x++){
					if(x != 0){
						ctx.fillText(letters[x-1],xStart,textOptions.Yoffset);
					}
					xStart += tileSize;
				}
			}else{
				ctx.fillText(y,((tileSize/2)-textOptions.numXOff), coordYStart);
			}
			coordYStart += tileSize/2;
		}
		
		//Draw map
		var yStart=tileSize/2;
		for(var y=0; y<mapSize; y++){
			var xStart = tileSize;
			for(var x=0; x<mapSize; x++){
				ctx.fillStyle = colors[tiles[y][x]];
				ctx.fillRect(xStart,yStart,tileSize, tileSize/2);
				xStart += tileSize;
			}
			yStart += (tileSize/2);
		}
		
	}

//Will return a randomly generated map
function generateMap(size){
	//Start by finding the size if it dons't exist
	var t = getBlankMap(size);
	//Create a map object out of the a blank map
	var m = map(t);
	//Sets map start
	var tile = m.get(getRandomNormal(sizeHash[size]),0);
	m.setStart(tile);
	
	//Set first main branch
	makeBranch(m, tile, 0);
	//Will keep trying to make random branches untill map is full or 500 branches have been tried
	var branchTryCount = 0;
	var maxBranchTry = 500;
	while(!m.mapFull() && branchTryCount < maxBranchTry){
		//Keep making branches off random active tiles, until map is full
		makeBranch(m, m.getRandomActive(), getRandomDirection());
		branchTryCount++;
	}	
	return m;
	
}

//Down = 0, Up = 1, Right = 2, Left = 4
function makeBranch(map, s, d){
	//Create branch size, add one so min is 1;
	var size = getRandomNormal(4)+1;
	var currentTile = s;
	for(var i=0; i<size; i++){
		var t = map.move(currentTile, d);
		//Set tile to walkway if it exists, if not end loop or requested tile is already active
		if(t != null && !map.active(t) && map.noneAround(t,d)){
			map.set(t, "w");
			currentTile = t;
		}
		else{break;}
	}
}
	
function getBlankMap(size){
	var count = sizeHash[size];
	var map = [];
	for(var y=0; y<count; y++){
		var mapRow = [];
		for(var x=0; x<count; x++){
			mapRow.push("x");
		}
		map.push(mapRow);
	}
	return map;
}

function stringToTraps(trapString){
	if(trapString != ''){
		var t = JSON.parse(trapString);
		var traps = [];
		for(var i=0; i<t.length; i++){
			var trap = {};
			trap.id = t[i][0];
			trap.column = t[i][1];
			trap.row = t[i][2];
			traps.push(trap);
		}
		return traps;		
	}
}

function getRandomDirection(){
	return Math.floor(Math.random() * 4);
}

function getRandomSize(){
	var rand = Math.floor(Math.random() * 2);
	return (rand==0) ? "S" : (rand==1) ? "M" : "L";
}

function getRandomNormal(n){
	return Math.floor(Math.random() * n);
}