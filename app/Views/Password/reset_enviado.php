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
   <div class="col-lg-8 mx-auto">
      <div class="info d-flex align-items-center">
         <div class="content">
            <div row>
               <div class="logo">
                  <h2><?php echo $titulo;  ?></h2>
               </div>
               <p>Caso não encontre na caixa de entrada verifique o spam ou lixo eletronico!</p>
               <div class="text-center">
                  <a href="<?php echo site_url('login') ?>" class="btn btn-dark mt-3">Fazer login</a>
               </div>
            </div>
         </div>

      </div>
   </div>
   <!-- Form Panel    -->
   <div class="col-lg-6 bg-white d-none">
      <div class="form d-flex align-items-center">
         <div class="content">


            <div class="text-center">
               <a href="<?php echo site_url('login') ?>" class="btn btn-danger">Fazer login</a>
            </div>
         </div>
      </div>
   </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<?= $this->endSection() ?>