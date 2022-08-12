<link rel="stylesheet" href="<?php echo base_url(); ?>public/css/dt.css">

<div style="background-color:#e4e4e4; height:180px; margin-bottom: 20px">
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-xs-12">
                <br><br>
                <h2 class="title_h2">
                    <div class="dropdown" title="Export files">
                        <?php echo $id; ?>   
                        <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dLabel" >
                            <li><a href="#"><strong>Export</strong></a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo base_url(); ?>/public/data/<?php echo $id; ?>/mutations_details.csv">Mutations (CSV)</a></li>
                            <li><a href="<?php echo base_url(); ?>/public/data/<?php echo $id; ?>/mutations.csv">Mutations grouped by sites</a></li>
                            <li><a href="<?php echo base_url(); ?>/public/data/<?php echo $id; ?>/origin.pdb">PDB file</a></li>
                        </ul>
                    </div>
                </h2>
                <!--<p>{{subtitle}} &#8491;</p>-->
                <p><strong><a href='<?php echo base_url(); ?>result/id/<?php echo $id; ?>'><?php echo base_url(); ?>result/id/<?php echo $id; ?></a> </strong></p>
            </div>

            <div class="col-md-3 col-xs-12" style="height: 180px; background-color: #00bc9e; color:#fff">
                <p style="text-align: center; font-size: 90px; padding-top:10px">
                    <strong id="mutations_found_title"><?php echo $total_results; ?></strong>
                </p>

                <p style="font-size: 12px; text-align:center; margin-top: -20px">
                    pairs of mutations were found
                    <a href="#" data-toggle="modal" data-target="#help" style="color:#fff"><span class="glyphicon glyphicon-info-sign"></span></a>
                </p>
            </div>
        </div> 
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-md-9" ng-if="cttlok">
            <!--
            <h3><a style="color:#111; text-decoration:none" data-toggle="collapse" data-target="#summary">
              <span class="glyphicon glyphicon-collapse-down"></span> Summary</h3>
            </a> -->

            <div class="btn-group btn-group-justified" role="group" aria-label="...">
              <span class="input-group-addon" id="basic-addon1"><b>Filters: </b></span>
              <div class="btn-group" role="group">
                <button type="button" id="show_all" class="btn btn-primary">Show all</button>
              </div>
              <div class="btn-group" role="group">
                <button type="button" id="positive" class="btn btn-danger">Positive</button>
              </div>
              <div class="btn-group" role="group">
                <button type="button" id="negative" class="btn btn-info">Negative</button>
              </div>
              <div class="btn-group" role="group">
                <button type="button" id="hydrophobic" class="btn btn-success">Hydrophobic</button>
              </div>
              <div class="btn-group" role="group">
                <button type="button" id="aromatic" class="btn btn-warning">Aromatic</button>
              </div>
              <div class="btn-group" role="group">
                <button type="button" id="disulfide" class="btn btn-default">Disulfide</button>
              </div>
            </div>
            <br>
            <!--
            <br>

            <div class="table-responsive collapse" id="summary">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Sites</th>
                            <th>Mutation suggested</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach($mutations as $mutation){ ?> 
                        <?php $m = explode(';',$mutation); $len_mut = count($m); ?>
                        <tr onmouseover="selectID(glviewer,this.children[0].innerHTML,0,'<?php echo $chain; ?>')">

                            <td><?php echo $m[1]; ?></td>

                            <td>
                                <ul class="list-inline">
                                    <?php for($i = 3; $i < $len_mut; $i+=2){ ?>
                                    <li>
                                        <a onclick="highlight('#'+<?php echo $m[$i-1]; ?>)" href="#<?php echo $m[$i-1]; ?>"><?php echo $m[$i]; ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div> -->

            <div class="table-responsive">
                <table class="display" id="mut">
                    <thead>
                        <tr>
                            <th>Mutation</th>
                            <th>Template</th>
                            <th>Chain</th>
                            <th title="Residue 1 (origin PDB)">R1</th>
                            <th title="Residue 2 (origin PDB)">R2</th>
                            <th>RMSD</th>
                            <th title="&Delta;&Delta;G predicted by Maestro">&Delta;&Delta;G</th>
                            <th title="Stereochemical clash">Clash</th>
                            <th>Show template</th>
                            <!--<th title="Simulate mutation">Mutate</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mutations_details as $mutation_detail){ ?> 
                        <?php $m = explode(';',$mutation_detail); $len_mut = count($m); ?>
                        <tr onmouseover="selectID(glviewer,this.children[0].innerHTML,1,'<?php echo $chain; ?>')" id="<?php echo $m[0]; ?>">
                            <td><?php echo $m[1]; ?></td>
                            <td>
                                <a href="http://www.rcsb.org/pdb/explore.do?structureId=<?php echo $m[2]; ?>" target="_blank">
                                    <?php echo $m[2]; ?>
                                </a>
                            </td>
                            <td><?php echo $m[3]; ?></td>
                            <td><?php echo $m[4]; ?></td>
                            <td><?php echo $m[5]; ?></td>
                            <td><?php echo $m[6]; ?></td>
                            <td><?php echo $m[7]; ?></td>
                            <td><?php echo $m[8]; ?></td>
                            <td><button type="button" id="<?php echo $m[2]; ?>_<?php echo $m[4]; ?>_<?php echo $m[5]; ?>" class="btn btn-primary btn-sm preview_button" onclick="selectPDB('ID_<?php echo $m[1]; ?>_<?php echo $m[4]; ?>_<?php echo $m[5]; ?>_<?php echo $m[3]; ?>')" data-toggle="modal" data-target="#preview">Show</button></td>
                            <!--
                            <?php 
                            $tmp = explode('/',$m[1]); 
                            if(count($tmp) == 2){
                            ?>
                            <td><a target="_blank" class="btn btn-primary btn-sm" href="<?php echo base_url(); ?>result/mutate/<?php echo $id.'_'.$chain.'_'.$tmp[0].'_'.$tmp[1];   ?>">Mutate</a></td>
                            <?php } else { ?>
                            <td><a target="_blank" class="btn btn-primary btn-sm" href="<?php echo base_url(); ?>result/mutate/<?php echo $id.'_'.$chain.'_'.$tmp[0];   ?>">Mutate</a></td>
                            <?php } ?> -->

                       </tr>
                       <?php } ?>
                   </tbody>
               </table>
           </div>
       </div>


        <div class="col-md-3">

            <style>.affix{ top: 100px; z-index: 9999 !important; }</style>

            <div data-spy="affix" id="affix" data-offset-top="240" data-offset-bottom="250">
                <div id="pdb" style="height: 400px; width: 280px"></div>
                <p style="color:#ccc; text-align: right">Wild protein</p>
            </div>
        </div>
    </div>
</div>

<!-- Return to Top -->
<a href="#" title="Return to top" style="font-size:25px; position:fixed; right:10px; bottom:10px"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a>

<br><br>

<!-- MODAL PREVIEW -->
<div class="modal fade" tabindex="-1" id="preview" role="dialog" style="z-index: 9998 !important; ">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                <h4 class="modal-title"><strong>Wild (blue): <span id="pdb_origin_name"></span> | Mutations (green): <span id="residues_pdb_origin_name"></span></strong></h4>
               
            </div>
                        
            <div class="modal-body" style="height: 400px; width:598px">    
                <div id="preview_contact" style="height: 400px; width:598px"></div>
            </div>
                        
            <div class="modal-footer">
		<a href="<?php echo base_url(); ?>/public/data/<?php echo $id; ?>/origin.pdb" class="btn btn-primary">Download PDB (wild)</a>
		<a href="#" id="mut_download" class="btn btn-success">Download PDB (mutant)</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="<?php echo base_url(); ?>public/js/jquery.min.js"></script>

<script>

$(document).ready( function () {
    var table = $('#mut').DataTable( {
        "paging": false
    } );
    $('#negative').click(function(){
        table.columns(0).search("[0-9](D|E)",true, false).draw();    
    });
    $('#positive').click(function(){
        table.columns(0).search("[0-9](R|H|K)",true, false).draw();    
    });
    $('#aromatic').click(function(){
        table.columns(0).search("[0-9](F|W|Y)",true, false).draw();    
    });
    $('#hydrophobic').click(function(){
        table.columns(0).search("[0-9](A|V|L|F|P|W|M|I)",true, false).draw();    
    });
    $('#disulfide').click(function(){
        table.columns(0).search("[0-9]C.*[0-9]C",true, false).draw();   
    });
    $('#show_all').click(function(){
        table.columns(0).search("[A-Z]",true,false).draw();    
    });
       
    
} );


$('nav').css('position', 'relative');

function highlight(pos){
    $(pos).css("background-color","#f2dede");
}

// 3DMOL **********************************************************************
/* Select ID */
function selectID(glviewer,residues,type,chain){

    residues = residues.split("/");

    if(type==0){
        var res1 = residues[0].substr(1);
        var res2 = residues[1].substr(1);
    }
    else{
        var len1 = residues[0].length;
        var len2 = residues[1].length;
        var res1 = residues[0].substr(1,len1-2);
        var res2 = residues[1].substr(1,len2-2);
    }
    
    
    glviewer.setStyle({},{line:{color:'grey'},cartoon:{color:'white'}}); /* Cartoon multi-color */
    glviewer.setStyle({resi:res1},{stick:{colorscheme:'whiteCarbon'}}); 
    glviewer.setStyle({resi:res2},{stick:{colorscheme:'whiteCarbon'}}); 
    
    glviewer.zoomTo({resi: [res1,res2], chain:chain}); 
    
    glviewer.render();

}


function selectPDB(id){

    var ids = id.split("_");
    var mut = ids[1].replace("/","_");

    try {
        var pos = mut.split("_");
        var pos1 = pos[0].substr(1,pos[0].length-2);
        var pos2 = pos[1].substr(1,pos[1].length-2);
        var pos1a = Number(pos1) - 1;
        var pos1d = Number(pos1) + 1;
        var pos2a = Number(pos2) - 1;
        var pos2d = Number(pos2) + 1;
        pos1a = pos1a.toString();
        pos1d = pos1d.toString();
        pos2a = pos2a.toString();
        pos2d = pos2d.toString();
    }
    catch(err) {
        var erro = 1;
    }
    

    var txt2 = "<?php echo base_url(); ?>public/data/<?php echo $id; ?>/mutations/"+mut+".pdb";
    $("#mut_download").attr("href", txt2);

    $.post(txt2, function(d) {

            moldata = data = d;

            // Creating visualization 
            glviewer2 = $3Dmol.createViewer("preview_contact", {
                defaultcolors : $3Dmol.rasmolElementColors
            });

            // Color background 
            glviewer2.setBackgroundColor(0xffffff);

            receptorModel = m = glviewer2.addModel(data, "pqr");

            // Name of the atoms 
            atoms = m.selectedAtoms({});
            for ( var i in atoms) {
                var atom = atoms[i];
                atom.clickable = true;
                atom.callback = atomcallback;
            }

            glviewer2.mapAtomProperties($3Dmol.applyPartialCharges);
            glviewer2.zoomTo();
            //glviewer2.render();


            // zoom in the residues
            //var res1 = ids[2].substr(1);
            //var res2 = ids[3].substr(1);

            //var chain2 = ids[4];
            
            glviewer2.setStyle({},{stick:{colorscheme:'greenCarbon'}}); // Cartoon multi-color 
            //glviewer2.setStyle({resi:res1},{stick:{colorscheme:'whiteCarbon'}}); 
            //glviewer2.setStyle({resi:res2},{stick:{colorscheme:'whiteCarbon'}}); 
            
            //glviewer2.zoomTo({resi: [res1,res2], chain: chain2}); 
            
            glviewer2.render();

    });

    var txt3 = "<?php echo base_url(); ?>public/data/<?php echo $id; ?>/tmp/"+pos1+"_"+pos2+".pdb";

    $.post(txt3, function(d) {

            moldata = data = d;

            receptorModel = m = glviewer2.addModel(data, "pqr");

            glviewer2.mapAtomProperties($3Dmol.applyPartialCharges);
            glviewer2.zoomTo();     
            glviewer2.setStyle({resi: [pos1a,pos1,pos1d,pos2a,pos2,pos2d]},{stick:{colorscheme:'blueCarbon'}}); 
            glviewer2.render();

    });

    // write title
    $('#pdb_origin_name').html(ids[1]);
    $('#residues_pdb_origin_name').html(ids[2]+'/'+ids[3]);

    var atomcallback = function(atom, viewer) {
        if (atom.clickLabel === undefined
            || !atom.clickLabel instanceof $3Dmol.Label) {
            atom.clickLabel = viewer.addLabel(atom.resn + " " + atom.resi + " ("+ atom.elem + ")", {
                fontSize : 10,
                position : {
                    x : atom.x,
                    y : atom.y,
                    z : atom.z
                },
                backgroundColor: "black"
            });
            atom.clicked = true;
        }

        //toggle label style
        else {

            if (atom.clicked) {
                var newstyle = atom.clickLabel.getStyle();
                newstyle.backgroundColor = 0x66ccff;

                viewer.setLabelStyle(atom.clickLabel, newstyle);
                atom.clicked = !atom.clicked;
            }
            else {
                viewer.removeLabel(atom.clickLabel);
                delete atom.clickLabel;
                atom.clicked = false;
            }
        }
    };

}

$(document).ready(function(){


    //var title_pdb = $(".title_h2").text();
    //title_pdb = title_pdb.split(": ")

    //var txt = "https://files.rcsb.org/download/"+title_pdb[1]+".pdb";
    var txt = "<?php echo base_url(); ?>public/data/<?php echo $id; ?>/origin.pdb";
    
    $.post(txt, function(d) {

        moldata = data = d;

        /* Creating visualization */
        glviewer = $3Dmol.createViewer("pdb", {
            defaultcolors : $3Dmol.rasmolElementColors
        });

        /* Color background */
        glviewer.setBackgroundColor(0xffffff);

        receptorModel = m = glviewer.addModel(data, "pqr");

        /* Type of visualization */
        glviewer.setStyle({},{line:{color:'grey'},cartoon:{color:'white'}}); /* Cartoon multi-color */
        
        /*glviewer.addSurface($3Dmol.SurfaceType, {opacity:0.3});  Surface */

        /* Name of the atoms */
        atoms = m.selectedAtoms({});
        for ( var i in atoms) {
            var atom = atoms[i];
            atom.clickable = true;
            atom.callback = atomcallback;
        }

        glviewer.mapAtomProperties($3Dmol.applyPartialCharges);
        glviewer.zoomTo();
        glviewer.render();
    });

    var atomcallback = function(atom, viewer) {
        if (atom.clickLabel === undefined
            || !atom.clickLabel instanceof $3Dmol.Label) {
            atom.clickLabel = viewer.addLabel(atom.resn + " " + atom.resi + " ("+ atom.elem + ")", {
                fontSize : 10,
                position : {
                    x : atom.x,
                    y : atom.y,
                    z : atom.z
                },
                backgroundColor: "black"
            });
            atom.clicked = true;
        }

        //toggle label style
        else {

            if (atom.clicked) {
                var newstyle = atom.clickLabel.getStyle();
                newstyle.backgroundColor = 0x66ccff;

                viewer.setLabelStyle(atom.clickLabel, newstyle);
                atom.clicked = !atom.clicked;
            }
            else {
                viewer.removeLabel(atom.clickLabel);
                delete atom.clickLabel;
                atom.clicked = false;
            }
        }
    };
});

</script>
