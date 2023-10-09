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
        <?php if ($forma->id == 1) : ?>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Importante!</h4>
                <p> A forma de pagamento <b><?php echo esc($forma->nome); ?></b> não pode ser editada ou excluída, pois poderão está associadas as ordens de serviços.</p>
                <hr>
                <p class="mb-0">Não se preocupe, pois os demais Formas de pagamento podem ser editadas ou removidas conforme a necessidade!</p>
            </div>
        <?php endif; ?>
        <?php if ($forma->id == 2) : ?>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Importante!</h4>
                <p> A forma de pagamento <b><?php echo esc($forma->nome); ?></b> não pode ser editada ou excluída, pois estão associadas as ordens de serviços que não geram valor.</p>
                <hr>
                <p class="mb-0">Não se preocupe, pois os demais Formas de pagamento podem ser editadas ou removidas conforme a necessidade!</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <div class="user-block block">
            <h5 class="card-title mt-2"><?php echo esc($forma->nome); ?></h5>
            <p class="contributions mt-0"><?php echo $forma->exibeSituacao(); ?></p>
            <p class="card-text"><?php echo esc($forma->descricao); ?></p>
            <p class="card-text">Criado: <?php echo $forma->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $forma->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <?php if ($forma->id > 2) : ?>

                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Ações
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo site_url("formas/editar/$forma->id"); ?>">Editar Forma de Pagamento</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url("formas/excluir/$forma->id"); ?>">Excluir Forma de Pagamento</a>

                    </div>
                </div>

            <?php endif; ?>

            <a href="<?php echo site_url("formas"); ?>" class="btn btn-secondary btn-sm">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>