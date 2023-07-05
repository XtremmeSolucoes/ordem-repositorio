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
            <div class="text-center">
                <?php if ($item->imagem == null) : ?>

                    <img src="<?php echo site_url('recursos/img/item_sem_imagem.png'); ?>" class="card-img-top" style="width: 90%;" alt="Item sem imagem">

                <?php else : ?>

                    <img src="<?php echo site_url("itens/imagem/$item->imagem"); ?>" class="card-img-top" style="width: 90%;" alt="<?php echo esc($item->nome); ?>">

                <?php endif; ?>

                <a href="<?php echo site_url("itens/editarimagem/$item->id"); ?>" class="btn btn-outline-primary btn-sm mt-3">Alterar Imagem</a>

            </div>

            <hr class="border-secondary">

            <h5 class="card-title mt-2">Nome: <?php echo esc($item->nome); ?></h5>
            <p class="contributions mt-0"><?php echo $item->exibeTipo(); ?></p>       
            <p class="contributions mt-0"><?php echo $item->exibeSituacao(); ?></p>
            <p class="contributions mt-0">Estoque: <?php echo $item->exibeEstoque(); ?></p>
            <p class="contributions mt-0">
                <a class="btn btn-sm" href="<?php echo site_url("itens/codigobarras/$item->id")?>" target="_blank">Ver código de Barras do Item</a>
            </p>
            <p class="card-text">Criado: <?php echo $item->criado_em->humanize(); ?></p>
            <p class="card-text">Atualizado: <?php echo $item->atualizado_em->humanize(); ?></p>

            <!-- Example single danger button -->
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo site_url("itens/editar/$item->id"); ?>">Editar Item</a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?php echo site_url("itens/excluir/$item->id"); ?>">Excluir Item</a>

                </div>
            </div>

            <a href="<?php echo site_url("itens"); ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>

        </div> <!-- ./ block -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- aqui os scripts da view  -->
<?= $this->endSection() ?>