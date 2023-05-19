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
    <div class="col-md-12">
        <?php if ($grupo->id < 3) : ?>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Importante!</h4>
                <p> O grupo <b><?php echo esc($grupo->nome); ?></b> não pode ser editado ou excluído, as permissões para esse grupo não podem ser revogadas.</p>
                <hr>
                <p class="mb-0">Não se preocupe, pois os demais grupos podem ser editados ou removidos conforme a necessidade!</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <div class="user-block block">
            <h5 class="card-title mt-2"><?php echo esc($grupo->nome); ?>
                <a tabindex="0" style="text-decoration: none;" role="button" data-toggle="popover" data-trigger="focus" title="Importante" data-content="O grupo determina o nível de acesso ao <b>sistema!</b>">&nbsp; <i class="fa fa-question-circle fa-lg text-danger"></i></a>
            </h5>
            <p class="card-text">Descrição: <?php echo esc($grupo->descricao); ?></p>
            <p class="card-text">Criado: <?php echo $grupo->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $grupo->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <?php if ($grupo->id > 2) : ?>

                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Ações
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo site_url("grupos/editar/$grupo->id"); ?>">Editar Grupo de Acesso</a>

                        <?php if ($grupo->id > 2) : ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo site_url("grupos/permissoes/$grupo->id"); ?>">Gerenciar Permissões do Grupo</a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("grupos/excluir/$grupo->id"); ?>">Excluir Grupo de Acesso</a>

                    </div>
                </div>

            <?php endif; ?>

            <a href="<?php echo site_url("grupos"); ?>" class="btn btn-secondary btn-sm">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>