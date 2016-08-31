<div class="panel panel-default">
	<div class="panel-heading clearfix">
		<h3 class="panel-title pull-left">Rolls</h3>
		<button class="btn btn-primary btn-sm pull-right" type="button"
			data-toggle="modal" data-target="#addRollModal">Add</button>
	</div>
	<div class="panel-body" ng-show="rollValues.length >0">
		<div class="row">
			<div class="col-md-3">
				<label>Amount</label>
			</div>
			<div class="col-md-3">
				<label>Kind</label>
			</div>
			<div class="col-md-3">
				<label>Modifer</label>
			</div>
		</div>
		<div data-ng-repeat="dice in rollValues">
			<div class="row">
				<div class="form-group col-md-3">
					<input type="number" class="form-control" min=0
						ng-model="dice.amount">
				</div>
				<div class="form-group col-md-3">
					<input type="number" class="form-control" min=0
						ng-model="dice.kind">
				</div>
				<div class="form-group col-md-3">
					<input type="number" class="form-control" min=0
						ng-model="dice.modifer">
				</div>
				<div class="form-group col-md-3">
					<button class="btn btn-danger" type="button"
						ng-click="deleteRoll(dice.id)">Delete</button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">Text:
					<% dice.amount+"d"+dice.kind+"+"+dice.modifer %></div>
				<div class="col-md-3">Min: <% dice.amount+dice.modifer %></div>
				<div class="col-md-3">Max: <% (dice.amount*dice.kind)+dice.modifer %>
				</div>
			</div>
		</div>
	</div>
</div>