<!-- HERO: main div in home -->
<div id="hero">
	<div id="overlay_hero">
		<div class="container">
			<div class="row">
				<div class="col-md-5 col-xs-12">
					<!--<img title="Protein beta-glucosidase 1BGA" src="<?php echo base_url(); ?>/public/img/bgl.png" class="img-responsive" style="margin-top:-50px"> 
					--><iframe width="100%" height="315" src="https://www.youtube.com/embed/UUEj49gkbUo" frameborder="0" allowfullscreen></iframe>			</div>
				<div class="col-md-7 col-xs-12" style="padding: 40px 10px">
					<span style="font-size:30px;  color:#f4f4f4; text-shadow:1px 1px 1px #111"><b>Protein engineering using Proteus</b></span>
					<p style="font-size:18px; padding:15px 0; color:#d4d4d4; text-shadow:1px 1px 1px #111">
						<strong>Proteus</strong> is a Webtool, database, and method to propose mutations for proteins used in industrial applications. <strong>Proteus</strong> uses the hypothesis of mutation transference of residue pairs in contact detected in PDB to suggest mutations for a target protein. 
					</p>
					<p>
						<a class="btn btn-primary btn-lg" href="<?=base_url('result/id/SYLX52')?>">Explore example</a>
						<a class="btn btn-success btn-lg" href="#run" role="button">Run now!</a>

					</p>
				</div>
			</div>
			<div id="run" style="margin:60px"></div>
		</div>
	</div>
</div>

<div id="subNav">
    <div class="container">
    	<img src="<?php echo base_url(); ?>public/img/tools.png">
    	<strong>Create a new project</strong></div>
</div>

<!-- RUN -->
<div id="run">
	<div class="container">
		<br><strong>Cite us</strong>
		<div class="alert alert-warning" role="alert">
      Barroso, J.M., Mariano, D., Dias, S.R. et al. Proteus: An algorithm for proposing stabilizing mutation pairs based on interactions observed in known protein 3D structures. BMC Bioinformatics 21, 275 (2020). https://doi.org/10.1186/s12859-020-03575-6

    </div>		
		<div class="row" style="padding-top:10px; margin:1px">
        <div class="col-12">
        <div class="thumbnail" style="border-left: #ccc 5px solid; background-color:#f4f4f4; color: #ccc">
        <div class="caption">         
        
        <p>Select a PDB file and a chain to <b>start a new project</b>. Optionally, you can insert an amino acid residue to limit the 
		region for suggesting mutations (and accelerate the running). 
		If you want to test Proteus, you can download this 
		<a href="http://proteus.dcc.ufmg.br/public/download/2LZM.pdb">PDB example (2LZM)</a>. 
		Type <code>A</code> in the second field, <code>S44</code> in the third field, 
		and insert your email address. 
		Proteus will take 1 hour to process your project. If you do not want to wait, 
		<b><a href="http://proteus.dcc.ufmg.br/result/id/SYLX52">click here</a> to see 
		the final result</b>.
	</p>
        </div>
        </div>
        </div>
        </div>
		<form action="<?php echo base_url('result/create'); ?>" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-12">
					<form role="form" method="post" enctype="multipart/form-data" class="form-horizontal">
                        
                        <br><br>

                        <span class="btn btn-primary"><span class="badge badge-pill badge-light" style="color: #23313f">1</span> Select a PDB File</span>
                            Upload a structure (.pdb):<br><br>

                        <input type="file" name="pdb" id="pdb" class="form-control" required>

                        <br><br>

                        <span class="btn btn-primary"><span class="badge badge-pill badge-light" style="color: #23313f">2</span> Select a chain</span>

                        [A, B, C ... Z]
                        <input style="margin:10px 0" type="text" id="chain" name="chain" class="form-control" placeholder="A" required><br><br> 
                           

                        <span class="btn btn-primary"><span class="badge badge-pill badge-light" style="color: #23313f">3</span> Select a residue</span>

                        Proteus will collect all residues at the distance of 10 Å:
                        <input style="margin:10px 0" type="text" id="residue" name="residue" class="form-control" placeholder="E167">
                            
                        <div class="form-check">
                            <input type="checkbox" name="all_residues" id="all_residues">
                            <span>Run for all residues of the chain.</span>
                        </div>
                            
                        <br><br>                          

                        <span class="btn btn-primary"><span class="badge badge-pill badge-light" style="color: #23313f">4</span> Email address</span>

                        Insert your email address to receive a warning when the project was concluded:
                        <br>

						<input style="margin:10px 0" type="email" id="project_name" name="project_name" class="form-control" placeholder="your_name@gmail.com">

                        <div style="margin: 50px 0">
                        	<button type="submit" class="btn btn-primary btn-lg" style="width: 100%">
                                Calculate
                            </button>
                        </div>
                    </form>
				</div>
			</div>
		<form>
	</div>
</div>
