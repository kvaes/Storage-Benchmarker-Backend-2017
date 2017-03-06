<div id="body">

	<?php 
	if ($unix_date <> null) {
		$unix_date = gmdate("d/m/Y H:m:s", $unix_date); 
	}
	?>

	<h1>Tests Results for system <?php echo $system_name;?></h1>
			<table class="table table-striped table-hover ">
		  <tbody>
		  	<tr>
			  <td>System</td>
			  <td><?php echo $system_name; ?></td>
			</tr>
			<tr>
			  <td>Target</td>
			  <td><?php echo $target; ?></td>
			</tr>
	<?php
	if ($unix_date <> null) { 
	?>
		    <tr>
			  <td>Scenario</td>
			  <td><?php echo $scenario; ?></td>
			</tr>
			<tr>
			  <td>Date</td>
			  <td><?php echo $unix_date ?></td>
			</tr>
	<?php
	}
	?>
		  </tbody>
		</table> 
	
	<?php foreach ($results as $result) { 
			$testname = $result['name'];
			$testname = str_replace("%", "", $testname);
	?>
		<h2>Benchmark : <?php echo$result['title']; ?></h2>
		<h3>Parameters</h3>
		<table class="table table-striped table-hover ">
			<tr>
			  <td>IO Size</td>
			  <td><?php echo $result['sizeiokbytes']; ?> Kbytes</td>
			</tr>
			<tr>
			  <td>Type</td>
			  <td><?php echo $result['type']; ?></td>
			</tr>
		  </tbody>
		</table> 
		
		<h3>MB/s</h3>
		<div id="<?php echo $testname; ?>mbsec"></div>
		
		<h3>IO/s</h3>
		<div id="<?php echo $testname; ?>iops"></div>
		
		<h3>Latency (ms)</h3>
		<div id="<?php echo $testname ?>latency"></div>
		

		
	<?php 
	
			/*<h3>Outstanding IO</h3>
		<div id="<?php echo $result['name']; ?>outstanding"></div>*/
	
	}	?>
	
</div>
