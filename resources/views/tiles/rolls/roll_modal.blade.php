<!-- Add Roll Modal -->
<div id="addRollModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Roll</h4>
			</div>
			<div class="modal-body">
				<label>Amount</label>
				<input type="number" class="form-control" min=0 ng-model="newDice.amount">

				<label>Kind</label>
				<input type="number" class="form-control" min=0 ng-model="newDice.kind">

				<label>Modifer</label>
				<input type="number" class="form-control" min=0 ng-model="newDice.modifer">
				<div class='row'>
					<div class="col-md-4">Text: <% newDice.displayText %></div>
					<div class="col-md-4">Mininum: <% newDice.minRoll %></div>
					<div class="col-md-4">Maximum: <% newDice.maxRoll %></div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" ng-click="addRoll(newDice)"
					type="button">Add</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>