<script src="<?php echo base_url(); ?>public/js/jquery.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/bootstrap.min.js"></script>

<script src="<?php echo base_url(); ?>public/js/3Dmol.js"></script>

<script src="<?php echo base_url(); ?>public/js/dt.js"></script>

<script src="<?php echo base_url(); ?>public/js/scripts.js"></script>

 <script>
    $(document).ready(function(){
        $("#all_residues").click(function () {
            $('#residue').attr("disabled", $(this).is(":checked"));
        });
    });
</script>