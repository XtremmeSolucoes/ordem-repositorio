<?= $this->extend('Layout/principal')?>


<?= $this->section('titulo') ?>
    <?= $titulo ?>
<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
   <!-- aqui os estilos da view -->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
   <!-- aqui os conteudos da view  -->
   <h1>AQUI VOU METER BRONCA!</h1>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
   <!-- aqui os scripts da view  -->
<?= $this->endSection() ?>