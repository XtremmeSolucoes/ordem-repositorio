<?= $this->extend('Layout/Autenticacao/principal_autenticacao') ?>


<?= $this->section('titulo') ?>
<?= $titulo ?>
<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!-- aqui os estilos da view -->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<!-- aqui os conteudos da view  -->
<div class="row">
   <!-- Logo & Information Panel-->
   <div class="col-lg-6">
      <div class="info d-flex align-items-center">
         <div class="content">
            <div class="logo">
               <h1><?php echo $titulo;  ?></h1>
            </div>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
         </div>
      </div>
   </div>
   <!-- Form Panel    -->
   <div class="col-lg-6 bg-white">
      <div class="form d-flex align-items-center">
         <div class="content">

            <?php echo form_open('/', ['id' => 'form', 'class' => 'form-validate']); ?>

            <div id="response">

            </div>            

            <div class="form-group">
               <input id="login-username" type="text" name="email" required data-msg="Por favor informe seu E-mail" class="input-material">
               <label for="login-username" class="label-material">Seu E-mail de acesso</label>
            </div>
            <div class="form-group">
               <input id="login-password" type="password" name="password" required data-msg="Por favor informe sua senha" class="input-material">
               <label for="login-password" class="label-material">Sua senha</label>
            </div>

            <input id="btn-login" type="submit" class="btn btn-primary" value="Entrar">

            <?php echo form_close(); ?>

            <a href="<?php echo site_url('esqueci') ?>" class="forgot-pass mt-3">Esqueceu a sua senha?</a>
         </div>
      </div>
   </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<!-- aqui os scripts da view  -->
<script>
   $(document).ready(function() {

      $("#form").on('submit', function(e) {
         e.preventDefault();

         $.ajax({
            type: 'POST',
            url: '<?= site_url('login/criar'); ?>',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
               $("#response").html('');
               $("#btn-login").val('Por favor aguarde...');

            },
            success: function(response) {
               $("#btn-login").val('Entrar');
               $("#btn-login").removeAttr("disabled");
               $('[name=csrf_ordem]').val(response.token);


               if (!response.erro) {

                  //tudo deu certinho com a atualização do usuário

                  window.location.href = "<?= site_url() ?>" + response.redirect;

               }

               if (response.erro) {

                  $("#response").html('<div class="alert alert-danger">' + response.erro + '</div>');

                  if (response.erros_model) {

                     $.each(response.erros_model, function(key, value) {

                        $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>');

                     });

                  }

               }

            },
            error: function() {
               alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte tecnico!');
               $("#btn-login").val('Entrar');
               $("#btn-login").removeAttr("disabled");
            },
         });
      });

      $("#form").submit(function() {

         $(this).find(":submit").attr('disabled', 'disabled');

      });

   });
</script>

<?= $this->endSection() ?>




