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
    <div class="col-lg-6">
        <div class="block">
            <div class="block-body">


                <?php echo form_open("ordens/excluir/$ordem->codigo") ?>

                <div class="alert alert-warning" role="alert">
                    Tem certeza da que quer excluir a ordem: <b><?php echo $ordem->codigo ?></b>?
                </div>

                <div class="form-group mt-5 mb-2">
                    <input codigo="btn-salvar" type="submit" value="Sim, pode excluir" class="btn btn-danger btn-sm mr-2">
                    <a href="<?php echo site_url("ordens/detalhes/$ordem->codigo"); ?>" class="btn btn-secondary btn-sm ml-2">Cancelar</a>
                </div>

                <?php echo form_close(); ?>

            </div><!-- ./ block-body -->
        </div> <!-- ./ block -->
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>