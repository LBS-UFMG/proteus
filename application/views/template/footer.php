<footer>
    <div class="container">
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Laboratório de Bioinformática e Sistemas (LBS)</strong></p>
                <p style="font-size: 12px"><strong>Universidade Federal de Minas Gerais</strong> <br>Av. Pres. Antônio Carlos, 6627 - Pampulha, Belo Horizonte - MG, 31270-901 </p>
                <p style="font-size: 12px">Instituto de Ciências Exatas (ICEx), Departamento de Ciência da Computação (DCC)</p> 
                <p style="font-size: 12px">Anexo U, 4º andar, Sala: 4340 | Telefone +55 31 3409-5896</p>
            </div>
            <div class="col-md-6 right">
                <br>
                <div style="float:right; margin-bottom:20px"><a target="_blank" alt="DCC" href="http://dcc.ufmg.br"><img src="<?php echo base_url(); ?>public/img/dcc.png"></a></div>
                <div style="float:right; margin-bottom:20px"><a target="_blank" alt="UFMG" href="http://ufmg.br"><img src="<?php echo base_url(); ?>public/img/ufmg.png"></a></div>
                <div style="clear:both"></div>
            
            </div>
        </div>
                                
    </div>
    
</footer>
<div id="pos_footer">
    ©2018 LBS | Created by <a href="#" data-toggle="modal" data-target="#about">LBS team</a>.
</div>

<!-- MODAL -->
<div class="modal fade" tabindex="-1" id="about" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>   
                <div style="text-align: center">
                    <img src="<?php echo base_url(); ?>public/img/logo3.svg">
                </div>           
            </div>
                        
            <div class="modal-body">    
               
                <div class="row">
                    <div class="col-md-5">
                        <p style="text-align:center">
                        <img src="<?php echo base_url(); ?>public/img/pse.png" class="img-responsive"></p>
                    </div>

                    <div class="col-md-7">
                        <p style="padding-top:10px">
                            <strong>Designed by: </strong><br>
                            <a href="http://www.dcc.ufmg.br/~raquelcm" target="_blank">Prof. Dr. Raquel de Melo-Minardi</a><br>
                            <a href="http://somos.ufmg.br/professor/ronaldo-alves-pinto-nagem" target="_blank">Prof. Dr. Ronaldo Alves Pinto Nagem</a><br><br>                    
                            <strong>ProteusDB / ProteusWEB / Back-end: </strong><br>
                            <a href="https://br.linkedin.com/in/jose-renato-barroso" target="_blank">José Renato</a><br><br>

                            <strong>PSE / ProteusWEB / Front-end: </strong><br>
                            <a href="http://diegomariano.com" target="_blank">Diego Mariano</a><br><br>
                            <strong>Financing and support: </strong><br>
                            <a href="http://www.capes.gov.br" target="_blank">CAPES</a> / <a href="http://ufmg.br" target="_blank">UFMG</a> / <a href="http://bioinfo.dcc.ufmg.br" target="_blank">LBS</a><br>
                        </p>
                    </div>
                </div>
            </div>
                        
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- MODAL HELP -->
<div class="modal fade" tabindex="-1" id="help" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                <h4 class="modal-title"><strong>HELP</strong></h4>
               
            </div>
                        
            <div class="modal-body">    
                <h3>How does Proteus work?</h3>
                <p style="text-align: center">
                    <img src="<?php echo base_url(); ?>public/img/pse.png">
                </p>
                <p>Proteus is composed by three main structures:</p>
                <ul><li><b>ProteusWEB</b>: Webtool that allows the creation of projects of protein engineering;</li><li><b>ProteusDB</b>: database of contacts among side chains of amino acids;</li><li><b>PSE</b>: <i>Proteus Search Engine</i>; powerful search tool that combines SVD clustering and Structural Signature Variation (SSV) for data mining, and structural alignment for detection of mutations.</li></ul>
            </div>
                        
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>