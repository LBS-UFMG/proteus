<body onLoad="relogio()">	
<meta http-equiv="refresh" content="300">
<div style="background-color: #e4e4e4">
	<div class="container">
		<br>
		<h1>Please, wait! Proteus still is running!</h1>

		<div class="progress">
		  <div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar" aria-valuenow="<?php echo $status; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $status; ?>%"><?php echo $status; ?>%
		  </div>
		</div>
	<br>
	</div>
</div>
<br>
<div class="container">

	<?php if($status == 0){ ?>

				<div class="alert alert-danger" role="alert">
					<p>We detected a possible error during the processing of your PDB file. Please, wait a few minutes and refresh this page. If this message appears again, try submitting a new project or contact the system administrator.</p>
				</div>

			<?php } ?>

	<div class="row">
		<div class="col-md-6">

			
			<h3>Remaining time*</h3>

			<center><span style="text-align: center; font-size:120px" id="spanRelogio"></span></center>

			<p style="color:#999;font-size: 12px">* This value is based on previous estimates. Differences can occur according to the protein length, the number of sextets detected, and the status of the processing server.</p>
		</div>
		
		<div class="col-md-6">

			<h3>Estimated time</h3>
			<span class="label label-success">One residue and neighbors</span>
			<div class="progress">
			  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 7%">
			    ~1h
			  </div>
			</div>

			<span class="label label-info">Polypeptide</span>
			<div class="progress">
			  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 14%">~2h
			  </div>
			</div>

			<span class="label label-warning">Small protein</span>
			<div class="progress">
			  <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 30%">~5h
			  </div>
			</div>
			<span class="label label-danger">Large protein</span>
			<div class="progress">
			  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 100%">~30h
			  </div>
			</div>
		</div>

		<div class="row">
			<span class="label label-info">Your project is stored in ProteusWEB.</span>

			<div class="alert alert-info" role="alert"><p></p>

				<p></span> You can access the project <b><?php echo $id; ?></b> by the link: <a href="http://proteus.dcc.ufmg.br/result/id/<?php echo $id; ?>">http://proteus.dcc.ufmg.br/result/id/<?php echo $id; ?></a></p>
			</div>
			<br><br><br>
		</div>
	</div>
</div>

<script language="javaScript">	
	var hour,min;		
	hour = <?php echo $hour; ?>;
	min = <?php echo $min; ?>;

	function relogio(){			
		if((hour > 0) || (min > 0)){
			if(min == 0){
				min = 59;
				hour = hour - 1
			}
			else{
				min = min - 1;
			}
			if(hour.toString().length == 1){
				hour = hour;
			}
			if(min.toString().length == 1){
				min = "0" + min;
			}
			
			document.getElementById('spanRelogio').innerHTML = hour + "h" + min;
			setTimeout('relogio()', 60000);
		}
		else{
			document.getElementById('spanRelogio').innerHTML = "Ready";
		}
	}	
</script>
</body>