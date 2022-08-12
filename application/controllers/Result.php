<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Result extends CI_Controller {

	public function id($id = 'null'){
		$id = substr($id, 0, 6);
		# ********************* Search project *********************
		# Read directory
		chdir('public/data');
		$projects = glob("{*}", GLOB_BRACE);
		$project_exists = False;

		# Is the id unique? If not, create a new!
		for($i = 0; $i < (count($projects)); $i++){
			if($projects[$i] == $id){
				$project_exists = True;
			}
		}

		# Project does not exist
		if(!$project_exists){
			$view = 'project_fail';
			$data = array();
			$this->template->show($view,$data);
		}
		else{

			# Verifying status 
			$status = fopen('../../public/data/'.$id.'/status.txt','r');
			while (($line = fgets($status, 4096)) !== false) {
				$pstatus = trim($line);
			}
			fclose($status);

			# Load project data 
			$pj = array();
			$pj_data = fopen('../../public/data/'.$id.'/data.txt','r');
			while (($line = fgets($pj_data, 4096)) !== false) {
				array_push($pj,$line);
			}			
			fclose($pj_data);

			# ********************* Return variables *********************
			$data = array();
			$data['id'] = $id;
			$data['pdbid'] = $pj[1];
			$data['chain'] = trim($pj[2]);
			$data['residue'] = $pj[3];
			if(count($pj) == 5){
				$data['total_tests'] = $pj[4];
				$data['total_results'] = 0;
			}
			elseif(count($pj) >= 6){
				$data['total_tests'] = $pj[4];
				$data['total_results'] = $pj[5];
			}
			else{
				$data['total_tests'] = 0;
				$data['total_results'] = 0;
			}
			
			# If is not ready
			if($pstatus < 100){

				# Calculating remaininng time
				$tests_remaing = (100 - $pstatus)*$data['total_tests']/100;
				#$time_min = $tests_remaing * 0.05; # 0.05 min (3 seg) per test
				$time_min = $tests_remaing * 0.07;
				#$time_min = $tests_remaing * 0.2; # servidor remoto: 0.2 min (12 seg)
				#$time_min = $tests_remaing * 0.1; # ligase: 0.1 min (6 seg) per test
				$time_hour = intval($time_min / 60);
				$time_min = $time_min % 60; 

				$data['hour'] = $time_hour;
				$data['min'] = $time_min;

				$view = 'project_running';
				$data['status'] = $pstatus;
				
				$this->template->show($view,$data);
			}
			else{

				# ********************* Loading data *********************
				# Mutations
				$mutations_file = fopen('../../public/data/'.$id.'/mutations.csv','r');
				$mutations = array();
				while (($line = fgets($mutations_file, 4096)) !== false) {
					array_push($mutations, $line);
				}
				fclose($mutations_file);

				# Mutations datails
				$mut_det_file = fopen('../../public/data/'.$id.'/mutations_details.csv','r');
				$mutations_details = array();
				while (($line = fgets($mut_det_file, 4096)) !== false) {
					array_push($mutations_details, $line);
				}
				fclose($mut_det_file);
				
				$view = 'project';

				// Load template
				$data['mutations'] = $mutations;
				$data['mutations_details'] = $mutations_details;
				$this->template->show($view,$data);
			}
		}
	}

	public function create(){

		# ********************* Create new ID *********************
		$id = $this->generateRandomString(6);
		
		# Read directory
		chdir('public/data');
		$arquivos = glob("{*}", GLOB_BRACE);

		# Is the id unique? If not, create a new!
		for($i = 0; $i < (count($arquivos)); $i++){
			if($arquivos[$i] == $id){
				$id = $this->generateRandomString(6);
				$i = 0;
			}
		}

		# Create project folder 
		mkdir("../../public/data/$id");
		mkdir("../../public/data/$id/tmp");
		chmod("../../public/data/$id", 0777);
		chmod("../../public/data/$id/tmp", 0777);


		# ********************* Receiving post data *********************

		$chain = $this->input->post('chain');
		$residue = $this->input->post('residue');
		$project_name = $this->input->post('project_name');


		# Saving project data
		$project = fopen('../../public/data/'.$id.'/data.txt','w');
		fwrite($project,$id."\n"); #id
		fwrite($project,$project_name."\n"); #project name
		fwrite($project,$chain."\n"); #chain
		fwrite($project,$residue."\n"); #residue
		fclose($project);

		$stat = fopen('../../public/data/'.$id.'/status.txt','w');
		fwrite($stat,'0');
		fclose($stat);


		$config['upload_path'] = "../../public/data/$id";
        $config['allowed_types'] = '*';
        $config['max_size'] = 2048;
        if(!empty($residue)){
			$config['file_name'] = 'raw.pdb';
		}
		else{
			$config['file_name'] = 'origin.pdb';
		}

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('pdb')){
        	$error = array('error' => $this->upload->display_errors());
        	print_r($error);
        }
        else{
        	$data = array('upload_data' => $this->upload->data());
        }

        # Security
        #chmod("../../public/data/$id", 0644);
		
		# Cut part of the protein
		if(!empty($residue)){
			system("python ../../application/bin/cut_protein.py $id $chain $residue");
		}


		# START PROTEUS PIPELINE *******************************************
		system("nohup sh ../../application/bin/proteus_pipeline.sh $id $chain $project_name &");


		$view = 'project_created';
		$data = array();

		// Load template
		$data['id'] = $id;
		$this->template->show($view,$data);
		#redirect('/result/id/'.$id, 'refresh');


	}

	public function mutate($id = 'null'){

		#$id = "ASDF12_A_A123G_A321C";

		$codes = explode("_",$id);

		if(count($codes) == 4){

			$id_project = $codes[0];
			$chain = $codes[1];

			$res1 = $codes[2];
			$pos1 = substr($res1, 1, -1);
			$mut1 = substr($res1, -1);
			$mut1 = $this->one_to_three($mut1);

			$pos1_0 = $pos1-1;
			$pos1_2 = $pos1+1;

			$res2 = $codes[3];
			$pos2 = substr($res2, 1, -1);
			$mut2 = substr($res2, -1);
			$mut2 = $this->one_to_three($mut2);

			$pos2_0 = $pos2-1;
			$pos2_2 = $pos2+1;

			if(file_exists($id_project.'/wild.pdb')){
				$file = 'wild.pdb';
			}
			else{
				$file = 'origin.pdb';
			}


			# Create folder mutations
			@mkdir("public/data/$id_project/mutations", 0777);
			#chmod("public/data/$id_project/mutations", 0777);

			# Create 1PPF.pgn
			$ppf = fopen("public/data/$id_project/mutations/$id.pgn", "w");

			$rec = "
			mol new public/data/$id_project/$file 
			set prot [atomselect top \"protein\"]
			\$prot writepdb public/data/$id_project/input.pdb
			package require psfgen
			topology application/bin/top_all27_prot_na.rtf
			pdbalias atom ILE CD1 CD
			segment $chain {pdb public/data/$id_project/input.pdb}
			coordpdb public/data/$id_project/input.pdb $chain
			guesscoord
			writepdb public/data/$id_project/mutations/mutant-top.pdb
			writepsf public/data/$id_project/mutations/mutant-top.psf

			package require mutator

			mutator -psf public/data/$id_project/mutations/mutant-top.psf -pdb public/data/$id_project/mutations/mutant-top.pdb -o public/data/$id_project/mutations/mutant-first  -ressegname $chain -resid $pos1 -mut $mut1
			mutator -psf public/data/$id_project/mutations/mutant-first.psf -pdb public/data/$id_project/mutations/mutant-first.pdb -o public/data/$id_project/mutations/mutant -ressegname A -resid $pos2 -mut $mut2

			mol new public/data/$id_project/mutations/mutant-mut.pdb

			set all [atomselect top all]

			set sel [atomselect top \"not resid $pos1_0 $pos1 $pos1_2 $pos2_0 $pos2 $pos2_2 \"]
			\$all set beta 0

			\$sel set beta 1

			\$all writepdb public/data/$id_project/mutations/restriction1.pdb";

			fwrite($ppf, $rec);
			fclose($ppf);
		}
		else{
			$id_project = $codes[0];
			$chain = $codes[1];

			$res1 = $codes[2];
			$pos1 = substr($res1, 1, -1);
			$mut1 = substr($res1, -1);
			$mut1 = $this->one_to_three($mut1);

			$pos1_0 = $pos1-1;
			$pos1_2 = $pos1+1;

			if(file_exists($id_project.'/wild.pdb')){
				$file = 'wild.pdb';
			}
			else{
				$file = 'origin.pdb';
			}


			# Create folder mutations
			@mkdir("public/data/$id_project/mutations", 0777);
			#chmod("public/data/$id_project/mutations", 0777);

			# Create 1PPF.pgn
			$ppf = fopen("public/data/$id_project/mutations/$id.pgn", "w");

			$rec = "
			mol new public/data/$id_project/$file 
			set prot [atomselect top \"protein\"]
			\$prot writepdb public/data/$id_project/input.pdb
			package require psfgen
			topology application/bin/top_all27_prot_na.rtf
			pdbalias atom ILE CD1 CD
			segment $chain {pdb public/data/$id_project/input.pdb}
			coordpdb public/data/$id_project/input.pdb $chain
			guesscoord
			writepdb public/data/$id_project/mutations/mutant-top.pdb
			writepsf public/data/$id_project/mutations/mutant-top.psf

			package require mutator

			mutator -psf public/data/$id_project/mutations/mutant-top.psf -pdb public/data/$id_project/mutations/mutant-top.pdb -o public/data/$id_project/mutations/mutant -ressegname A -resid $pos1 -mut $mut1

			mol new public/data/$id_project/mutations/mutant-mut.pdb

			set all [atomselect top all]

			set sel [atomselect top \"not resid $pos1_0 $pos1 $pos1_2  \"]
			\$all set beta 0

			\$sel set beta 1

			\$all writepdb public/data/$id_project/mutations/restriction1.pdb";

			fwrite($ppf, $rec);
			fclose($ppf);
		}
		
		$ppf2 = fopen("public/data/$id_project/mutations/minimization.conf", "w");

		$rec2 = "#############################################################
## JOB DESCRIPTION                                         ##
#############################################################

# Minimization and Equilibration of
#acetilcholinesterase with sanguinine


#############################################################
## ADJUSTABLE PARAMETERS                                   ##
#############################################################
#reiniciando a simulaÃÃo
#bincoordinates     .restart.coor
#binvelocities      .restart.vel
#extendedSystem     .restart.xsc

structure          /var/www/html/proteus/public/data/$id_project/mutations/mutant.psf
coordinates        /var/www/html/proteus/public/data/$id_project/mutations/mutant.pdb
set temperature    300 
set outputname     /var/www/html/proteus/public/data/$id_project/mutations/$id
firsttimestep      0


#############################################################
##amber simulation                                         ##
#############################################################
#amber              yes
#parmfile           lyc-5resflex-frame1-for-simu.prmtop
#ambercoor         .inpcrd
#readexclusions     yes
#scnb               2.0 




#############################################################
## SIMULATION PARAMETERS                                   ##
#############################################################

# Input
paraTypeCharmm	    on
parameters          /var/www/html/proteus/application/bin/par_all27_prot_na_CYC.prm
temperature         \$temperature

#dinÃmica com restriÃÃo 

#constraints         on
#consexp             2
#consref             /var/www/html/proteus/public/data/$id_project/mutations/restriction1.pdb
#conskfile           /var/www/html/proteus/public/data/$id_project/mutations/restriction1.pdb
#conskcol            B

fixedAtoms on
fixedAtomsForces on
fixedAtomsFile /var/www/html/proteus/public/data/$id_project/mutations/restriction1.pdb
fixedAtomsCol B

# Force-Field Parameters
exclude             scaled1-4
1-4scaling          1.0
cutoff              12.
switching           on
switchdist          10.
pairlistdist        13.5


# Integrator Parameters
timestep           2.0  ;# 2.0 fs/step
rigidBonds          all  ;# needed for 2fs steps
nonbondedFreq       1
fullElectFrequency  2  
stepspercycle       10


# Constant Temperature Control
langevin            on    ;# do langevin dynamics
langevinDamping     2     ;# damping coefficient (gamma) of 2/ps
langevinTemp        \$temperature
langevinHydrogen    off    ;# don't couple langevin bath to hydrogens


# Periodic Boundary Conditions
cellBasisVector1   69.    0.   0.
cellBasisVector2    0.   69.   0.
cellBasisVector3    0.    0.  80.
cellOrigin         1.9011341333389282 0.27419817447662354 4.809254169464111
wrapAll             on
 

# PME (for full-system periodic electrostatics)
PME                 yes
PMEGridSizeX        81
PMEGridSizeY        81
PMEGridSizeZ        81


# Constant Pressure Control (variable volume)
useGroupPressure      yes ;# needed for rigidBonds
useFlexibleCell       no
useConstantArea       no
langevinPiston        on
langevinPistonTarget  1.01325 ;#  in bar -> 1 atm
langevinPistonPeriod  100.
langevinPistonDecay   50.
langevinPistonTemp    \$temperature


# Output
outputName          \$outputname
restartfreq         1000     ;# 1000steps = every 2 ps
dcdfreq             2000
outputEnergies      1000
outputPressure      1000


#############################################################
## EXECUTION SCRIPT                                        ##
#############################################################

# Minimization
minimize            200
reinitvels          \$temperature
#run 100000 ; #200 ps

		";

		fwrite($ppf2, $rec2);
		fclose($ppf2);

		# part3
		$ppf3 = fopen("public/data/$id_project/mutations/finalpdb.pgn", "w");

		$rec3 = "
		mol new public/data/$id_project/mutations/mutant.psf 
		mol addfile public/data/$id_project/mutations/$id.coor
		set prot2 [atomselect top \"noh\"]
		\$prot2 writepdb public/data/$id_project/mutations/$id.pdb";

		fwrite($ppf3, $rec3);
		fclose($ppf3);

		# RUN
		system("vmd < public/data/$id_project/mutations/$id.pgn >> public/data/$id_project/log.txt"); #>> public/data/$id_project/log.txt
		system("application/bin/namd2 public/data/$id_project/mutations/minimization.conf >> public/data/$id_project/log.txt");
		system("vmd < public/data/$id_project/mutations/finalpdb.pgn >> public/data/$id_project/log.txt");

		$view = 'downloads';
		$data['id'] = $id;
		$data['id_project'] = $id_project;
		$this->template->show($view,$data);

		#redirect("public/data/$id_project/mutations/$id.pdb", 'refresh');

	}

	private function one_to_three($a1){
		$a3 = array(
			'A'=>'ALA',
			'C'=>'CYS',
			'D'=>'ASP',
			'E'=>'GLU',
			'F'=>'PHE',
			'G'=>'GLY',
			'H'=>'HSE',
			'I'=>'ILE',
			'K'=>'LYS',
			'L'=>'LEU',
			'M'=>'MET',
			'N'=>'ASN',
			'P'=>'PRO',
			'Q'=>'GLN',
			'R'=>'ARG',
			'S'=>'SER',
			'T'=>'THR',
			'V'=>'VAL',
			'W'=>'TRP',
			'Y'=>'TYR'
		);

		return $a3[$a1];
	}

	private function generateRandomString($size){

		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$randomString = '';
		
		for($i = 0; $i < $size; $i = $i+1){
			$randomString .= $chars[mt_rand(0,35)];
		}

		return $randomString;

	}

}



/*
		$view = 'run';
		$data = array();

		// Receiving post data
		$chain = $this->input->post('chain');
		$residue = $this->input->post('residue');
		$email = $this->input->post('email');


		// Creating randomic id
		$time = microtime();
		$id = md5($project_name.$time);
		$id = strtoupper('ID'.substr($id, 0, 7));
		mkdir('./public/data/'.$id);

		//Files
		$wild_file = $_FILES['wild_file'];
		$mutant_file = $_FILES['mutant_file'];
		$templates_file = $_FILES['templates_file'];

		$pdb = array(
			'upload_path'    => './public/data/'.$id,
			'allowed_types'  => '*',
			'file_name'      => 'pdb.pdb',
			'max_size'       => '2048'
		);


		$this->load->library('upload');

		//wild
		$this->upload->initialize($config_wild);
		if(!$this->upload->do_upload('wild_file')) 
			echo $this->upload->display_errors();

		
		//unzip
		$zip = new ZipArchive;
		$res = $zip->open('./public/data/'.$id.'/templates/templates.zip');
		$zip->extractTo('./public/data/'.$id.'/templates');
		$zip->close();


		// ********** aCSM *************
		// Creating list.txt
		$pt_wm = fopen('./public/data/'.$id.'/list.txt', "w+");
		fwrite($pt_wm, '../data/'.$id."/wild.pdb\n../data/".$id.'/mutant.pdb');
		fclose($pt_wm);

		// Creating templates.txt
		$dir = dir('./public/data/'.$id.'/templates');
		$pt_t = fopen('./public/data/'.$id.'/templates.txt', "w+");

		while($file = $dir -> read()){
			if(substr($file,-3) == 'pdb'){
				fwrite($pt_t,'templates/'.$file."\n");
			}
		}
		$dir -> close();

		fclose($pt_t);

		// Run aCSM
		system("cd public/data/".$id." && perl ../../bin/aCSM/aCSM.pl templates.txt templates.csv 0.1 10.0 2 > log.txt");

		system("cd public/data/".$id." && perl ../../bin/aCSM/aCSM.pl list.txt result.csv 0.1 10.0 2 >> log.txt");

		//Calculating GTS
		$signatures = file("public/data/$id/result.csv"); 
		$templates = file("public/data/$id/template.csv");

		$wild = explode(",",$signatures[0]);
		$mutant = explode(",",$signatures[1]);


		//Calculating dGTSwt and dGTSmt ************************** 
		$dGTSmt = array();
		$dGTSwt = array();

		foreach($templates as $template){
			$distw = 0;
			$distm = 0;

			$t = explode(",", $template);
			$len = count($t);

			// WILD **********************************************
			for($j = 0; $j < $len; $j++){
				$distw += pow((double)$wild[$j] - (double)$t[$j], 2);
			}
			$distw = sqrt($distw);
			array_push($dGTSwt, $distw);

			// MUTANT **********************************************
			for($k = 0; $k < $len; $k++){
				$distm += pow((double)$mutant[$k] - (double)$t[$k], 2);
			}
			$distm = sqrt($distm);
			array_push($dGTSmt, $distm);

		}

		//Calculating ddGTS

		$min_wt = min($dGTSwt);
		$key_wt = array_search($min_wt, $dGTSwt);

		$min_mt = min($dGTSmt);
		$key_mt = array_search($min_mt, $dGTSmt);

		$ddGTS = $min_mt - $min_wt;

		$pt = fopen("public/data/$id/data.txt","a+");
		fwrite($pt,"\n".$key_wt."\n".$min_wt."\n".$templates_names[$key_mt]."\n".$min_mt."\n".$ddGTS);
		fclose($pt);

		// Load template
		$data['id'] = $id;
		$this->template->show($view,$data);

		*/
