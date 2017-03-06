
	<h1>System Details</h1>
	
	<div id="body">
	
	<table class="table table-striped table-hover ">
	  <thead>
		<tr>
		  <th>Tests Results for system <?php echo $system_name;?></th>
		</tr>
	  </thead>
	  <tbody>
	  <?php
	    //echo "<tr><td><a href=/system/compare/".$system_name."/>Compare</a></td><td>All Results</td></tr>";
		echo "<tr><td>Test ID</td><td>Scenario Description</td></tr>";
		foreach ($results as $result)
		{
				echo "<tr><td><a href=/system/results/".$system_name."/".$result['unixdate']."/>".$result['unixdate']."</a></td><td>".$result['scenario']."</td></tr>";
		}
	  ?>
	  </tbody>
	</table>
	
	</div>
