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
   <div class="col-lg-6 mx-auto">
      <div class="form d-flex align-items-center bg-white">
         <div class="content">

            <div class="mt-5 text-center text-black">
               <p><?php echo $item->codigo_barras; ?></p>
               <p><?php echo $item->codigo_interno; ?></p>
               <p><?php echo $item->nome; ?></p>
               <p><button class="btn btn-primary bg-dark" onclick="window.print();">Imprimir</button></p>
            </div>

         </div>

      </div>
   </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<?= $this->endSection() ?>