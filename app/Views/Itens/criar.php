<?= $this->extend('Layout/principal') ?>


<?= $this->section('titulo') ?>
<?= $titulo ?>
<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!-- aqui os estilos da view -->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<!-- aqui os conteudos da view  -->
<div class="row">
    <div class="col-lg-8">
        <div class="block">
            <div class="block-body">

                <!-- Exibir as mensagens do Backend -->
                <div id="response">
                
                
                </div>

                <?php echo form_open('/', ['id' => 'form'], ['id' => "$item->id"]) ?>

                <?php echo $this->include('Itens/_form'); ?>

                <div class="form-group mt-5 mb-2">
                    <input id="btn-salvar" type="submit" value="Salvar" class="btn btn-danger btn-sm mr-2">
                    <a href="<?php echo site_url("itens"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>

                <?php echo form_close(); ?>

            </div><!-- ./ block-body -->
        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<script src="<?php echo site_url('recursos/vendor/mask/jquery.mask.min.js') ?>"></script>

<script src="<?php echo site_url('recursos/vendor/mask/app.js') ?>"></script>
<script >
$(document).ready(function(){

    $("[name=tipo]").on("click", function () {

        if($(this).val() === "serviço"){

            $(".servico").hide("slow");
            $("[name=marca]").prop("disabled", true);
            $("[name=modelo]").prop("disabled", true);
            $("[name=estoque]").prop("disabled", true);
            $("[name=preco_custo]").prop("disabled", true);
            $("[name=controla_estoque]").prop("disabled", true);

        }else{
            $(".servico").show("slow");
            $("[name=marca]").prop("disabled", false);
            $("[name=modelo]").prop("disabled", false);
            $("[name=estoque]").prop("disabled", false);
            $("[name=preco_custo]").prop("disabled", false);
            $("[name=controla_estoque]").prop("disabled", false);
        }


    });

    $("#form").on('submit', function(e){
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: '<?= site_url('itens/cadastrar'); ?>',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(){
                $("#response").html('');
                $("#btn-salvar").val('Por favor aguarde...');

            },
            success: function(response){
                $("#btn-salvar").val('Salvar');
                $("#btn-salvar").removeAttr("disabled");
                $('[name=csrf_ordem]').val(response.token);


                if(!response.erro){

                    window.location.href = "<?= site_url("Itens/exibir/") ?>" + response.id;

                }

                if(response.erro){

                    $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');

                    if(response.erros_model){

                        $.each(response.erros_model, function(key, value){
                            
                            $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');

                        });

                    }

                }

            },
            error: function(){
                alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte tecnico!');
                $("#btn-salvar").val('Salvar');
                $("#btn-salvar").removeAttr("disabled");
            },
        });
    });

    $("#form").submit(function(){

        $(this).find(":submit").attr('disabled', 'disabled');

    });
    
});
</script>
<?= $this->endSection() ?>