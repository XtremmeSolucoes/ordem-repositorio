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
    <div class="col-lg-3">
        <div class="user-block block">
            

            <h5 class="card-title mt-2">Nome: <?php echo esc($grupo->nome); ?></h5>
            <p class="card-text">Descrição: <?php echo esc($grupo->descricao); ?></p>
            <p class="card-text">Criado: <?php echo $grupo->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $grupo->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("grupos/editar/$grupo->id"); ?>">Editar Grupo de Acesso</a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url("grupos/excluir/$grupo->id"); ?>">Excluir Grupo de Acesso</a>

                </div>
            </div>

            <a href="<?php echo site_url("grupos"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>