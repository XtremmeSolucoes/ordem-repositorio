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
    <div class="col-lg-4">
        <div class="user-block block">
            
            <h5 class="card-title mt-2"><?php echo esc($conta->razao); ?></h5>
            <p class="card-text">CNPJ: <?php echo esc($conta->cnpj); ?></p>
            <p class="card-text">Valor da Conta R$&nbsp;<?php echo number_format($conta->valor_conta, 2); ?></p>
            <p class="contributions mt-0"><?php echo $conta->exibeSituacao(); ?></p>
            <p class="card-text">Criado: <?php echo $conta->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $conta->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("contas/editar/$conta->id"); ?>">Editar Conta</a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?php echo site_url("contas/excluir/$conta->id"); ?>">Excluir Conta</a>

                </div>
            </div>

            <a href="<?php echo site_url("contas"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>