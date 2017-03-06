
	<h1>System Overview</h1>
	
	<div id="body">
	
	<table class="table table-striped table-hover ">
	  <thead>
		<tr>
		  <th>System</th>
		</tr>
	  </thead>
	  <tbody>
	  <?php
			foreach ($systems as $system)
			{
					echo "<tr><td><a href=/system/details/".$system."/>".$system."</a></td></tr>";
			}
	  ?>
	  </tbody>
	</table>
	
	</div>
