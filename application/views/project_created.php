<body onLoad="relogio()">	
<div style="background-color: #e4e4e4">
	<div class="container">

	<br>
	<h1>Proteus is running now!</h1>

	<div class="progress">
	  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
	  </div>
	</div>
<br>
</div>
</div>

	<div class="container">
<br>
<span class="label label-success">Your project was created successfully.</span>


	<div class="alert alert-success" role="alert"><p></p>

	<p><span class="glyphicon glyphicon-ok" style="color:green" aria-hidden="true"></span> You can access it by the link: <a href="<?php echo base_url(); ?>result/id/<?php echo $id; ?>"><?php echo base_url(); ?>result/id/<?php echo $id; ?></a></p>
	</div>

	<br><br>
	<p style="text-align: center;font-weight: bold">You will be redirected in...</p>

	<center><span style="text-align: center; font-size:96px" id="spanRelogio"></span></center>

<script language="javaScript">	
	var seg;		
	seg = 30;

	function relogio(){			
		if(seg > 0){
			seg = seg - 1;			
			document.getElementById('spanRelogio').innerHTML = seg;				
			setTimeout('relogio()', 1000);			
		}			
		else{				
			document.getElementById('spanRelogio').innerHTML = "0";	
			window.location.href = "<?php echo base_url(); ?>result/id/<?php echo $id; ?>";		
		}		
	}	
</script>

<br>

</div>
</body>